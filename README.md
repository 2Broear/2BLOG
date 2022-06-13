# 2BLOG
A Simplized Wordpress Blog Theme Design &amp; Developed from 2broear.com by 2BROEAR Released &amp; openSourced in 2022.

__Preview Site__ ：http://2blog.2broear.com

## 主题简介
历经半年之久的周期，2BLOG也算是可以开启测试了，这里将作为主题日后开源的发布、更新、备份用途。目前主题尚处测试阶段，未上传至 Wordpress 主题。

2BLOG在前静态主题的外观及功能基础之上做了部分修改及更新，其中主要更新内容包括：
- 多级富文本、元数据导航
- 自定义 RSS、SITEMAP 内容
- 自定义搜索内容、列表样式
- 新增漫游影视及资源下载页面子级
- 部分页面支持 wordpress 与 leancloud 数据切换
- 重写 js 逻辑，主文件剥离 jquery（css暂未修改，留到后期更新

当然了，最重要的还是集成了 __Leancloud 与 Wordpress 之间的数据同步__ ，这个主要是因为之前静态博客使用的是 __valine__ 评论系统（其实之前很少使用 leancloud 数据储存），然后我自己改了很多东西，所以在 wordpress 中仍做了数据切换，顺带更新了之前尚未同步数据到 leancloud 的页面

> 在wordpress中除“公告”外所有数据均以文章形式发布，通过后端函数调用数据，而使用leancloud数据的页面将通过 __lbms__ 后台进行数据上传、修改及删除等操作，再通过xhr异步前端调用json数据
