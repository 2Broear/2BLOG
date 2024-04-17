2BLOG THEME
====================================================================================================================================================================
A Simplized Wordpress Blog Theme Design &amp; Developed from [2broear.com](http://blog.2broear.com) by 2BROEAR Released &amp; openSourced in 2022.

__Preview Site__ ：[演示站点](http://wpk.2broear.com) （演示并不代表最终版本，最新请以 [blog.2broear.com](http://blog.2broear.com) 为准）

![2blog_theme](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/2BLOG-screenshot.jpg "theme 2blog")

主题简介
--------------------------------------------------------------------------------------------------------------------------------------------------------------
历经半年之久，__鸽鸽碰碰的 WordPress 主题 2BLOG 他来了！！__ 折腾这么些日子终于算是可以开测了，这里将作为主题开源后续的发布、更新、备份地址。目前主题尚处测试阶段，未上传至 Wordpress。注意：此主题存在一定的定制成分，其中部分内容可能无法适用于部分人群！主题在前静态主题的功能外观基础之上做了部分取舍，
<details>
      <summary>其中主要更新内容包括（点击展开）</summary>
      
- 页面/首页文章置顶
- 基础、图文多级分类元导航、可控的导航图标
- 头部公告单独分离文章（可自定义展示数量）
- 自定义首页 banner 图集、首页图文卡片导航（需手动指定分类slug别名）
- 自定义各数据展示位（自选）调用分类及展示数量
- 自定义头像、背景、主题色、Gravatar 镜像源等
- 自定义 _RSS订阅、SITEMAP地图、站内搜索（可选样式）_ 包含内容
- 自动深色模式时段控制
- 可控的侧栏广告位（Google AdSense）及 Pixiv 排行展示（数量）、最高浏览分类及展示数量
- 底部各项自定义、各图标等信息开启控制
- 支持开启 Valine 评论及调用 Leancloud 应用数据（可单独控制分类页面数据来源）
- 支持 Wordpress Ajax 评论/翻页
- _图片懒加载_
- _视频动态预览_
- _全新文章归档页面（ajax）_
- _可控的随机标签云_
- _可选的文章目录索引_
- _可选页面缓存索引_
- _新增 Twikoo 评论支持_
- _新增漫游影视异步加载（ajax）支持_
- _修复了 Valine 存在的隐私泄漏问题_
- _支持 chatGPT 文章 AI 摘要_
- _支持 Memos 日记切换_
- _支持文章 Marker 多人标记_
- 支持邮件（可选模板）、自建企业微信应用（多选模版）评论推送提醒（Valine 集成 server酱、pushplus及企业微信应用推送）
- 部分页面支持 wordpress 与 leancloud 数据切换
- 部分页面支持使用视频替代 poster 背景
- 部分页面（weblog、acg、download）支持子页并可选是否开启文章页
- ...
- 取消了当前导航下方 slider 滑块
- 部分页面取消了顶部面包屑导航（部分页面仍可启用）
      
</details>

当然了，最重要的还是集成了 __Leancloud 与 Wordpress 之间的数据切换__ ，这个主要是因为之前静态博客使用的是 _valine_ 评论系统（其实之前很少使用 _leancloud_ 数据储存），后面我自己改了很多东西（至于要不要集成到 wp 上只能日后再说了），所以在 _wordpress_ 中仍做了数据切换，然后顺带更新了之前尚未同步数据到 _leancloud_ 的页面。

> 在wordpress中除“公告”外所有数据均以文章形式发布，通过后端函数调用数据，而使用leancloud数据的页面将通过 __`lbms`__ 后台进行数据上传、修改及删除等操作，再通前端过`xhr`异步调用json数据写入。
> 
> ~~__LBMS__ 为个人需求，故无法保证其可用性，__无需使用 leancloud 时请无视其所有相关文档__ ）~~

![2blog_wordpress_theme](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/screenshot.png "theme overview")

主题结构
--------------------------------------------------------------------------------------------------------------------------------------------------------------
主题是职业之余开发完成的，主要是满足个人需求的同时进行开发，其中的部分功能页面可能不适用于所有人（老早之前还鸽了 _HEXO_ 的主题开发，主要是那个文档有点难找），做成 _cms_ 的主要原因是因为之前静态博客的内容多了之后有点不好管理（后面做了个 _markdown_ 的编辑器来发布文章，不过没用就是了），开源呢一方面是因为之前受到了部分博友的认可，都表示有意向这个主题，另一方面正巧公司的框架去年也搬到 _wp_ 了所以整个开发流程是相对顺利，写的功能在主题之间都能互通这一点是很友好的。 

__以下分为 `wp` 及 `lbms` 两个结构简述__ 

### WordPress 后台

wordpress 后台设置分为  __基本信息__ 、 __通用控制__ 、 __页面设置__ 、 __侧栏设置__ 、 __页尾控制__  5 个版面，每个版面对应不同的设置选项，_每个选项下方都有相应的功能使用说明，一般情况下只需要对应其提示操作即可。_（其他操作说明将在下方 __文档说明__ 中补充，如分类、页面、文章中的设置细节等等）

1. ___基本信息___ 只中有 5 个选项，可以修改个人昵称（注意非博客名称）、头像及卡片背景图，包括全站的描述及关键词（单页分类的关键词及各项配置需在 __文章->分类__ 中单独配置）

2. ___通用控制___ 中所拥有控制选项是整个主题最多也是最复杂的，主要包含 __主题颜色__ 、 __LOGO__ 、 __公告__ 、 __元导航__ 、 __面包屑导航__ 、 __Gravatar头像__ 、 __sitemap__ 、 __rss feed__ 、 __搜索结果/样式__ 、 __暗黑模式__ 、 __Leancloud 数据存储__ 、 __第三方 valine 评论__ 、 __评论邮件/微信提醒__ 、 __站点静态文件CDN__ 等等，注意所有涉及邮件收发的选项均需填写 __SMTP 发件服务配置__ 并测试成功之后才能正常使用。

      > 启用 leancloud 设置需要到 leancloud.cn 控制台 __创建应用__ ，然后在 __设置->域名绑定__ 中设置 __API 访问域名__ （二级域名国内需要备案），之后再将博客域名添加到 __设置->安全中心__ 的 __Web 安全域名__ 中用以开启调用API数据，再之后在 __数据储存->结构化数据__ 中创建名称为 __*wp分类模板名称__ （如 __`category-news.php`__ 模板名称为 __`news`__ ）的 `Class` ，最后将 __设置->应用凭证__ 中的 `appid、appkey、serverurl（rest api）`填入 _wp_ 后台对应选项保存即可。
      > 
      > ### __Valine__ 配置流程大同小异 [快速开始](https://valine.js.org/quickstart.html)
      > 
      > 开启 __评论微信提醒__ 功能后需要 __注册企业微信__ 登录完善信息后在 __应用管理->自建__ 中 __创建应用__ ，创建应用完成后，在 __我的企业__ 选项卡中获取 __`企业ID`__ ，之后在 __应用管理->自建__ 中找到刚刚创建的应用，点进去可找到 __`AgentId`__ 和 __`Secret`__ 。
      > 
      > 企业应用配置完成，将 `企业id`、`AgentId`、`Secret` 填入后台对应值即可。注意：开启后需使用微信扫描企业微信中 __我的企业->微信插件__ 栏目中的 __邀请关注__ 栏目二维码后才能收到通知！此外，在 __微信推送消息类型__ 选项中，  _模板卡片_  仅能在 企业微信app 中收到消息推送__ ，微信端暂不支持接收该消息类型。

3. ___页面设置___ 选项，此选项卡内有很多项都是 __下拉选项__ 形式的控制组件，用于选择展示在各页面、位置、类型的已创建的分类选项（目前仅支持一级分类），其余的则是各页面展示 __背景图、背景视频、banner__ 等控件。

4. ___侧栏设置___ 选项，此选项卡所有应用仅应用于 __文章资讯__ 页面的右侧，支持 __Google Adsense__ 广告块，默认开启来自 mokeyjay 超能小紫的 __Pixiv每日排行榜小挂件__ ，及自定义展示 __热门文章__ 分类下拉控制。

5. ___页尾控制___ 选项，页面底部有一些文章、评论及联系方式的展示设置，还包括各支持图标展示设置及站外（沟通）插件控制，左侧主要有 __近期文章 和 近期评论__ 选项卡，_所有选项均按选项下方提示操作即可。_ 

![2blog_theme_setting](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/basic.png "2blog basiclly set")

### LBMS 后台（选读）
<details>
      <summary>展开内容</summary>
      
lbms 后台将在 __`通用控制`__ 中的 _leancloud_ 选项开启后自动创建 `lbms` 及 `lbms-login` 页面（ ___默认创建页面类型为 `私密` ，仅可在已登录 wp 站点账号后的环境下访问___ ）。在 leancloud 创建应用之后，可通过 _leancloud_ 或 `/bms-login` 页面创建账号（若注册账号邮件验证不及时，请前往 leancloud 后台对应应用中的 __数据储存->结构化数据->User__ 表中手动设置账号的 __`emailVerified`__ 为 __`true`__ 即可正常登录）

-  ~~news 栏目已废弃，~~ 默认原生 _wordpress_ 数据
- __weblog__ 对应主题模板中的 __`category-weblog.php`__ 日记日志模板，需创建 __`weblog`__ Class
-  __acg__ 对应主题模板中的 __`category-acg.php`__ 漫游影视模板，需创建 __`acg`__ Class
-  __link__ 对应主题模板中的 __`category-2bfriends.php`__ 友情链接模板，需创建 ___`link`___ Class（ __注意此项使用 `link`__ ）
-  __download__ 对应主题模板中的 __`category-download.php`__ 资源下载模板，需创建 __`download`__ Class
-  __inform__ 对应主题模板中的 __`category-weblog.php`__ 公告模板，需创建 __`inform`__ Class

> __`news`__ 选项卡虽已不再使用，但其中的 _markdown_ 功能仍可以正常使用且支持同步预览。

![2blog_lbms_ui](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/edit.png "lbms UI")

</details>

文档说明
====================================================================================================================================================================
### 视频流程
[主题设置流程](https://www.bilibili.com/video/BV1ig411C7FH)

### 安装说明
所有步骤和普通 _wordpress_ 主题安装无异（测试环境为最新版的 __WordPress 6.0__ ），在 __外观->主题->上传主题->选择.zip压缩包__ 启用即可。

> 主题安装并启用后即可正常访问，不过其中部分数据需要在后台点击 ___2BLOG主题设置___ 后以初始化预设

### 分类导航
在 __文章->分类__ 中的下方有一组名为 __`Page Sync Options`__ 的选项，里面有各分类同步页面的自定义属性： 

- __Background Images__ （分类背景）
- __Page Template__ （分类绑定的页面模板）
- __Page Title__ （分类/页面自定义标题）
- __Page Keywords__ （分类/页面自定义关键词）
- __Page Description__ （分类/页面自定义描述）

其中 ___分类背景___ 用于后台开启 __元分类导航__ 后所应用的背景图， ___页面模板___ 则为创建分类后所同步的页面模板（创建分类后会自动新建相同名称、别名、模板的页面） _这里隐藏了一个  __Category Order__ 选项，也就是 __页面__ 中的 `menu_order` 用于导航排序。在默认创建分类时会自动将其 `term_id` 同步到所创建应页面的 `menu_order`，__在分类创建后单独编辑时可查看和修改其 `Category Order` 导航排序（规则默认值越小越靠前，适用于所有层级，建议不要更改 页面 中的 `menu_order` 选项，以方便查看页面所绑定的同步分类id）__ ，剩余的 __title__ 、 __keywords__ 、 __description__ 则为页面标题、关键词及描述，一般用于 seo 选项。

> ___Tricks：___ 分类自带的 __描述__ 选项可在某些存在背景图的页面中作为 ___页面小标题___ 使用。当分类设置为 `uncategorized` 即 __未分类__ 时，该分类不会被输出到导航栏目。
> 
> __额外属性：__ 当分类别名设置为 __`slash`__ 时，其创建的分类将使用 `/` 作为分类导航别名，其 _permalink_ 将为 `javascript:;` __（即无导航，此时访问该导航下的分类时将直接作为其父级的层级显示页面）__ （特殊需求，可无视）

~~注意，在后台 __通用控制->页面层级关系__ 中可以控制是否同步分类层级到页面层级（这样更方便查看和编辑页面），此选项因其开启后将会导致无法正常使用 ___`slash`___ 关键字的原因所以  __默认关闭，如果没有使用 `slash` 将子级作为父级输出的需求，则可以开启__~~ 

__因bug暂未修复故暂停上述👆选项，需启用 `slash` “/” 目录下子级分类评论时请手动在 _页面_ 定义其分类父级__

![2blog_theme_setting](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/category.png "2blog category set")

### 分类/页面、文章模板

1. 分类/页面模板在导航被创建时定义（`category-*.php`），__所有子级分类均继承父级模板，在创建分类时可手动选择后自动同步。__
2. 文章模板分为 `news` 和 `notes` ，默认模板为通用文章类型 `single-notes.php` （__请注意：__ 一般情况下发布文章仅需选择对应分类即可自动匹配其分类文章模板（如 `category-news.php` 与 `single-news.php` 则为模板一致，此时无需指定文章模板），但当 ___分类模板名称（category-*.php）___ 与 ___文章模板名称（single-*.php）___ 不相同时，__则需在发布时手动选择文章模板__

### 友情链接
在 __链接__ 栏目中，所有选项都是官方默认的，所以只需要注意几个单独的点设置即可。首先，需要设置以友链分类：

- `standard` __默认链接__（头像、名称、描述）
- `technical` __技术链接__（头像、名称）
- `special` __特殊链接__（名称、描述）
- `missing` __失联链接__（显示名称、在 _特殊友链_ 下方显示，可包含单项链接）
- `sitelink` __全站链接__（显示名称、在全站页面底部显示）

以上分类名称将作为友情链接分类判断的依据（分类别名可随意变更），通过设置 __将这个链接设为私密链接__ 来改变链接状态为 `standby` 即某个链接无法访问时可应用特殊样式（适用于 `missing` 类型）
> 更改排序或版式结构请在 `functions.php` 文件中自定义 `the_site_link()` 函数。  
> 
> __额外属性：__ 通过设置链接中的 __评级__ 属性为 `1`或`10` 时链接右下角将被标记为 ___`girl`___ （♀标识）；通过设置 __备注__ 属性来显示链接左上角的标签（默认 `灰色` 标签，当 __评级__ 属性 `>=9` 时将显示 `绿色` 标签）

### 下载页面
<details>
      <summary>展开内容（~~）</summary>
      
此模板一般不建议使用，该瀑布流结构分为 3 栏，需要在下载模板分类的子分类中自定义 `category_order` 为特定值：1、2、3 来区分某个子分类展现在某一列中，（已知的问题有排序功能依赖 `category_order` 故一列中存在多个子分类时，相同的 `category_order` 会导致排序混乱，所以不建议使用 ）

</details>

### 其他设置

#### 页面内容
除文章、笔记模板外其他页面基本都可以在 __页面__ 中自定义内容，内容将展示在 `commnets` 评论之前（如关于、留言板、隐私等模板的自定义内容均在留言之前展示）

#### 文章排序
~~WP自带的 __“置顶这篇文章”__ 将作为 ___置顶到首页___ 使用，~~（wp自带的文章置顶暂时搁置）一般情况下文章使用 `排序（列表）排序值` 进行排序（最后编辑排序值>排序值>发布日期），值越大越靠前。

#### 评论调整
WordPress 评论在后台 __设置->讨论__ 中可设置 ___评论数量、分页、嵌套、限制等___ （邮件SMTP收发模块在 `functions.php` 中可修改，收发控制在 __2BLOG 主题设置__ 后台中更改）

Valine 评论在 `footer.php` 中 __初始化__ 各项原生配置及自定义选项（微信评论通知代码在 valine.js 中检索 `custom_initfield_wxnotify` 即可定位修改）

> Valine 邮件收发配置需到 leancloud.cn 控制台使用云引擎部署 `https://github.com/2broear-xty/valine-admins.git` ，[zhaojun1998 分支](https://github.com/zhaojun1998/Valine-Admin)  其中收发邮件模板在 __template -> default__ 目录下，分别为 `send.ejs` 与 `notice.ejs` 
> 
> 部署前请先查看 Valine-Admin 教程：[https://github.com/zhaojun1998/Valine-Admin](https://github.com/zhaojun1998/Valine-Admin/blob/master/README.md)
> 
> __注意使用 `https://github.com/2broear-xty/valine-admins.git` 地址进行部署，__ zhaojun1998 的分支尚未更新 `package.json` 中的 `NodeJs` 版本号（leancloud 目前不再支持 node.js 6.* 版本，已 pull request，因为要修改模板中的一些东西所以暂时下载到仓库临时使用，最好自己fork源项目后自定义模板等内容，否则无法不保证可用性） 

CDN 静态文件加速
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
<details>
      <summary>展开内容</summary>
      
1：cdn加速依赖于各方平台，此处演示为 __腾讯云__ cdn 静态文件加速，登录控制台并参考对应教程配置好对应的加速域名（如：`https://img.example.com`）后进行下一步。
2：在 2blog 后台开启 cdn 并填写上方配置的加速域名后，此时文章内图片将以该域名为链接头替换wp路径（加速文件访问路径取决于 `nginx` 配置），并自动关闭 wp 原生 `srcset` 图片属性，如需加速站内 `images` 目录下的预设图片，需将其复制到 nginx 配置中的路径（uploads）即可。

___参考 nginx 配置：___（此处设置加速路径为 wp 目录下的 `uploads` 路径）
``` nginx
# 图片资源
server {
    listen 80;
    listen 443;
    server_name ***.example.com;
    location / {
      root /www/wwwroot/example.com/wp-content/uploads;
   }
}
# 文件资源
server {
    listen 80;
    listen 443;
    server_name ***.example.com;
    location / {
      root /www/wwwroot/example.com/wp-content/2BLOG-main;  # 注意此处路径
   }
}
```
配置完成后，访问 子域名+uploads 目录下的文件即可。__图片资源__ 默认目录储存结构为：`***.example.com/date/to/file.jpg` 

</details>

伪静态与固定链接
--------------------------------------------------------------------------------------------------------------------------------------------------------------------
> 如需实现 [演示站](http://wpk.2broear.com) 的 _permalink/url_ 层级（404等预设页面也需要配置伪静态或在分类层级前加入 `index.php` 后才能访问）如下：

<details>
      <summary>展开内容</summary>
      
___Nginx 伪静态规则___ （apache或其他环境请自行转换语法，宝塔面板可一键配置）
``` nginx
location / {
    try_files $uri $uri/ /index.php?$args;
}
rewrite /wp-admin$ $scheme://$host$uri/ permanent;
```

___WordPress 固定链接___ （请勿关闭 __通用控制__ 中的 ___移除 CATEGORY___ 选项）
``` plaintext
/%category%/%postname%_%post_id%  
```
固定连接可删除 `%post_id%`，建议保留 `%postname%` 后的下划线 `_` __如下图所示__（其目的是为了访问多层级分类时正确显示 url 地址栏中的分类/页面层级，属于临时方案）

---

__更新:__ 现已在 wp 初始化时自动设置 `permalink_structure` 默认值为 `/%category%/%day%-%monthnum%-%year%_%postname%`, 可前往 __设置->固定链接__ 自行更改

![2blog_wordpress_theme](https://raw.githubusercontent.com/2Broear/2BLOG/main/screenshots/permalink.png "permalink setting")

</details>

支持 & 其他
====================================================================================================================================================================

### 开发支持
<details>
      <summary>感谢以下产品提供的服务，2BLOG 主题在这些服务下完善功能</summary>
      
- [WordPress](https://wordpress.org) 提供的CMS程序及主题开发文档支持
- [Leancloud](https://www.leancloud.cn) 提供的 BaaS 数据储存服务
- [Valine](http://valine.js.org) 提供的无后端评论系统及 [zhaojun1998](https://github.com/zhaojun1998/Valine-Admin) 提供的 valine 评论邮件通知
- Wechat（企业）提供的微信消息推送服务
- ...
- [Ying 酱](https://blog.luvying.com) 及 [橘纸柚](https://lovemen.cc/) 提供的随机动漫图片API
- [mokeyjay 超能小紫](https://www.mokeyjay.com) 提供的Pixiv每日排行榜小挂件

</details>

### 前端插件
- iconmoon.io（字体图标）
- highlight.js（代码高亮）
- fancybox.js（图集灯箱）
- qrcode.js（二维码生成）
- html2canvas.js（html图片生成）
- marked.js（markdown文档解析）
- md5.js（md5邮件解析）
- nprogress.js（文档进度条）
- ~~jquery.js（）~~
- ...
  
外网有些很棒的 WordPress 主题开发文档教程，这些文档一定程度上提升了开发进度，后续将在此补上相关链接。

- [https://wp-kama.com](https://wp-kama.com)
- [https://wordpress.stackexchange.com](https://wordpress.stackexchange.com)
- [https://developer.wordpress.org/reference](https://developer.wordpress.org/reference)

### 版权声明

本主题遵循GPL协议开源，在您免费使用此主题时，__请保留站点底部右下角声明字样，谢谢。__

<details>
      <summary>主题差异化问题</summary>
      
这款主题和官方在模板设计上有些许不同，通过 _wp_ 默认主题模板文件不难看出一款 _wordpress_ 主题在导航上是通过页面来进行导航的。但是，我之前一直都是用的分类进行页面导航，使用分类页面无法调用评论而百思不得其解的时候到处瞎逛论坛的时候才发现 __wp 根本不支持通过分类调用评论，__ 这也就是说之前写的那套定制导航的逻辑全都不能用，因为主题 __部分页面在调用页面数据的同时需要调用页面评论__ ，这个就很尴尬了，而且通过分类来导航很难控制页面层级关系，再三犹豫期间又跑去写了一个“页面”导航，结果差强人意， __最后还是选择了使用分类作为页面导航__ ，同时在解决调用页面评论这方面的方案则是分类 __固定链接的 url 重写__ ，该方案在伪静态下工作的很好。

不过还要解决之前静态主题的一个层级问题，需要部分分类别名为“`/`”实现略过父级访问子级链接，那么这个 _wordpress_ 不支持这个操作，所以只能通过 `$wpdb` 来强制写入 __（这里就涉及了分类与页面直接的操作数据互相同步）__ ，好在问题目前是得以解决，然而这一连串的问题如果通过页面来做导航就完全不存在了。
</details>

Futures todo & bugs
--------------------------------------------------------------------------------------------------------------------------------------------------------------------

### todo

- ✅ ~~归档 Archive 页面~~
- ✅ ~~标签云（页面、挂件）~~
- ✅ ~~文章目录 Index 索引~~
- ✅ ~~引进 Twikoo 评论~~
- ✅ ~~集成图片懒加载~~
- ✅ ~~视频动态预览~~
- ✅ ~~集成 Ajax 功能到 wp 评论~~
- ✅ ~~集成 chatGPT 文章摘要~~
- ✅ ~~新增 Marker 文章标记~~
- 集成 Valine 自定义功能到 WordPress 评论

### bug
<details>
      <summary>展开内容</summary>
      
1. ~~创建 `category` 分类时，本该默认将层级同步到页面，但由于使用 `slash` __“/”__ 默认将所有页面都定位顶级，这导致了存在父级slug不为“/”的分类子级无法访问其页面（即无法调用页面评论），那么此时应该将页面中该子级的父级定位其分类父级。（目前是创建时可以正常同步，修改分类时页面正常同步但其分类父级会出现归为顶级0的错误，暂时无解）~~ 

      __暂不打算修复了（3级分类需要评论支持的，请手动在“页面”中指定其父级）__ （当使用 `slash` 即 “/”作为分类别名时，在其分类下发布文章时请勿选择其父级别名为“/”的分类，否则可能导致文章层级404错误）
   
</details>
