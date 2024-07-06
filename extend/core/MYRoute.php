<?php

namespace extend\core;

use think\App;
use think\Route as ThinkRoute;
use think\route\RuleItem;

class MYRoute extends ThinkRoute {

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 注册路由规则
     * @access public
     * @param string $rule   路由规则
     * @param mixed  $route  路由地址
     * @param string $method 请求类型
     * @return RuleItem
     */
    public function rule(string $rule, $route = null, string $method = '*'): RuleItem
    {
        if(is_array($route)) {
            $route = implode('@',$route);
        }
        return $this->group->addRule($rule, $route, $method);
    }
}