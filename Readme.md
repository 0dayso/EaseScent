《开发必读》

2012.11.19 更新>>>
增加项目间API调用

2012.11.12 更新>>>

** 框架开发思路
Admin项目负责权限控制、角色、角色组管理、管理员个人信息管理、整站推荐区块管理等;
Admin项目与框架内其他项目通过接口(HTTP POST API)的方式进行通信(会造成额外请求开销,但能保证项目之间的独立性);
关于前台展示：整站静态化,动态页面通过Ajax调用接口的方式进行；
每个项目生成的HTML文件 => ./HTML/项目名称/ 可根据需求将指定目录绑定域名
需要用到的CSS,JS,Images等静态文件 => ./Public/项目名称 (可分类单独域名绑定 static.wine.cn)
静态文件 => ./Uploads/项目名称/
每个项目必须存在一个Auth控制器,用来设置该项目编辑权限
每个项目必须存在一个Api控制器并且继承Core/Lib/Common/中Api类,用来项目间数据通信,项目间通过Api()函数进行通信,规定以JSON格式传输数据

2012.10.08 更新>>>

** 目录说明：
Core 存放基础公共文件以及框架文件
Modules 存放项目文件
Public 存放静态文件
Uploads 存放上传文件
其中：
Core/Common 存放公共基础函数库 可在配置文件中定义 'LOAD_EXT_FUNC' 参数，例如 "LOAD_EXT_FUNC" => 'common'
Core/Conf 存放公共基础配置文件 通过'LOAD_EXT_CONF'参数引用
Core/Lib 存放基础类库 通过'LOAD_EXT_LIB'参数引用

** 添加新项目说明
首先需修改
Core/Conf/globals.php中$globals['modules_allow']数组，在其中加入项目名称；
通过Url访问http://domain/index.php?app=你的项目名,TP框架会自动在Modules目录中生成项目目录；
其他按照TP规范开发即可；

** 代码编写规范
为了代码具有更好的可读性，希望大家按照TP手册1.7章节的规范编写自己的项目代码；
尽量采用MVC-ORM方式编写自己的代码；

** 项目中可用常量
CODE_RUNTIME_PATH 程序物理路径
DS 程序目录分隔符
APP_PATH 项目物理路径
APP_NAME 项目名称

2013.08.23 更新>>>
迁移版本库到Gitlab.wine.cn/mengfk/wine-cn.git

** 其他
框架版本：ThinkPHP 3.1
API手册：http://thinkphp.cn/api/
在线文档手册：http://doc.thinkphp.cn/manual/

** 框架修改记录
TP/Lib/Core/Action.class.php 中修改第124行,增加第二个参数;
