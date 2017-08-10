#mvc小框架
* 在app文件中分层
* 分index和admin, 分别表示前台的和后代的
* 在index和admin中, 分MVC

#### MVC使用规则: 

* Controller的方式:
  * 声明命名空间:  'namespace index\controller;', 
  * 并且继承Controller: 比如 class ArticleContorller extends Controller {}
* Model层:
  *  声明命名空间:  'namespace admin\model;'
  *  引入Model类:  'use framework\Model;'
  *  继承Model类:  比如: class ArticleModel extends Model {}
* View层:
  * 创建根据index里的Contorller里的控制器除Contorller的名字: 比如indexContorller对应的文件夹名字是index

#### 路由规则:

* url第一个参数:   index还是admin
* 第二个参数:  表示在哪个控制器
* 第三个参数:  是控制器的哪个方法
* 比如 `www.test.com?l=index&c=index&a=user` 表示admin后台的index控制器的user方法,  l=index也可以省略, 如果不写l默认是index

#### 各个文件的作用: 

* app mvc业务逻辑
* cache 缓存文件
* config  公共文件
* open  定义了入口文件的规则, mvc规则
* public  放图片, css, js等
* verdor  放一些自己封装的类
  * 图片处理类, 放大缩小, 加水印
  * Model类, 自动根据名字连接数据库表
  * page类, 分页类
  * tmp类, 模板类
  * upload类, 文件上传类
  * verify类,  验证码类

#### 仓促间可能写的不够详细

