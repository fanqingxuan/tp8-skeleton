<?php

require_once 'support/ValueObject.php';

// 创建一个测试类
class TestCustomClassError extends support\ValueObject {
    /** @var \app\vo\Book[] */
    public $books;
}

// 测试数据
$data = [
    'books' => 'aa,bb'
];

echo "测试数据: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";

// 测试 isCustomClass 方法
$testInstance = new TestCustomClassError();
$reflection = new ReflectionClass($testInstance);
$method = $reflection->getMethod('isCustomClass');
$method->setAccessible(true);

echo "isCustomClass('\\app\\vo\\Book'): " . ($method->invoke($testInstance, '\\app\\vo\\Book') ? 'true' : 'false') . "\n";
echo "class_exists('\\app\\vo\\Book'): " . (class_exists('\\app\\vo\\Book') ? 'true' : 'false') . "\n";

try {
    // 创建对象
    $test = new TestCustomClassError($data);
    echo "转换成功\n";
    echo "books 类型: " . gettype($test->books) . "\n";
    if (is_array($test->books)) {
        echo "books 内容: " . json_encode($test->books, JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
} 