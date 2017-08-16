<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [公共常量定义文件]
 */

//系统版本号
define('SYSTEM_VERSION','1.0');

//程序公用
define('PAGE_SIZE',10);

//数据库配置
define('_DB_HOSTNAME_','127.0.0.1');
define('_DB_USERNAME_','root');
define('_DB_PASSWORD_','');
define('_DB_DATABASE_','acd');
define('_DB_DBDRIVER_','mysqli');
define('_DB_DBPREFIX_','ad_');
define('DB_LOG', true);

//基础设置
define('_URL_','http://10.35.128.28/www_bak');//站点地址，必须设置
// 加密的key
define('_ENCRYPTION_KEY_','mediav_auto_');
// 时区
define('_TIME_ZONE_','Asia/ShangHai');

//微信配置
define('_WECHAT_APPID_','');
define('_WECHAT_APPSECRET_','');

// 请求方式判断
define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
define('IS_GET', REQUEST_METHOD =='GET' ? true : false);
define('IS_POST', REQUEST_METHOD =='POST' ? true : false);
define('IS_AJAX', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

//超级管理员ID
define('_MANAGER_ID_',1);