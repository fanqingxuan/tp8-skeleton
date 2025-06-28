<?php

namespace support;

use think\Model as ThinkModel;

/**
 * @method static void onAfterRead(Model $model) after_read事件定义
 * @method static mixed onBeforeInsert(Model $model) before_insert事件定义
 * @method static void onAfterInsert(Model $model) after_insert事件定义
 * @method static mixed onBeforeUpdate(Model $model) before_update事件定义
 * @method static void onAfterUpdate(Model $model) after_update事件定义
 * @method static mixed onBeforeWrite(Model $model) before_write事件定义
 * @method static void onAfterWrite(Model $model) after_write事件定义
 * @method static mixed onBeforeDelete(Model $model) before_write事件定义
 * @method static void onAfterDelete(Model $model) after_delete事件定义
 * @method static void onBeforeRestore(Model $model) before_restore事件定义
 * @method static void onAfterRestore(Model $model) after_restore事件定义
 * @method static $this scope(string|array $scope) static 查询范围
 * @method static $this where(mixed $field, string $op = null, mixed $condition = null) static 查询条件
 * @method static $this whereRaw(string $where, array $bind = [], string $logic = ‘AND’) static 表达式查询
 * @method static $this whereExp(string $field, string $condition, array $bind = [], string $logic = ‘AND’) static 字段表达式查询
 * @method static $this when(mixed $condition, mixed $query, mixed $otherwise = null) static 条件查询
 * @method static $this join(mixed $join, mixed $condition = null, string $type = ‘INNER’, array $bind = []) static JOIN查询
 * @method static $this view(mixed $join, mixed $field = null, mixed $on = null, string $type = ‘INNER’) static 视图查询
 * @method static $this with(mixed $with, callable $callback = null) static 关联预载入
 * @method static $this count(string $field = ‘*’) static Count统计查询
 * @method static $this min(string $field, bool $force = true) static Min统计查询
 * @method static $this max(string $field, bool $force = true) static Max统计查询
 * @method static $this sum(string $field) static SUM统计查询
 * @method static $this avg(string $field) static Avg统计查询
 * @method static $this field(mixed $field, boolean $except = false, string $tableName = ‘’, string $prefix = ‘’, string $alias = ‘’) static 指定查询字段
 * @method static $this fieldRaw(string $field) static 指定查询字段
 * @method static $this union(mixed $union, boolean $all = false) static UNION查询
 * @method static $this limit(mixed $offset, integer $length = null) static 查询LIMIT
 * @method static $this order(mixed $field, string $order = null) static 查询ORDER
 * @method static $this orderRaw(string $field, array $bind = []) static 查询ORDER
 * @method static $this cache(mixed $key = null, integer|\DateTime $expire = null, string $tag = null) static 设置查询缓存
 * @method static mixed value(string $field, mixed $default = null) static 获取某个字段的值
 * @method static array column(string $field, string $key = ‘’) static 获取某个列的值
 * @method static $this find(mixed $data = null) static 查询单个记录
 * @method static $this findOrFail(mixed $data = null) 查询单个记录
 * @method static \think\Model\Collection|$this[] select(mixed $data = null) static 查询多个记录
 * @method static $this get(mixed $data = null, mixed $with = [], bool $cache = false, bool $failException = false) static 查询单个记录 支持关联预载入
 * @method static $this getOrFail(mixed $data = null, mixed $with = [], bool $cache = false) static 查询单个记录 不存在则抛出异常
 * @method static $this findOrEmpty(mixed $data = null) static 查询单个记录 不存在则返回空模型
 * @method static Collection|$this[] all(mixed $data = null, mixed $with = [], bool $cache = false) static 查询多个记录 支持关联预载入
 * @method static $this withAttr(array $name, \Closure $closure = null) static 动态定义获取器
 * @method static $this withJoin(string|array $with, string $joinType = ‘’) static
 * @method static $this withCount(string|array $relation, bool $subQuery = true) static 关联统计
 * @method static $this withSum(string|array $relation, string $field, bool $subQuery = true) static 关联SUM统计
 * @method static $this withMax(string|array $relation, string $field, bool $subQuery = true) static 关联MAX统计
 * @method static $this withMin(string|array $relation, string $field, bool $subQuery = true) static 关联Min统计
 * @method static $this withAvg(string|array $relation, string $field, bool $subQuery = true) static 关联Avg统计
 * @method Paginator|$this paginate(int|array $listRows = null, int|bool $simple = false, array $config = []) static 分页
 */
abstract class Model extends ThinkModel {

}