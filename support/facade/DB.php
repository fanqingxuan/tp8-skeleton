<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2025 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace support\facade;

use think\DbManager;
use think\Facade;

/**
 *  * @method static \think\db\Query master() static 从主服务器读取数据
 * @method static \think\db\Query readMaster(bool $all = false) static 后续从主服务器读取数据
 * @method static \think\db\Query table(string $table) static 指定数据表（含前缀）
 * @method static \think\db\Query name(string $name) static 指定数据表（不含前缀）
 * @method static \think\db\Expression raw(string $value) static 使用表达式设置数据
 * @method static \think\db\Query where(mixed $field, string $op = null, mixed $condition = null) static 查询条件
 * @method static \think\db\Query whereRaw(string $where, array $bind = []) static 表达式查询
 * @method static \think\db\Query whereExp(string $field, string $condition, array $bind = []) static 字段表达式查询
 * @method static \think\db\Query when(mixed $condition, mixed $query, mixed $otherwise = null) static 条件查询
 * @method static \think\db\Query join(mixed $join, mixed $condition = null, string $type = ‘INNER’) static JOIN查询
 * @method static \think\db\Query view(mixed $join, mixed $field = null, mixed $on = null, string $type = ‘INNER’) static 视图查询
 * @method static \think\db\Query field(mixed $field, boolean $except = false) static 指定查询字段
 * @method static \think\db\Query fieldRaw(string $field, array $bind = []) static 指定查询字段
 * @method static \think\db\Query union(mixed $union, boolean $all = false) static UNION查询
 * @method static \think\db\Query limit(mixed $offset, integer $length = null) static 查询LIMIT
 * @method static \think\db\Query order(mixed $field, string $order = null) static 查询ORDER
 * @method static \think\db\Query orderRaw(string $field, array $bind = []) static 查询ORDER
 * @method static \think\db\Query cache(mixed $key = null , integer $expire = null) static 设置查询缓存
 * @method static \think\db\Query withAttr(string $name,callable $callback = null) static 使用获取器获取数据
 * @method static mixed value(string $field) static 获取某个字段的值
 * @method static array column(string $field, string $key = ‘’) static 获取某个列的值
 * @method static mixed find(mixed $data = null) static 查询单个记录
 * @method static mixed select(mixed $data = null) static 查询多个记录
 * @method static integer insert(array $data, boolean $replace = false, boolean $getLastInsID = false, string $sequence = null) static 插入一条记录
 * @method static integer insertGetId(array $data, boolean $replace = false, string $sequence = null) static 插入一条记录并返回自增ID
 * @method static integer insertAll(array $dataSet) static 插入多条记录
 * @method static integer update(array $data) static 更新记录
 * @method static integer delete(mixed $data = null) static 删除记录
 * @method static boolean chunk(integer $count, callable $callback, string $column = null) static 分块获取数据
 * @method static \Generator cursor(mixed $data = null) static 使用游标查找记录
 * @method static mixed query(string $sql, array $bind = [], boolean $master = false, bool $pdo = false) static SQL查询
 * @method static integer execute(string $sql, array $bind = [], boolean $fetch = false, boolean $getLastInsID = false, string $sequence = null) static SQL执行
 * @method static \think\Paginator paginate(integer $listRows = 15, mixed $simple = null, array $config = []) static 分页查询
 * @method static mixed transaction(callable $callback) static 执行数据库事务
 * @method static void startTrans() static 启动事务
 * @method static void commit() static 用于非自动提交状态下面的查询提交
 * @method static void rollback() static 事务回滚
 * @method static boolean batchQuery(array $sqlArray) static 批处理执行SQL语句
 * @method static string getLastInsID(string $sequence = null) static 获取最近插入的ID
 * @mixin \think\DbManager
 */
class DB extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）.
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return DbManager::class;
    }
}
