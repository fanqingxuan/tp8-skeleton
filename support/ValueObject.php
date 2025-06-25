<?php
namespace support;

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
        $this->initialize($data);
    }

    public function initialize(array $data = []) {
        $this->rawData = $data;
        $this->fill($data);
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
     * 处理嵌套对象
     * @param mixed $value
     * @param \ReflectionProperty $property
     * @return mixed
     */
    protected function handleNestedObject($value, $property) {
        // 优先使用 PHP 8 属性类型声明
        $type = $this->getPropertyType($property);
        
        if ($type) {
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
        
        // 尝试直接使用类名
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
            if ($this->isCustomClass($elementType)) {
                // 自定义类型：创建对象
                if (is_array($item)) {
                    $obj = $this->createNestedObject($item, $elementType);
                    $result[] = $obj;
                } else {
                    $result[] = $item;
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
        return json_last_error() === JSON_ERROR_NONE;
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
                    return is_array($value) ? $value : (empty($value) ? [] : [$value]);
                case 'string':
                    return is_string($value) ? trim($value) : (string) $value;
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