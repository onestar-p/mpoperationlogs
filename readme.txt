=== mpOperationLogs ===
Contributors: Mrpeng
Donate link: http://www.ipy8.com/
Tags: operation log, IP record , IP address
Requires at least: 4.2
Tested up to: 4.7
Stable tag: 4.3

后台管理员操作日志、后台登陆IP记录插件(administrator operation logs and IP record Plugin).

== Description ==

本插件主要用于后台管理员、编辑等角色对文章操作的记录(发布\更新\删除)以及用户登录的IP地址记录（This plugin is mainly used for post operation records (release, update, delete) and the user login IP address record.）。
如果在使用中发现BUG或者有任何建议或意见可以发送邮件至“root@ipy8.com”。

注意：在启用本插件时，会在你的数据库中新建两张数据表，1：日志记录表；2：IP地址记录表，如果停用插件将会删除这两张表（如需清空数据停用再启用即可）。


== Installation ==
1.将文件上传至'/wp-content/plugins/'目录下（如：/wp-content/plugins/mpOperationLogs），或者通过WordPress插件商城进行安装。
2.通过WordPress插件界面启用本插件。

1. Upload the plugin files to the `/wp-content/plugins/mpOperationLogs` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==


== Screenshots ==

== Changelog ==

= 1.0.0 =
* just getting started

= 1.0.1 =
* 取消记录自动删除‘自动草稿’日志记录
