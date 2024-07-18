CREATE TABLE `yjorder_balance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_bill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` char(50) NOT NULL DEFAULT '',
  `bill_sort_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `in_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `in_count` int(10) unsigned NOT NULL DEFAULT '0',
  `in_price_out` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `in_count_out` int(10) unsigned NOT NULL DEFAULT '0',
  `out_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `out_count` int(10) unsigned NOT NULL DEFAULT '0',
  `out_price_in` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `out_count_in` int(10) unsigned NOT NULL DEFAULT '0',
  `all_in` decimal(10,2) NOT NULL DEFAULT '0.00',
  `all_out` decimal(10,2) NOT NULL DEFAULT '0.00',
  `all_add` decimal(10,2) NOT NULL DEFAULT '0.00',
  `text_id_note` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_bill_sort` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_bill_sort`(`id`,`name`,`type`,`sort`,`date`) VALUES('1','出售产品','0','1','1486428465');

CREATE TABLE `yjorder_brand` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `color` char(20) NOT NULL DEFAULT '',
  `logo` char(25) NOT NULL,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_brand_page` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '',
  `title` char(255) NOT NULL DEFAULT '',
  `keyword` char(255) NOT NULL DEFAULT '',
  `description` char(255) NOT NULL DEFAULT '',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `left_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bg_color` char(20) NOT NULL DEFAULT '',
  `page` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `copyright` char(255) NOT NULL DEFAULT '',
  `code_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text_id_code` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_nav` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` char(255) NOT NULL DEFAULT '',
  `share_title` char(100) NOT NULL DEFAULT '',
  `share_pic` char(25) NOT NULL,
  `share_desc` char(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_brand_page`(`id`,`name`,`title`,`keyword`,`description`,`width`,`left_width`,`bg_color`,`page`,`copyright`,`code_type`,`text_id_code`,`text_id_nav`,`icon`,`share_title`,`share_pic`,`share_desc`) VALUES('1','','','','','750','100','#F3F3F3','10','','0','0','0','','','','');

CREATE TABLE `yjorder_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `color` char(20) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_category_page` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL DEFAULT '',
  `title` char(255) NOT NULL DEFAULT '',
  `keyword` char(255) NOT NULL DEFAULT '',
  `description` char(255) NOT NULL DEFAULT '',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `left_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bg_color` char(20) NOT NULL DEFAULT '',
  `copyright` char(255) NOT NULL DEFAULT '',
  `code_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text_id_code` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_nav` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` char(255) NOT NULL DEFAULT '',
  `share_title` char(70) NOT NULL DEFAULT '',
  `share_pic` char(25) NOT NULL,
  `share_desc` char(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_category_page`(`id`,`name`,`title`,`keyword`,`description`,`width`,`left_width`,`bg_color`,`copyright`,`code_type`,`text_id_code`,`text_id_nav`,`icon`,`share_title`,`share_pic`,`share_desc`) VALUES('1','','','','','750','100','#F3F3F3','','0','0','0','','','','');

CREATE TABLE `yjorder_click` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `wxxcx_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_css` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `filename` char(20) NOT NULL DEFAULT '',
  `description` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('1','0','basic','基本样式');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('2','0','gallery','图片库');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('3','0','single_page','后台单页公共样式');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('4','0','tip','信息提示页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('5','1','basic','基本样式');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('6','1','brand','品牌详情页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('7','1','category','品牌分类页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('8','1','index','列表页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('9','1','item','商品详情页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('10','1','order','订单查询');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('11','1','pay','订单支付');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('12','1','template1','手机版下单页1、2');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('13','1','template2','手机版下单页3');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('14','1','template3','手机版下单页4');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('15','1','template4','电脑版下单页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('16','2','bill','账单管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('17','2','bill_statistic','账单统计');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('18','2','brand','品牌管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('19','2','captcha','验证码');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('20','2','category','品牌分类');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('21','2','css','CSS管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('22','2','database','数据表状态');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('23','2','database_backup','数据库备份');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('24','2','index','后台首页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('25','2','install','系统安装');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('26','2','item','商品页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('27','2','login','登录');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('28','2','manager','管理员/分销商');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('29','2','message','留言管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('30','2','message_board','留言板');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('31','2','order','订单管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('32','2','order_output','订单导出设置');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('33','2','order_recycle','订单回收站');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('34','2','order_statistic','订单统计');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('35','2','order_ui','订单界面设置');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('36','2','permit_group','权限组');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('37','2','picture','图片管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('38','2','product','商品管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('39','2','profile','个人中心');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('40','2','reset','重置密码');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('41','2','smtp','SMTP服务器');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('42','2','system','系统设置');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('43','2','template_style','模板样式');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('44','2','validate_file','生成验证文件');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('45','2','visit_wxxcx','访问统计（微信小程序）');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('46','2','withdraw','提现管理');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('47','2','wxxcx','微信小程序');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('48','3','index','后台首页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('49','3','login','登录页');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('50','3','order','订单');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('51','3','order_statistic','订单统计');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('52','3','profile','个人中心');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('53','3','register','注册');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('54','3','visit_wxxcx','访问统计（微信小程序）');
INSERT INTO `yjorder_css`(`id`,`type`,`filename`,`description`) VALUES('55','3','wxxcx','微信小程序');

CREATE TABLE `yjorder_express` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `code` char(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_field` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('1','订购数量','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('2','姓名','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('3','联系电话','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('4','所在地区（选填）','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('5','所在地区（手填）','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('6','街道地址','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('7','备注','0');
INSERT INTO `yjorder_field`(`id`,`name`,`is_default`) VALUES('8','电子邮箱','0');

CREATE TABLE `yjorder_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `text_id_note` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0',
  `preview` char(25) NOT NULL,
  `tag` char(255) NOT NULL DEFAULT '',
  `tag_bg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_show_price` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `price1` char(10) NOT NULL DEFAULT '',
  `price2` char(10) NOT NULL DEFAULT '',
  `sale` int(10) unsigned NOT NULL DEFAULT '0',
  `sale_minute` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sale_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `countdown1` int(10) unsigned NOT NULL DEFAULT '0',
  `countdown2` int(10) unsigned NOT NULL DEFAULT '0',
  `is_show_send` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_distribution` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` char(255) NOT NULL DEFAULT '',
  `keyword` char(255) NOT NULL DEFAULT '',
  `description` char(255) NOT NULL DEFAULT '',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bg_color` char(20) NOT NULL DEFAULT '',
  `copyright` char(255) NOT NULL DEFAULT '',
  `code_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text_id_code` int(10) unsigned NOT NULL DEFAULT '0',
  `tel` char(20) NOT NULL DEFAULT '',
  `sms` char(20) NOT NULL DEFAULT '',
  `qq` char(15) NOT NULL DEFAULT '',
  `picture` char(233) NOT NULL DEFAULT '',
  `text_id_buy` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_procedure` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_introduce` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_service` int(10) unsigned NOT NULL DEFAULT '0',
  `message_board_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_type` char(5) NOT NULL DEFAULT '',
  `text_id_comment` int(10) unsigned NOT NULL DEFAULT '0',
  `column_name1` char(15) NOT NULL DEFAULT '',
  `text_id_column_content1` int(10) unsigned NOT NULL DEFAULT '0',
  `column_name2` char(15) NOT NULL DEFAULT '',
  `text_id_column_content2` int(10) unsigned NOT NULL DEFAULT '0',
  `column_name3` char(15) NOT NULL DEFAULT '',
  `text_id_column_content3` int(10) unsigned NOT NULL DEFAULT '0',
  `column_name4` char(15) NOT NULL DEFAULT '',
  `text_id_column_content4` int(10) unsigned NOT NULL DEFAULT '0',
  `column_name5` char(15) NOT NULL DEFAULT '',
  `text_id_column_content5` int(10) unsigned NOT NULL DEFAULT '0',
  `template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `product_sort_ids` char(255) NOT NULL DEFAULT '',
  `product_ids` char(255) NOT NULL DEFAULT '',
  `product_default` int(10) unsigned NOT NULL DEFAULT '0',
  `product_view_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` char(25) NOT NULL DEFAULT '',
  `text_id_nav` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` char(255) NOT NULL DEFAULT '',
  `share_title` char(50) NOT NULL DEFAULT '',
  `share_pic` char(25) NOT NULL,
  `share_desc` char(100) NOT NULL DEFAULT '',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `module` char(20) NOT NULL DEFAULT '',
  `title` char(255) NOT NULL DEFAULT '',
  `keyword` char(255) NOT NULL DEFAULT '',
  `description` char(255) NOT NULL DEFAULT '',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `bg_color` char(20) NOT NULL DEFAULT '',
  `copyright` char(255) NOT NULL DEFAULT '',
  `code_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text_id_code` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_item_ids` int(10) unsigned NOT NULL DEFAULT '0',
  `page` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `text_id_banner` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_nav` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` char(255) NOT NULL DEFAULT '',
  `share_title` char(50) NOT NULL DEFAULT '',
  `share_pic` char(25) NOT NULL,
  `share_desc` char(100) NOT NULL DEFAULT '',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_login_record_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `pass` char(40) NOT NULL DEFAULT '',
  `email` char(255) NOT NULL DEFAULT '',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_activation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `permit_group_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `order_permit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bill_permit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `wechat_open_id` char(28) NOT NULL DEFAULT '',
  `wechat_union_id` char(28) NOT NULL DEFAULT '',
  `qq_open_id` char(32) NOT NULL DEFAULT '',
  `distributor_code` char(10) NOT NULL DEFAULT '',
  `bank` char(30) NOT NULL DEFAULT '',
  `real_name` char(20) NOT NULL DEFAULT '',
  `account` char(50) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `tel` char(20) NOT NULL DEFAULT '',
  `email` char(50) NOT NULL DEFAULT '',
  `text_id_content` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `message_board_id` int(10) unsigned NOT NULL DEFAULT '0',
  `text_id_reply` int(10) unsigned NOT NULL DEFAULT '0',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_message_board` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `field` char(10) NOT NULL DEFAULT '',
  `captcha_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `page` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` char(13) NOT NULL DEFAULT '',
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attr` char(255) NOT NULL DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` char(20) NOT NULL DEFAULT '',
  `tel` char(20) NOT NULL DEFAULT '',
  `province` char(10) NOT NULL DEFAULT '',
  `city` char(15) NOT NULL DEFAULT '',
  `county` char(15) NOT NULL DEFAULT '',
  `town` char(25) NOT NULL DEFAULT '',
  `address` char(200) NOT NULL DEFAULT '',
  `note` char(255) NOT NULL DEFAULT '',
  `email` char(50) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  `referrer` char(255) NOT NULL DEFAULT '',
  `payment_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pay_id` char(28) NOT NULL DEFAULT '',
  `pay_scene` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_date` int(10) unsigned NOT NULL DEFAULT '0',
  `order_state_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `express_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `express_number` char(30) NOT NULL DEFAULT '',
  `commission` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_commission` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_recycle` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_order_state` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `color` char(20) NOT NULL DEFAULT '',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('1','待支付','#008000','1','1','1640137197');
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('2','待发货','#F00','2','0','1640137197');
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('3','已发货','#00F','3','0','1640137197');
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('4','已签收','#C60','4','0','1640137197');
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('5','售后中','#C06','5','0','1640137197');
INSERT INTO `yjorder_order_state`(`id`,`name`,`color`,`sort`,`is_default`,`date`) VALUES('6','交易关闭','#993','6','0','1640137197');

CREATE TABLE `yjorder_permit_data` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(7) NOT NULL,
  `alias` varchar(20) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('1','系统信息','system','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('2','版本号','version_code','0','1');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('3','更新时间','version_date','0','1');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('4','个人信息','profile','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('5','身份','level','0','4');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('6','权限组','permit_group','0','4');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('7','登录次数','login_count','0','4');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('8','上次登录时间','login_date','0','4');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('9','上次登录IP','login_ip','0','4');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('10','订单','order','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('11','总数','total','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('12','待支付','arrearage','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('13','待发货','undelivered','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('14','已发货','delivered','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('15','已签收','received','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('16','售后中','after_sale','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('17','交易关闭','closed','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('18','剩余订单量','count','0','10');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('19','账单','bill','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('20','总收入','income','0','19');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('21','总支出','expend','0','19');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('22','余额','balance','0','19');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('23','商品','product','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('24','总数','total','0','23');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('25','运作商品','view_total','0','23');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('26','分类&品牌','category_brand','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('27','品牌分类','category','0','26');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('28','运作品牌分类','view_category','0','26');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('29','品牌','brand','0','26');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('30','运作品牌','view_brand','0','26');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('31','页面','page','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('32','商品页','item','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('33','分销商品页','distribution_item','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('34','运作商品页','view_item','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('35','列表页','lists','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('36','运作列表页','view_lists','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('37','微信小程序','wxxcx','0','31');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('38','留言','message','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('39','总数','total','0','38');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('40','精选','view','0','38');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('41','未精选','un_view','0','38');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('42','数据','data','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('43','今日网站PV','web_pv','0','42');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('44','今日小程序PV','wxxcx_pv','0','42');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('45','文件','file','0','42');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('46','图片','picture','0','42');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('47','管理员','manager','0','0');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('48','总数','total','0','47');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('49','创始人','founder','0','47');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('50','超级管理员','super','0','47');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('51','普通管理员','general','0','47');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('52','分销商','distributor','0','47');
INSERT INTO `yjorder_permit_data`(`id`,`name`,`alias`,`is_default`,`parent_id`) VALUES('53','待激活','wait_activation','0','47');

CREATE TABLE `yjorder_permit_group` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `text_id_permit_manage_ids` int(10) unsigned NOT NULL DEFAULT '0',
  `permit_data_ids` char(160) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_permit_manage` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL DEFAULT '',
  `controller` varchar(20) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('1','订单管理','Order','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('2','添加','','add','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('3','修改','','update','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('4','详情','','detail','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('5','导出','','output','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('6','删除','','delete','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('7','修改状态','','state','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('8','修改物流','','express','0','1','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('9','界面设置','OrderUi','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('10','重置','','restore','0','9','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('11','导出设置','OrderOutput','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('12','重置','','restore','0','11','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('13','订单回收站','OrderRecycle','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('14','详情','','detail','0','13','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('15','导出','','output','0','13','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('16','还原','','recover','0','13','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('17','删除','','delete','0','13','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('18','订单统计','OrderStatistic','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('19','按天','','day','0','18','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('20','按月','','month','0','18','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('21','按年','','year','0','18','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('22','导出','','output','0','18','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('23','订单状态','OrderState','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('24','添加','','add','0','23','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('25','修改','','update','0','23','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('26','删除','','delete','0','23','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('27','设置默认','','isDefault','0','23','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('28','排序','','sort','0','23','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('29','快递公司','Express','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('30','添加','','add','0','29','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('31','修改','','update','0','29','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('32','删除','','delete','0','29','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('33','账单管理','Bill','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('34','添加','','add','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('35','修改','','update','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('36','详情','','detail','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('37','导出','','output','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('38','删除','','delete','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('39','更新收支余','','update2','0','33','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('40','账单分类','BillSort','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('41','添加','','add','0','40','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('42','修改','','update','0','40','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('43','删除','','delete','0','40','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('44','排序','','sort','0','40','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('45','账单统计','BillStatistic','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('46','按天','','day','0','45','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('47','按月','','month','0','45','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('48','按年','','year','0','45','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('49','导出','','output','0','45','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('50','资金流动','Flow','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('51','添加','','add','0','50','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('52','修改','','update','0','50','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('53','删除','','delete','0','50','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('54','结算管理','Balance','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('55','修改','','update','0','54','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('56','删除','','delete','0','54','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('57','提现管理','Withdraw','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('58','修改','','update','0','57','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('59','删除','','delete','0','57','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('60','商品管理','Product','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('61','添加','','add','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('62','修改','','update','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('63','删除','','delete','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('64','上下架','','isView','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('65','设置默认','','isDefault','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('66','排序','','sort','0','60','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('67','商品分类','ProductSort','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('68','添加','','add','0','67','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('69','修改','','update','0','67','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('70','删除','','delete','0','67','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('71','排序','','sort','0','67','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('72','模板管理','Template','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('73','添加','','add','0','72','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('74','修改','','update','0','72','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('75','删除','','delete','0','72','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('76','获取代码','','code','0','72','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('77','设置默认','','isDefault','0','72','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('78','模板样式','TemplateStyle','','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('79','添加','','add','0','78','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('80','修改','','update','0','78','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('81','删除','','delete','0','78','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('82','下单字段','Field','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('83','设置默认','','isDefault','0','82','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('84','品牌管理','Brand','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('85','添加','','add','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('86','批量添加','','multi','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('87','修改','','update','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('88','排序','','sort','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('89','上下架','','isView','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('90','删除','','delete','0','84','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('91','品牌分类','Category','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('92','添加','','add','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('93','批量添加','','multi','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('94','修改','','update','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('95','排序','','sort','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('96','设置默认','','isDefault','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('97','上下架','','isView','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('98','删除','','delete','0','91','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('99','商品页','Item','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('100','添加','','add','0','99','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('101','修改','','update','0','99','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('102','删除','','delete','0','99','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('103','上下架','','isView','0','99','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('104','获取防伪页代码','','code','0','99','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('105','列表页','Lists','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('106','添加','','add','0','105','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('107','修改','','update','0','105','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('108','删除','','delete','0','105','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('109','上下架','','isView','0','105','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('110','品牌分类页','CategoryPage','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('111','修改','','update','0','110','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('112','品牌详情页','BrandPage','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('113','修改','','update','0','112','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('114','微信小程序','Wxxcx','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('115','添加','','add','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('116','修改','','update','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('117','删除','','delete','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('118','打包','','zip','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('119','下载压缩包','','download','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('120','删除压缩包','','deleteZip','0','114','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('121','留言管理','Message','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('122','修改','','update','0','121','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('123','删除','','delete','0','121','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('124','设置精选','','isView','0','121','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('125','留言板','MessageBoard','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('126','添加','','add','0','125','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('127','修改','','update','0','125','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('128','删除','','delete','0','125','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('129','访问统计','Visit','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('130','导出','','output','0','129','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('131','更新JS','','js','0','129','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('132','访问统计（微信小程序）','VisitWxxcx','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('133','导出','','output','0','132','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('134','文件管理','File','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('135','打包','','zip','0','134','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('136','下载','','download','0','134','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('137','删除','','delete','0','134','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('138','图片管理','Picture','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('139','进入目录','','picture','0','138','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('140','删除目录','','delete','0','138','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('141','清理冗余','','clearPicture','0','138','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('142','清理小程序码','','clearQrcode','0','138','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('143','同步图片至七牛云','','qiniuSynchronize','0','138','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('144','行政区划','District','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('145','添加','','add','0','144','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('146','批量添加','','multi','0','144','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('147','修改','','update','0','144','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('148','删除','','delete','0','144','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('149','验证码','Captcha','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('150','添加','','add','0','149','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('151','修改','','update','0','149','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('152','删除','','delete','0','149','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('153','管理员/分销商','Manager','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('154','添加','','add','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('155','修改','','update','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('156','删除','','delete','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('157','激活','','isActivation','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('158','解绑微信','','wechatOpenId','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('159','解绑QQ','','qqOpenId','0','153','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('160','登录记录','LoginRecordManager','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('161','导出并清空','','output','0','160','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('162','权限组','PermitGroup','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('163','添加','','add','0','162','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('164','修改','','update','0','162','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('165','删除','','delete','0','162','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('166','设置默认','','isDefault','0','162','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('167','管理权限','PermitManage','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('168','设置默认','','isDefault','0','167','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('169','数据权限','PermitData','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('170','设置默认','','isDefault','0','169','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('171','系统设置','System','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('172','生成验证文件','ValidateFile','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('173','SMTP服务器','Smtp','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('174','添加','','add','0','173','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('175','修改','','update','0','173','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('176','删除','','delete','0','173','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('177','运行状态','','state','0','173','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('178','数据表状态','Database','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('179','优化表','','optimize','0','178','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('180','修复AutoIncrement','','repairAutoIncrement','0','178','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('181','更新表缓存','','schema','0','178','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('182','数据库备份','DatabaseBackup','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('183','CSS管理','Css','index','0','0','0');
INSERT INTO `yjorder_permit_manage`(`id`,`name`,`controller`,`action`,`is_default`,`parent_id`,`sort`) VALUES('184','编辑','','update','0','183','0');

CREATE TABLE `yjorder_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `product_sort_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `commission` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `color` char(20) NOT NULL DEFAULT '',
  `email` char(255) NOT NULL DEFAULT '',
  `text_id_attr` int(10) unsigned NOT NULL DEFAULT '0',
  `low_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `high_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `text_id_own_price` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `is_view` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_product_sort` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL DEFAULT '',
  `color` char(20) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_smtp` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `smtp` char(20) NOT NULL DEFAULT '',
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `email` char(50) NOT NULL DEFAULT '',
  `pass` char(50) NOT NULL DEFAULT '',
  `from_name` char(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` char(20) NOT NULL DEFAULT '',
  `template` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `template_style_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `product_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `product_sort_ids` char(255) NOT NULL DEFAULT '',
  `product_ids` char(255) NOT NULL DEFAULT '',
  `product_default` int(10) unsigned NOT NULL DEFAULT '0',
  `product_view_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `field_ids` char(15) NOT NULL DEFAULT '',
  `payment_ids` char(5) NOT NULL DEFAULT '',
  `payment_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_show_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_show_send` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `captcha_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_sms_verify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_sms_notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `success` char(255) NOT NULL DEFAULT '',
  `success2` char(255) NOT NULL DEFAULT '',
  `often` char(255) NOT NULL DEFAULT '',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_template_style` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `bg_color` char(20) NOT NULL DEFAULT '',
  `border_color` char(20) NOT NULL DEFAULT '',
  `button_color` char(20) NOT NULL DEFAULT '',
  `select_current_bg_color` char(20) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('1','#EBFFEF','#0F3','#0C3','#0C3','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('2','#EBF7FF','#B8E3FF','#09F','#09F','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('3','#FFF0F0','#FFD9D9','#F66','#F66','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('4','#FFF7EB','#FFE3B8','#F90','#F90','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('5','#EBFFFF','#A6FFFF','#099','#099','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('6','#F2FFF9','#B2FFD9','#0C6','#0C6','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('7','#E6FAFF','#B2F0FF','#0CF','#0CF','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('8','#FFEBF0','#FFCCD9','#F36','#F36','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('9','#FFF4ED','#FFD9BF','#F60','#F60','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('10','#F2FFFF','#BFFFFF','#3CC','#3CC','1441424358');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('11','#FFF','#FC4400','#F63','#F63','1487560660');
INSERT INTO `yjorder_template_style`(`id`,`bg_color`,`border_color`,`button_color`,`select_current_bg_color`,`date`) VALUES('12','#FFF','#FFF','#BE0F22','#BE0F22','1576467626');

CREATE TABLE `yjorder_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_visit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '',
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `url` char(255) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date1` int(10) unsigned NOT NULL DEFAULT '0',
  `date2` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_visit_wxxcx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '',
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `wxxcx_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
  `scene_id` char(4) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date1` int(10) unsigned NOT NULL DEFAULT '0',
  `date2` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE `yjorder_wxxcx` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `page_id` int(10) unsigned NOT NULL DEFAULT '0',
  `submit_key` char(40) NOT NULL DEFAULT '',
  `app_id` char(18) NOT NULL DEFAULT '',
  `app_secret` char(32) NOT NULL DEFAULT '',
  `pay_mch_id` char(10) NOT NULL DEFAULT '',
  `pay_key` char(32) NOT NULL DEFAULT '',
  `pay_cert_serial_number` char(40) NOT NULL DEFAULT '',
  `text_id_pay_cert_private_key` int(10) unsigned NOT NULL DEFAULT '0',
  `zip` char(40) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;