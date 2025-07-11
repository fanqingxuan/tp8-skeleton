<?php
namespace support;

use support\exception\EnumValueException;

// 新增自定义异常，放在类外部

abstract class ValueObject {
    
    /**
     * 原始数据
     * @var array
     */
    protected $rawData = [];
    
    /**
     * 构造函数
     * @param array $data 请求数据
     */
    public function __construct(array $data = []) {
        $this->rawData = $data;
        $this->fill($data);
        $this->initialize($data);
    }

    public function initialize(array $data = []) {
        
    }
    
    /**
     * 填充数据到对象属性
     * @param array $data
     */
    protected function fill($data) {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            
            // 获取字段映射名称
            $fieldName = $this->getFieldName($property);
            
            // 优先使用映射名称，如果不存在则使用属性名
            $dataKey = null;
            $value = null;
            
            if ($fieldName && isset($data[$fieldName])) {
                $dataKey = $fieldName;
                $value = $data[$fieldName];
            } elseif (isset($data[$propertyName])) {
                $dataKey = $propertyName;
                $value = $data[$propertyName];
            }
            
            if ($dataKey !== null) {
                // 检查是否需要创建嵌套对象
                $value = $this->handleNestedObject($value, $property);
                
                // 对基础类型进行转换
                $value = $this->castValue($value, $property);
                
                $this->$propertyName = $value;
            }
        }
    }
    
    /**
     * 获取属性类型（优先使用 PHP 8 类型声明，然后回退到 @var 注释）
     * @param \ReflectionProperty $property
     * @return string|null
     */
    protected function getPropertyType($property) {
        // 优先使用 PHP 8 属性类型声明
        if (method_exists($property, 'getType')) {
            $type = $property->getType();
            if ($type) {
                // 处理联合类型
                if ($type instanceof \ReflectionUnionType) {
                    $types = $type->getTypes();
                    // 返回第一个非空类型
                    foreach ($types as $unionType) {
                        if ($unionType->getName() !== 'null') {
                            return $unionType->getName();
                        }
                    }
                    return null;
                }
                
                // 处理可空类型
                if ($type instanceof \ReflectionNamedType) {
                    $typeName = $type->getName();
                    // 如果是可空类型且值为 null，返回 null
                    if ($type->allowsNull() && $typeName === 'null') {
                        return null;
                    }
                    return $typeName;
                }
            }
        }
        
        // 回退到 @var 注释
        $docComment = $property->getDocComment();
        if ($docComment) {
            if (preg_match('/@var\s+([^\s\*]+)/', $docComment, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }
    
    /**
     * 判断类型是否为枚举
     * @param string $type
     * @return bool
     */
    protected function isEnumType($type) {
        return class_exists($type) && enum_exists($type);
    }
    
    /**
     * 处理嵌套对象
     * @param mixed $value
     * @param \ReflectionProperty $property
     * @return mixed
     */
    protected function handleNestedObject($value, $property) {
        // 优先使用 PHP 8 属性类型声明
        $type = $this->getPropertyType($property);
        
        if ($type) {
            // 新增：枚举类型处理
            if ($this->isEnumType($type)) {
                // 如果是枚举类型且传入的是数组，但类型声明不是数组，取第一个元素
                if (is_array($value)) {
                    if (empty($value)) {
                        return null;
                    }
                    if (count($value) > 1) {
                        throw new EnumValueException("属性类型为单个枚举 {$type}，但传入了多个值: " . implode(',', $value));
                    }
                    $value = $value[0]; // 取第一个元素
                }
                // 如果是枚举类型且传入的是标量，则自动转换
                if (is_scalar($value) && !($value instanceof $type)) {
                    if (method_exists($type, 'tryFrom')) {
                        $enum = $type::tryFrom($value);
                        if ($enum === null) {
                            throw new EnumValueException("无效的枚举值: {$value} for {$type}");
                        }
                        return $enum;
                    } elseif (method_exists($type, 'from')) {
                        try {
                            return $type::from($value);
                        } catch (\ValueError $e) {
                            throw new EnumValueException("无效的枚举值: {$value} for {$type}", 0, $e);
                        }
                    }
                }
                // 已经是枚举对象，直接返回
                return $value;
            }
            // 检查是否是数组类型：ClassName[] 或 array<ClassName>
            if (preg_match('/^(.+)\[\]$/', $type, $arrayMatches)) {
                // 处理 ClassName[] 格式
                $elementType = $arrayMatches[1];
                $arrayData = $this->convertToArray($value);
                return $this->createTypedArray($arrayData, $elementType);
            } elseif (preg_match('/^array<(.+)>$/', $type, $arrayMatches)) {
                // 处理 array<type> 或 array<keyType,valueType> 格式
                $typeParams = $arrayMatches[1];
                
                // 检查是否是关联数组格式：array<keyType,valueType>
                if (strpos($typeParams, ',') !== false) {
                    $types = array_map('trim', explode(',', $typeParams));
                    if (count($types) === 2) {
                        // 关联数组：array<keyType,valueType>
                        $arrayData = $this->convertToArray($value);
                        return $this->createAssociativeArray($arrayData, $types[0], $types[1]);
                    }
                }
                
                // 普通数组：array<type>
                $arrayData = $this->convertToArray($value);
                return $this->createTypedArray($arrayData, $typeParams);
            } else {
                // 检查是否是自定义类型（嵌套对象）
                if ($this->isCustomClass($type)) {
                    // 如果是JSON字符串，先解析
                    if (is_string($value) && $this->isJsonString($value)) {
                        $value = json_decode($value, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // JSON解析失败，返回原值
                            return $value;
                        }
                    }
                    
                    if (is_array($value)) {
                        return $this->createNestedObject($value, $type);
                    }
                }
            }
        }
        return $value;
    }
    
    /**
     * 将各种格式的值转换为数组
     * @param mixed $value
     * @return array
     */
    protected function convertToArray($value) {
        // 如果已经是数组，直接返回
        if (is_array($value)) {
            return $value;
        }
        
        // 如果是JSON字符串，先解析
        if (is_string($value) && $this->isJsonString($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        
        // 如果是字符串，尝试按逗号分割
        if (is_string($value)) {
            $value = trim($value);
            
            // 空字符串返回空数组
            if ($value === '') {
                return [];
            }
            
            // 检查是否包含逗号，如果包含则分割
            if (strpos($value, ',') !== false) {
                return array_map('trim', explode(',', $value));
            }
            
            // 单个值包装成数组
            return [$value];
        }
        
        // null 或空值返回空数组
        if ($value === null || $value === '') {
            return [];
        }
        
        // 其他类型包装成数组
        return [$value];
    }

    /**
     * 创建嵌套对象
     * @param array $data
     * @param string $className
     * @return object|null
     */
    protected function createNestedObject($data, $className) {
        // 尝试在当前命名空间查找类
        if (class_exists($className)) {
            return new $className($data);
        }
        
        return $data;
    }
    
    /**
     * 创建类型化数组（支持基础类型和对象类型）
     * @param array $dataArray
     * @param string $elementType
     * @return array
     */
    protected function createTypedArray($dataArray, $elementType) {
        $result = [];
        foreach ($dataArray as $item) {
            if ($this->isEnumType($elementType)) {
                // 自动转换为枚举对象
                if (is_scalar($item) && !($item instanceof $elementType)) {
                    if (method_exists($elementType, 'tryFrom')) {
                        $enum = $elementType::tryFrom($item);
                        if ($enum === null) {
                            throw new EnumValueException("无效的枚举值: {$item} for {$elementType}");
                        }
                        $result[] = $enum;
                    } elseif (method_exists($elementType, 'from')) {
                        try {
                            $result[] = $elementType::from($item);
                        } catch (\ValueError $e) {
                            throw new EnumValueException("无效的枚举值: {$item} for {$elementType}", 0, $e);
                        }
                    }
                } else {
                    $result[] = $item;
                }
            } elseif ($this->isCustomClass($elementType)) {
                // 自定义类型：创建对象
                if (is_array($item)) {
                    $obj = $this->createNestedObject($item, $elementType);
                    $result[] = $obj;
                } else {
                    // 如果不是数组，抛出异常
                    throw new \InvalidArgumentException("类型声明为 {$elementType}，但传入的数据不是数组: " . json_encode($item, JSON_UNESCAPED_UNICODE));
                }
            } else {
                // 基础类型：进行类型转换
                $result[] = $this->convertBasicType($item, $elementType);
            }
        }
        return $result;
    }

    /**
     * 创建关联数组（支持键值类型转换）
     * @param array $dataArray
     * @param string $keyType
     * @param string $valueType
     * @return array
     */
    protected function createAssociativeArray($dataArray, $keyType, $valueType) {
        $result = [];
        
        foreach ($dataArray as $key => $value) {
            // 转换键类型
            $convertedKey = $this->convertBasicType($key, $keyType);
            
            // 转换值类型
            if ($this->isCustomClass($valueType)) {
                // 自定义类型：创建对象
                if (is_array($value)) {
                    $convertedValue = $this->createNestedObject($value, $valueType);
                } else {
                    $convertedValue = $value;
                }
            } else {
                // 基础类型：进行类型转换
                $convertedValue = $this->convertBasicType($value, $valueType);
            }
            
            $result[$convertedKey] = $convertedValue;
        }
        
        return $result;
    }
    
    /**
     * 转换基础类型
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function convertBasicType($value, $type) {
        switch (strtolower($type)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return $this->convertToBool($value);
            case 'string':
                return is_string($value) ? trim($value) : (string) $value;
            default:
                return $value;
        }
    }
    
    /**
     * 转换为布尔值（更直观的转换逻辑）
     * @param mixed $value
     * @return bool
     */
    protected function convertToBool($value) {
        // 如果已经是布尔值，直接返回
        if (is_bool($value)) {
            return $value;
        }
        
        // 处理字符串
        if (is_string($value)) {
            $value = trim(strtolower($value));
            
            // 明确的true值
            if (in_array($value, ['true', '1', 'yes', 'on', 'y'])) {
                return true;
            }
            
            // 明确的false值
            if (in_array($value, ['false', '0', 'no', 'off', 'n', ''])) {
                return false;
            }
            
            // 其他非空字符串视为true
            return !empty($value);
        }
        
        // 处理数字
        if (is_numeric($value)) {
            return (float) $value !== 0.0;
        }
        
        // 处理null
        if ($value === null) {
            return false;
        }
        
        // 处理数组和对象
        if (is_array($value) || is_object($value)) {
            return !empty($value);
        }
        
        // 默认转换
        return (bool) $value;
    }
    
    /**
     * 检查是否是自定义类
     * @param string $type
     * @return bool
     */
    protected function isCustomClass($type) {
        // 排除基础类型
        $basicTypes = ['string', 'int', 'integer', 'float', 'double', 'bool', 'boolean', 'array', 'object', 'mixed'];
        
        if (in_array(strtolower($type), $basicTypes)) {
            return false;
        }
        
        // 检查类是否存在
        return class_exists($type);
    }
    
    /**
     * 检查字符串是否是有效的JSON
     * @param string $string
     * @return bool
     */
    protected function isJsonString($string) {
        if (!is_string($string)) {
            return false;
        }
        
        // 简单的JSON字符串检测
        $string = trim($string);
        if (empty($string)) {
            return false;
        }
        
        // 检查是否以 { 或 [ 开头（对象或数组）
        if (!in_array(substr($string, 0, 1), ['{', '['])) {
            return false;
        }
        
        // 尝试解析JSON
        json_decode($string);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // JSON解析失败，抛出异常
            throw new \InvalidArgumentException("JSON解析失败: " . json_last_error_msg() . "，原始值: " . $string);
        }
        
        return true;
    }
    
    /**
     * 获取字段映射名称
     * @param \ReflectionProperty $property
     * @return string|null
     */
    protected function getFieldName($property) {
        $docComment = $property->getDocComment();
        
        if ($docComment) {
            // 解析 @field 注释，格式：@field field_name
            if (preg_match('/@field\s+([^\s\*]+)/', $docComment, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }
    
    /**
     * 数据类型转换
     * @param mixed $value
     * @param \ReflectionProperty $property
     * @return mixed
     */
    protected function castValue($value, $property) {
        // 如果已经是对象，直接返回
        if (is_object($value)) {
            return $value;
        }
        
        // 使用 getPropertyType 方法获取类型信息
        $type = $this->getPropertyType($property);
        
        if ($type) {
            // 新增：枚举类型处理
            if ($this->isEnumType($type)) {
                if (is_scalar($value) && !($value instanceof $type)) {
                    if (method_exists($type, 'tryFrom')) {
                        $enum = $type::tryFrom($value);
                        if ($enum === null) {
                            throw new EnumValueException("无效的枚举值: {$value} for {$type}");
                        }
                        return $enum;
                    } elseif (method_exists($type, 'from')) {
                        try {
                            return $type::from($value);
                        } catch (\ValueError $e) {
                            throw new EnumValueException("无效的枚举值: {$value} for {$type}", 0, $e);
                        }
                    }
                }
                return $value;
            }
            // 排除数组类型标记：ClassName[] 或 array<ClassName>
            if (preg_match('/^(.+)\[\]$/', $type, $arrayMatches)) {
                return $value; // 数组类型已在 handleNestedObject 中处理
            }
            if (preg_match('/^array<(.+)>$/', $type, $arrayMatches)) {
                return $value; // 数组类型已在 handleNestedObject 中处理
            }
            
            switch (strtolower($type)) {
                case 'int':
                case 'integer':
                    return (int) $value;
                case 'float':
                case 'double':
                    return (float) $value;
                case 'bool':
                case 'boolean':
                    return $this->convertToBool($value);
                case 'array':
                    // 如果传入的是字符串，按逗号拆分
                    if (is_string($value)) {
                        return $this->convertToArray($value);
                    }
                    return is_array($value) ? $value : (empty($value) ? [] : [$value]);
                case 'string':
                    return is_string($value) ? trim($value) : (is_array($value)?implode(',',$value):(string) $value);
            }
        }
        
        // 默认处理字符串trim
        return is_string($value) ? trim($value) : $value;
    }
    
    /**
     * 转换为数组（包括嵌套对象）
     * @return array
     */
    public function toArray() {
        $result = [];
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $value = isset($this->$propertyName) ? $this->$propertyName : null;
            
            if ($value instanceof ValueObject) {
                // 嵌套对象转换为数组
                $result[$propertyName] = $value->toArray();
            } elseif (is_array($value)) {
                // 处理数组中的对象
                $result[$propertyName] = $this->arrayToArray($value);
            } else {
                $result[$propertyName] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * 递归处理数组中的对象
     * @param array $array
     * @return array
     */
    protected function arrayToArray($array) {
        $result = [];
        
        foreach ($array as $key => $item) {
            if ($item instanceof ValueObject) {
                $result[$key] = $item->toArray();
            } elseif (is_array($item)) {
                $result[$key] = $this->arrayToArray($item);
            } else {
                $result[$key] = $item;
            }
        }
        
        return $result;
    }
    
    /**
     * 获取原始数据
     * @return array
     */
    public function getRawData() {
        return $this->rawData;
    }
}