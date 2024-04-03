/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50740
 Source Host           : localhost:3306
 Source Schema         : cpone_game

 Target Server Type    : MySQL
 Target Server Version : 50740
 File Encoding         : 65001

 Date: 20/03/2024 12:27:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for block_admin
-- ----------------------------
DROP TABLE IF EXISTS `block_admin`;
CREATE TABLE `block_admin`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `pwd` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登陆密码',
  `salt` char(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加密随机字符串',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-正常,0-禁用',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录时间',
  `last_login_ip` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录IP',
  `login_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录次数',
  `realname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `tel` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电话号码',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `sex` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-男，2-女',
  `remarks` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_admin
-- ----------------------------
INSERT INTO `block_admin` VALUES (1, 'admin', '$2y$10$mge2sZLT2N.K5YOqV5h16.dVA.diYw8CQUDfoa8N7URWNEuusLczq', 'MZiYF9zGyWDhU', 1, 1595591917, 1710907270, 1710906565, 2130706433, 709, '超级管理员', '', '', 1, '可操作后台所有权限');

-- ----------------------------
-- Table structure for block_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `block_admin_group`;
CREATE TABLE `block_admin_group`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-正常，0-禁用',
  `rules` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '节点ID',
  `menus` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单ID',
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '说明',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_admin_group
-- ----------------------------
INSERT INTO `block_admin_group` VALUES (1, '系统管理员', 1, '215,216,217,218,219,220,221,222,223,224,225,226,227,228,230,231,233,234,235,236,232,237,238,242,243,244,245,246,247,248,249,250,251,252,239,240,241,253,314', '1,101,102,120,121,122,123,124,125', '', 1664869754, 1692956571);

-- ----------------------------
-- Table structure for block_admin_group_access
-- ----------------------------
DROP TABLE IF EXISTS `block_admin_group_access`;
CREATE TABLE `block_admin_group_access`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限分配表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_admin_group_access
-- ----------------------------
INSERT INTO `block_admin_group_access` VALUES (1, 1, 1);

-- ----------------------------
-- Table structure for block_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `block_admin_menu`;
CREATE TABLE `block_admin_menu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路由',
  `param` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路由参数',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-正常，0-禁用',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 173 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台菜单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_admin_menu
-- ----------------------------
INSERT INTO `block_admin_menu` VALUES (1, 0, '系统设置', '', '', 'layui-icon-set', 0, 1, 0, 1665403836);
INSERT INTO `block_admin_menu` VALUES (101, 1, '菜单管理', 'menu/index', '', '', 0, 1, 0, 1664521851);
INSERT INTO `block_admin_menu` VALUES (102, 1, '控制器管理', 'rule/index', '', '', 1, 1, 0, 1668737801);
INSERT INTO `block_admin_menu` VALUES (120, 1, '账号管理', '', '', '', 2, 1, 1664527144, 1668737805);
INSERT INTO `block_admin_menu` VALUES (121, 120, '平台账号', 'admin/index', '', '', 0, 1, 1664527232, 0);
INSERT INTO `block_admin_menu` VALUES (122, 120, '平台角色', 'group/index', '', '', 0, 1, 1664527339, 1664529025);
INSERT INTO `block_admin_menu` VALUES (123, 1, '平台设置', '', '', '', 3, 1, 1664872594, 1668737812);
INSERT INTO `block_admin_menu` VALUES (124, 123, '配置管理', 'conf/index', '', '', 0, 1, 1664872642, 0);
INSERT INTO `block_admin_menu` VALUES (125, 123, '平台配置', 'conf/save', '', '', 0, 1, 1664872662, 1664981447);

-- ----------------------------
-- Table structure for block_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `block_admin_rule`;
CREATE TABLE `block_admin_rule`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '节点上级id',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '规则',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `condition` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附加规则',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1-正常，2-禁用',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 400 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限节点' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_admin_rule
-- ----------------------------
INSERT INTO `block_admin_rule` VALUES (215, 0, '', '系统设置', 1, '', 0, 1665482867, 1);
INSERT INTO `block_admin_rule` VALUES (216, 215, 'menu/index', '菜单管理', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (217, 216, 'menu/getMenuList', '获取菜单列表', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (218, 216, 'menu/ajaxMenustatus', '编辑菜单状态', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (219, 216, 'menu/deleteMenu', '删除菜单', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (220, 216, 'menu/editSort', '编辑菜单排序', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (221, 216, 'menu/add', '添加菜单', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (222, 216, 'menu/edit', '编辑菜单', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (223, 215, 'rule/index', '节点管理', 1, '', 0, 1664526076, 1);
INSERT INTO `block_admin_rule` VALUES (224, 223, 'rule/getRuleList', '获取节点列表', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (225, 223, 'rule/ajaxRulestatus', '编辑节点状态', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (226, 223, 'rule/deleteRule', '删除节点', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (227, 223, 'rule/add', '添加节点', 1, '', 0, 0, 1);
INSERT INTO `block_admin_rule` VALUES (228, 223, 'rule/edit', '编辑节点', 1, '', 0, 1664526119, 1);
INSERT INTO `block_admin_rule` VALUES (230, 215, '', '账号管理', 1, '', 1664528927, 0, 1);
INSERT INTO `block_admin_rule` VALUES (231, 230, 'admin/index', '平台账号', 1, '', 1664528967, 0, 1);
INSERT INTO `block_admin_rule` VALUES (232, 230, 'group/index', '平台角色', 1, '', 1664529074, 0, 1);
INSERT INTO `block_admin_rule` VALUES (233, 231, 'admin/getAdminList', '获取平台账号列表', 1, '', 1664529148, 0, 1);
INSERT INTO `block_admin_rule` VALUES (234, 231, 'admin/deleteAdmin', '删除平台账号', 1, '', 1664717289, 0, 1);
INSERT INTO `block_admin_rule` VALUES (235, 231, 'admin/add', '添加平台账号', 1, '', 1664717317, 0, 1);
INSERT INTO `block_admin_rule` VALUES (236, 231, 'admin/edit', '编辑平台账号', 1, '', 1664717346, 0, 1);
INSERT INTO `block_admin_rule` VALUES (237, 232, 'group/getGroupList', '获取角色数据列表', 1, '', 1664848425, 0, 1);
INSERT INTO `block_admin_rule` VALUES (238, 232, 'group/add', '添加平台角色', 1, '', 1664861706, 0, 1);
INSERT INTO `block_admin_rule` VALUES (239, 0, '', 'API接口', 1, '', 1664864113, 1664864129, 1);
INSERT INTO `block_admin_rule` VALUES (240, 239, 'api/getRuleTree', '权限API接口', 1, '', 1664864614, 0, 1);
INSERT INTO `block_admin_rule` VALUES (241, 239, 'api/getMenuTree', '菜单API接口', 1, '', 1664864644, 0, 1);
INSERT INTO `block_admin_rule` VALUES (242, 232, 'group/delGroup', '删除平台角色', 1, '', 1664867578, 0, 1);
INSERT INTO `block_admin_rule` VALUES (243, 232, 'group/edit', '编辑角色', 1, '', 1664869754, 0, 1);
INSERT INTO `block_admin_rule` VALUES (244, 215, '', '平台设置', 1, '', 1664957804, 0, 1);
INSERT INTO `block_admin_rule` VALUES (245, 244, 'conf/index', '配置管理', 1, '', 1664957833, 1664975355, 1);
INSERT INTO `block_admin_rule` VALUES (246, 245, 'conf/getConfList', '获取配置管理数据', 1, '', 1664957892, 1664975370, 1);
INSERT INTO `block_admin_rule` VALUES (247, 245, 'conf/AjaxConfstatus', '修改配置列表状态', 1, '', 1664974653, 1664974696, 1);
INSERT INTO `block_admin_rule` VALUES (248, 245, 'conf/delConf', '删除配置管理', 1, '', 1664975228, 0, 1);
INSERT INTO `block_admin_rule` VALUES (249, 245, 'conf/add', '添加配置管理', 1, '', 1664975294, 0, 1);
INSERT INTO `block_admin_rule` VALUES (250, 245, 'conf/edit', '编辑配置管理', 1, '', 1664975324, 1664975930, 1);
INSERT INTO `block_admin_rule` VALUES (251, 244, 'conf/save', '平台配置', 1, '', 1664981388, 0, 1);
INSERT INTO `block_admin_rule` VALUES (252, 251, 'conf/saveBase', '保存平台配置', 1, '', 1664981410, 0, 1);
INSERT INTO `block_admin_rule` VALUES (253, 239, 'api/uploadInst', '文件上传', 1, '', 1664983009, 0, 1);
INSERT INTO `block_admin_rule` VALUES (314, 239, 'api/upload', '上传文件接口', 1, '', 1665558923, 0, 1);

-- ----------------------------
-- Table structure for block_conf
-- ----------------------------
DROP TABLE IF EXISTS `block_conf`;
CREATE TABLE `block_conf`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置标识',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置标题',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置类型',
  `group` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '配置分组',
  `verify` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '验证器：0-无验证，1-必填，2',
  `extra` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置项',
  `remark` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `value` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `group`(`group`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 270 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of block_conf
-- ----------------------------
INSERT INTO `block_conf` VALUES (1, 'CONFIG_GROUP_LIST', '配置分组', 4, 2, '0', '', '', 1, '1:平台配置\r\n2:系统配置\r\n3:上传配置\r\n4:分享配置', 81, 0, 1710908747);
INSERT INTO `block_conf` VALUES (2, 'CONFIG_TYPE_LIST', '配置类型', 4, 2, '0', '', '主要用于数据解析和页面表单的生成', 1, '1:数字\r\n2:字符串\r\n3:文本域\r\n4:数组\r\n5:枚举\r\n6:单选\r\n7:上传图片\r\n8:时间\r\n9:富文本\r\n10:上传文件', 80, 0, 1710908747);
INSERT INTO `block_conf` VALUES (47, 'COMPANY_NAME', '平台名称', 2, 1, '0', '', '平台名称会显示到前台，请合理输入此名称', 1, '基础功能系统', 0, 1597223808, 1710908730);
INSERT INTO `block_conf` VALUES (49, 'COMPANY_INFO', '平台描述', 3, 1, '0', '', '', 1, '', 0, 1597223865, 1710908730);
INSERT INTO `block_conf` VALUES (50, 'COMPANY_DOMAIN', '平台域名', 2, 1, '0', '', '平台域名请以http://或https://开始', 1, '', 0, 1597223937, 1710908730);
INSERT INTO `block_conf` VALUES (55, 'WEB_SITE_ICP', '备案信息', 2, 1, '0', '', '如：京ICP备19000000号-1', 1, '', 0, 1597224053, 1710908730);
INSERT INTO `block_conf` VALUES (57, 'OPEN_WEB', '网站状态', 6, 2, '0', '1:开启\r\n0:关闭', '', 1, '1', 0, 1597224152, 1710908747);
INSERT INTO `block_conf` VALUES (58, 'OPEN_WEB_NOTICE', '关闭提示语', 2, 2, '0', '', '', 1, '维护中。。。', 0, 1597224184, 1710908747);
INSERT INTO `block_conf` VALUES (130, 'LIST_ROWS', '分页', 1, 2, '0', '', '', 1, '15', 0, 1626340022, 1710908747);
INSERT INTO `block_conf` VALUES (131, 'FILE_SIZE', '文件大小', 1, 3, '0', '', '文件大小单位MB', 1, '10', 0, 1654026428, 1710908436);
INSERT INTO `block_conf` VALUES (132, 'FILE_EXT', '文件后缀', 2, 3, '0', '', '允许上传文件后缀', 1, 'xls,xlsx,csv,jpg,png,jpeg,bmp,mp3,mp4', 0, 1654026799, 1710908436);
INSERT INTO `block_conf` VALUES (133, 'FILE_RULE', '命名方式', 2, 3, '0', '', '文件重命名方式：sha1，md5', 1, 'sha1', 0, 1654026897, 1710908436);
INSERT INTO `block_conf` VALUES (134, 'ALLOW_IP', 'IP黑名单', 3, 2, '0', '', '', 1, '', 0, 1658655921, 1710908747);
INSERT INTO `block_conf` VALUES (142, 'COMPANY_LOGO', '平台LOGO', 7, 1, '0', '', '', 1, '', 0, 1660184036, 1710908730);
INSERT INTO `block_conf` VALUES (146, 'WX_SHARE', '分享标题', 2, 4, '0', '', '微信小程序首页分享的标题', 1, '', 0, 1660190048, 1710908441);
INSERT INTO `block_conf` VALUES (147, 'SHARE_INFO', '分享描述', 3, 4, '0', '', '只在支付宝小程序分享中显示', 1, '', 0, 1660190194, 1710908441);
INSERT INTO `block_conf` VALUES (148, 'SHARE_PIC', '分享图片', 7, 4, '0', '', '', 1, '', 0, 1660190240, 1710908441);
INSERT INTO `block_conf` VALUES (150, 'STORAGE_TYPE', '存储引擎', 5, 3, '0', '1:本地\r\n2:阿里云OSS\r\n3:腾讯云COS', '文件保存方式', 1, '1', 0, 1660197905, 1710908436);
INSERT INTO `block_conf` VALUES (151, 'PIC_DOMAIN', '绑定域名', 2, 3, '0', '', '图片分离时使用，使用阿里云OSS或腾讯云COS时也尽量绑定域名，例如：https://image.future888.cn', 1, '', 0, 1660198255, 1710908436);
INSERT INTO `block_conf` VALUES (152, 'STORAGE_NODE', '存储节点', 2, 3, '0', '', '腾讯云COS的所属地域，例如：ap-beijing，阿里云OSS的endpoint，例如：oss-cn-beijing.aliyuncs.com', 1, '', 0, 1660198495, 1710908436);
INSERT INTO `block_conf` VALUES (153, 'SPACE_NAME', '空间名称', 2, 3, '0', '', '腾讯云的空间名称，例如：xxx-11110000，阿里云OSS的bucket，例如：jshop-jihainet', 1, '', 0, 1660198618, 1710908436);
INSERT INTO `block_conf` VALUES (154, 'SECRET_ID', 'secretId', 2, 3, '0', '', '腾讯云申请地址：https://console.cloud.tencent.com/capi，阿里云申请地址：https://usercenter.console.aliyun.com/#/manag', 1, '', 0, 1660198738, 1710908436);
INSERT INTO `block_conf` VALUES (155, 'SECRET_KEY', 'secretKey', 2, 3, '0', '', '腾讯云secretKey，阿里云accessKeySecret', 1, '', 0, 1660198857, 1710908436);
INSERT INTO `block_conf` VALUES (203, 'PUSH_URL', '推广地址前缀', 2, 2, '0', '', '', 1, 'https://', 0, 1665625493, 1710908747);
INSERT INTO `block_conf` VALUES (206, 'LOGIN_TOKEN', 'Token加密', 2, 2, '0', '', '', 1, 'KLSDJK234kjhr23hrjkl2!$#!', 0, 1665655480, 1710908747);
INSERT INTO `block_conf` VALUES (207, 'LOGIN_TIME', '登录过期时长', 1, 2, '0', '', '秒；用户登录之后，token的过期时长', 1, '86400', 0, 1665711701, 1710908747);

SET FOREIGN_KEY_CHECKS = 1;
