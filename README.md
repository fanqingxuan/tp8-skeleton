## 说明
该项目基于thinkphp8.0，对`app`应用层代码进行了优化，便于开箱即用,快速进行业务层的开发，不修改**vendor**依赖的任何内容

## app文件夹结构
>去掉了app根目录下面的php文件，重新按功能整理了子文件夹
- command/
- controller/
- helper/
- middleware/
- model/
- service/
- provider/
  
## 优化清单
- 日志
  - 日志中添加了traceId
  - 分为access日志、error日志、业务日志
  - 业务层日志添加MYLog类
    - MYLog::debug($keyword,$message);
    - MYLog::info($keyword,$message);
    - MYLog::notice($keyword,$message);
    - MYLog::error($keyword,$message);
  ```shell
  // access日志
  2024-05-03 16:37:56 | 0cc68a9d-649d-45c5-877f-2487ae48d1bc | INFO | GET | 500 | 127.0.0.1 | 0.042006s | Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36 | /hello/test

  // 业务日志
  2024-05-03 16:37:56 | 0cc68a9d-649d-45c5-877f-2487ae48d1bc | INFO | info关键字 --> 测试了
  ```
- 服务提供者
  - 服务提供者类由service改造为了provider,`service`文件件夹于存放应用开发中的服务层
  - 服务提供者类在`app/provider`文件夹添加，在`config/provider.php`中引入，会自动加载
  - 提供了 `php think make:provider` 快速创建服务提供者类的命令,新生成文件添加到`app/provider`文件件
`
- 命令行
  - 去掉了`config/console.php`文件
  - 自动加载命令,在`app/command`文件夹添加命令文件后,php think命令查看，命令行终端下面就能看到对应的命令,不需要在`config/console.php`中配置
- 助手函数
  - 去掉了`app/common.php`文件
  - 添加了`app/helper`文件夹，在该文件夹下的php文件会自动引入,可以按照不同的类型添加文件，如str.php、arr.php等
- 控制器
  - 去掉了`app/BaseController.php`文件
  - 添加了`extend/Controller.php`文件
    - 增加`ok($data, string $msg = '操作成功')`方法,方法返回code、message、data三个字段的结构化json响应
    - 增加`error(string $msg, $data = null)`方法,方法返回code、message、data三个字段的结构化json响应
- 异常
  - 优化了异常日志的记录内容
  - 非debug模式时，返回包含code、message、data三个字段的结构化json结构
- 中间件
  - 提供了中间件`AccessLog`,用于记录access请求日志
  - 去掉了`app/middleware.php`文件
  - 添加了`config/middleware.php`文件，用于声明全局中间件
- 其它
  - `php think make:service`用于快速生成业务类
  - 优化了`php think make:middleware`生成文件内容
  - 移动了`public/index.php`部分代码到`bootstrap/start.php`
  
## 安装

~~~
git clone git@github.com:fanqingxuan/tp8-skeleton.git
~~~

启动服务

~~~
cd tp8-skeleton
composer install
php think run
~~~

然后就可以在浏览器中访问

~~~
http://localhost:8000
~~~
