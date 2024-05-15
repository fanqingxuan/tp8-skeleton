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
- transformer/
  
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
    - 增加`ok($data)`方法,方法返回code、message、data三个字段的结构化json响应
    - 增加`error(string $msg, $data = null)`方法,方法返回code、message、data三个字段的结构化json响应
    - 增加了返回固定格式的响应结构的`transformer`层，便于更好的维护代码结构和分层
- 异常
  - 优化了异常日志的记录内容
  - 非debug模式时，返回包含code、message、data三个字段的结构化json结构
- 中间件
  - 提供了中间件`AccessLog`,用于记录access请求日志
  - 去掉了`app/middleware.php`文件
  - 添加了`config/middleware.php`文件，用于声明全局中间件
- 参数结构体
  >支持将请求参数映射到类的属性上，便于约束和维护方法参数，增强可维护性，使用方法如下:

  - 定义request类
  ```php
  namespace app\request;

  use extend\core\Request;
  use think\App;

  class HelloRequest extends Request
  {
      protected const DEFAULT_PARSE_METHOD = ['HEAD','GET'];// 如果定义了的话就从这些方法中解析数据，否则根据请求metho从请求方法中解析

      protected const USE_SNAKE_TO_CAMEL_CASE = true;
      public $myId=55;
      public $host;
      public $cacheControl='status';    

  }
  ```
  需要说明的是,必须继承`extend\core\Request`类;常量`DEFAULT_PARSE_METHOD`用于控制从什么数据源解析数据，当前支持从GET、POST、HEAD中解析数据,如果没有定义或者是空数组，则根据当前请求方式从对应的数据中解析，**注意当前只支持GET、POST**;常量`USE_SNAKE_TO_CAMEL_CASE`用于控制是否将下划线分隔的变量转换为驼峰命名的属性上
  - 使用
```php
class Index extends Controller
{
    public function index(HelloRequest $req)
    {
        dd($req->cacheControl);
    }
}
```
单据看到了控制器参数灌入自定义的request类，然后方法体直接访问属性即可

- 响应结构体
  > 响应结构体是用来约束返回给前端页面的结构，主要目的是减少不必要的字段给前端，提高可维护性，使用方法如下:

  - 定义响应结构体
  ```php
  namespace app\transformer;

  use League\Fractal\TransformerAbstract;

  class UserTransformer extends TransformerAbstract
  {
      public function transform($user)
      {
          return [
              'id' => $user['id'],
              'name' => $user['name'],
          ];
      }
  }
  ```
  注意继承`League\Fractal\TransformerAbstract`类，然后重写`transform`方法，返回一个数组，数组中的key就是返回给前端页面的key，value就是返回给前端页面的value
  - 控制器使用
  ```php
    class Index extends Controller
    {
        public function show() {
            $user = [
                'id'=>1,
                'name'=>'thinkphp',
                'age'=>18,
                'address'=>'beijing',
                'hobby'=>['football','basketball'],
            ];
            return $this->Ok($user,UserItemTransformer::class,false);
        }
    }
  ```
  `$this->Ok($user,UserItemTransformer::class,false);`方法有三个参数，第一个参数是要转换的数据，第二个参数是转换类，第三个参数是区分传入的数据是否二位数组，如果为true，则是二位数组，第三个参数的目的主要是控制用collection还是item转换器; 这里还有一个更灵活的兼容，如果Ok方法只传一个参数，则默认不使用任何转换器

- 其它
  - `php think make:service`用于快速生成业务类
  - `php think make:transformer`用于快速生成响应转换类
  - `php think make:request`用于快速生成参数转换类
  - 优化了`php think make:middleware`生成文件内容
  - 移动了`public/index.php`部分代码到`bootstrap/start.php`
  
## 安装

~~~
git clone git@github.com:fanqingxuan/tp8-skeleton.git

cd tp8-skeleton

composer install
~~~

启动服务

~~~
php think run
~~~

然后就可以在浏览器中访问

~~~
http://localhost:8000
~~~
