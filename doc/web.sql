-- phpMyAdmin SQL Dump
-- version 4.1.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 19, 2014 at 05:36 PM
-- Server version: 5.5.35-1ubuntu1
-- PHP Version: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `siscon`
--
CREATE DATABASE IF NOT EXISTS `siscon` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `siscon`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) DEFAULT NULL COMMENT '管理员账号',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `group_id` int(11) DEFAULT NULL COMMENT '部门ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `role_id` int(11) DEFAULT NULL COMMENT '权限组ID',
  `mobile` varchar(24) DEFAULT NULL COMMENT '手机号',
  `email` varchar(100) DEFAULT NULL COMMENT '管理员邮箱',
  `pic` varchar(120) DEFAULT NULL COMMENT '管理员头像',
  `info` varchar(240) DEFAULT NULL COMMENT '管理员介绍',
  `weibo` varchar(120) DEFAULT NULL COMMENT '管理员WEIBO',
  `truename` varchar(120) DEFAULT NULL COMMENT '真实姓名',
  `status` int(1) DEFAULT NULL COMMENT '冻结状态1正常2冻结不能登陆',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) DEFAULT NULL COMMENT '权限名',
  `auth` varchar(100) DEFAULT NULL COMMENT '权限',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL COMMENT '点评类型1为文章点评',
  `did` int(11) DEFAULT NULL COMMENT '点评来源ID',
  `name` varchar(24) DEFAULT NULL COMMENT '点评标题',
  `content` text COMMENT '点评内容',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `weibo` int(1) DEFAULT NULL COMMENT '是否发布到新浪微博',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `ip` varchar(32) NOT NULL,
  `city` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `happy_cate`
--

CREATE TABLE IF NOT EXISTS `happy_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) DEFAULT NULL COMMENT '父级栏目ID',
  `name` varchar(24) DEFAULT NULL COMMENT '栏目名',
  `info` varchar(100) DEFAULT NULL COMMENT '栏目介绍',
  `key` varchar(200) DEFAULT NULL COMMENT '模板KEY,对应前台URL,如果为链接则直接外链',
  `reorder` int(11) DEFAULT NULL COMMENT '排序',
  `style` int(1) DEFAULT NULL COMMENT '样式',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `status` int(1) DEFAULT NULL COMMENT '状态1为启用2为不启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `happy_config`
--

CREATE TABLE IF NOT EXISTS `happy_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) DEFAULT NULL COMMENT '配置名',
  `site` varchar(200) DEFAULT NULL COMMENT '配置网址',
  `site_rule` varchar(200) DEFAULT NULL COMMENT '网址匹配规则',
  `name_rule` varchar(200) DEFAULT NULL COMMENT '标题匹配规则',
  `pic_rule` varchar(200) DEFAULT NULL COMMENT '图片匹配规则',
  `content_rule` varchar(200) DEFAULT NULL COMMENT '内容匹配规则',
  `date_rule` varchar(200) NOT NULL COMMENT '时间规则',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `sdate` int(11) DEFAULT NULL COMMENT '抓取时间',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `page` varchar(200) NOT NULL COMMENT '分页',
  `cate_id` int(11) NOT NULL COMMENT '栏目id',
  `type` int(1) NOT NULL COMMENT '类型1为图片2为视频3为文字',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态1为正常2为正在抓取数据3为已完成抓取',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '抓取间隔秒数',
  `second` int(11) NOT NULL DEFAULT '0' COMMENT '已抓取次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `happy_data`
--

CREATE TABLE IF NOT EXISTS `happy_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) DEFAULT NULL COMMENT '配置ID',
  `cate_id` int(11) DEFAULT NULL COMMENT '栏目ID',
  `num` int(1) NOT NULL COMMENT '内容数量',
  `type` int(1) DEFAULT NULL COMMENT '类型1为图片2为视频3为文字',
  `name` varchar(60) DEFAULT NULL COMMENT '标题',
  `pic` varchar(200) DEFAULT NULL COMMENT '封面图片',
  `spic` varchar(200) NOT NULL COMMENT '原图片地址',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `zdate` int(11) NOT NULL COMMENT '抓取时间',
  `muser` varchar(24) NOT NULL,
  `cuser` varchar(24) NOT NULL,
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `status` int(1) DEFAULT NULL COMMENT '状态1为发布2为暂时不发布',
  `content` text NOT NULL,
  `stime` int(11) NOT NULL COMMENT '发布时间，仅当status=3时有效',
  `source_base_url` varchar(255) NOT NULL COMMENT '来源的列表页',
  `source_url` varchar(255) NOT NULL COMMENT '来源的页面',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `happy_data_pic`
--

CREATE TABLE IF NOT EXISTS `happy_data_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) DEFAULT NULL COMMENT '配置ID',
  `data_id` int(11) DEFAULT NULL COMMENT '内容ID',
  `reorder` int(11) DEFAULT NULL COMMENT '排序',
  `name` varchar(60) DEFAULT NULL COMMENT '标题',
  `pic` varchar(200) DEFAULT NULL COMMENT '图片地址',
  `spic` varchar(200) NOT NULL COMMENT '原图片地址',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `zdate` int(11) NOT NULL COMMENT '抓取时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `oauth`
--

CREATE TABLE IF NOT EXISTS `oauth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `system` varchar(30) DEFAULT NULL COMMENT '所属项目',
  `name` varchar(30) DEFAULT NULL COMMENT '名称',
  `oid` varchar(200) DEFAULT NULL COMMENT '来源的OID',
  `sid` int(11) DEFAULT NULL COMMENT '来源的系统ID',
  `uid` int(11) DEFAULT NULL COMMENT '对应的本站UID',
  `token_code` varchar(200) DEFAULT NULL COMMENT '生成的正式TOKEN信息',
  `token_refresh` varchar(200) DEFAULT NULL COMMENT '生成的正式TOKEN信息',
  `token_type` varchar(200) DEFAULT NULL COMMENT '生成的正式TOKEN信息',
  `token_time` varchar(200) DEFAULT NULL COMMENT '生成的正式TOKEN信息',
  `token_id` varchar(24) DEFAULT NULL COMMENT '生成的正式TOKEN信息',
  `mdate` int(11) DEFAULT NULL COMMENT '数据修改时间',
  `cdate` int(11) DEFAULT NULL COMMENT '数据添加时间',
  `state` int(1) DEFAULT NULL COMMENT '1是数据存在，2是数据删除',
  `ip` varchar(32) DEFAULT NULL COMMENT '插入数据的IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pic`
--

CREATE TABLE IF NOT EXISTS `pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(11) DEFAULT NULL COMMENT '图片配置ID',
  `did` int(11) DEFAULT NULL COMMENT '关联ID',
  `file` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `source` varchar(255) DEFAULT NULL COMMENT '原图片地址',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pic_config`
--

CREATE TABLE IF NOT EXISTS `pic_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) DEFAULT NULL COMMENT '配置名',
  `key` varchar(24) DEFAULT NULL COMMENT '配置KEY',
  `width` int(11) DEFAULT NULL COMMENT '宽度设置',
  `height` int(11) DEFAULT NULL COMMENT '高度设置',
  `size` int(11) DEFAULT NULL COMMENT '图片大小',
  `t_width` int(11) DEFAULT NULL COMMENT '缩放图宽度设置',
  `t_height` int(11) DEFAULT NULL COMMENT '缩放图高度设置',
  `t_type` int(11) DEFAULT NULL COMMENT '缩放图类型1为等比2为居中3为上4为下',
  `c_width` int(11) DEFAULT NULL COMMENT '裁图宽度设置',
  `c_height` int(11) DEFAULT NULL COMMENT '裁图高度设置',
  `c_type` int(11) DEFAULT NULL COMMENT '裁图类型1为等比2为居中3为上4为下',
  `w_type` int(11) DEFAULT NULL COMMENT '水印类型1左上2为左下3为右上4为右下5为居中',
  `w_pic` int(11) DEFAULT NULL COMMENT '水印图片，从配置里读取',
  `filename` int(1) DEFAULT NULL COMMENT '是否生成文件名1不生成2生成',
  `quality` int(11) DEFAULT NULL COMMENT '清晰度默认为0',
  `content` varchar(255) DEFAULT NULL COMMENT '备注描述',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `status` int(1) DEFAULT NULL COMMENT '1为开放2为关闭',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(24) DEFAULT NULL COMMENT '权限组名称',
  `auth` text COMMENT '权限',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `menu` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `share_log`
--

CREATE TABLE IF NOT EXISTS `share_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `surl` varchar(200) DEFAULT NULL COMMENT '链接',
  `pic` varchar(200) DEFAULT NULL COMMENT '图片',
  `url` varchar(200) DEFAULT NULL COMMENT '链接',
  `title` varchar(32) DEFAULT NULL COMMENT '标题',
  `article` int(11) DEFAULT NULL COMMENT '文章ID',
  `cate` int(11) DEFAULT NULL COMMENT '类别ID',
  `site` int(1) DEFAULT NULL COMMENT '站点ID',
  `platform` varchar(100) DEFAULT NULL COMMENT '平台',
  `browser` varchar(50) DEFAULT NULL COMMENT '浏览器',
  `city` varchar(100) DEFAULT NULL COMMENT '城市',
  `ip` varchar(32) DEFAULT NULL COMMENT 'IP',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `status` int(1) DEFAULT NULL COMMENT '1为开放2为关闭',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `share_reflux`
--

CREATE TABLE IF NOT EXISTS `share_reflux` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `surl` varchar(200) DEFAULT NULL COMMENT '链接',
  `pic` varchar(200) DEFAULT NULL COMMENT '图片',
  `url` varchar(200) DEFAULT NULL COMMENT '链接',
  `title` varchar(32) DEFAULT NULL COMMENT '标题',
  `article` int(11) DEFAULT NULL COMMENT '文章ID',
  `cate` int(11) DEFAULT NULL COMMENT '类别ID',
  `site` int(1) DEFAULT NULL COMMENT '站点ID',
  `platform` varchar(100) DEFAULT NULL COMMENT '平台',
  `browser` varchar(50) DEFAULT NULL COMMENT '浏览器',
  `city` varchar(100) DEFAULT NULL COMMENT '城市',
  `ip` varchar(32) DEFAULT NULL COMMENT 'IP',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `status` int(1) DEFAULT NULL COMMENT '1为开放2为关闭',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `share_total`
--

CREATE TABLE IF NOT EXISTS `share_total` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url_hash` varchar(200) DEFAULT NULL COMMENT 'URLHASH',
  `url` varchar(200) DEFAULT NULL COMMENT '链接',
  `total` int(32) DEFAULT NULL COMMENT '总数',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `cuser` varchar(24) DEFAULT NULL COMMENT '创建人',
  `muser` varchar(24) DEFAULT NULL COMMENT '修改人',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `cate` int(11) NOT NULL,
  `article` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(24) DEFAULT NULL COMMENT '用户名',
  `password` varchar(32) DEFAULT NULL COMMENT '用户密码',
  `sex` varchar(10) DEFAULT NULL COMMENT '性别',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(32) DEFAULT NULL COMMENT '手机号',
  `pic` varchar(255) DEFAULT NULL COMMENT '头像',
  `ding` int(1) DEFAULT NULL COMMENT '是否订阅电子报，1订阅',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `status` int(1) DEFAULT NULL COMMENT '1为开放2为关闭',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  `level` int(11) NOT NULL DEFAULT '1',
  `muser` varchar(24) NOT NULL,
  `cuser` varchar(24) NOT NULL,
  `truename` varchar(24) NOT NULL,
  `token_type` int(11) NOT NULL,
  `token_id` varchar(24) NOT NULL,
  `token_system` varchar(24) NOT NULL,
  `token_uid` varchar(50) NOT NULL,
  `birth` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_ding`
--

CREATE TABLE IF NOT EXISTS `user_ding` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `cdate` int(11) DEFAULT NULL COMMENT '创建时间',
  `mdate` int(11) DEFAULT NULL COMMENT '修改时间',
  `status` int(1) DEFAULT NULL COMMENT '1为未发送邮件刚刚订阅2为已订阅3为退订',
  `state` int(1) DEFAULT NULL COMMENT '状态1为存在2为删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



-- phpMyAdmin SQL Dump
-- version 4.1.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 19, 2014 at 05:38 PM
-- Server version: 5.5.35-1ubuntu1
-- PHP Version: 5.5.9-1ubuntu4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `siscon`
--

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`, `group_id`, `uid`, `role_id`, `mobile`, `email`, `pic`, `info`, `weibo`, `truename`, `status`, `cuser`, `muser`, `cdate`, `mdate`, `state`) VALUES
(1, 'admin', '96e79218965eb72c92a549dd5a330112', NULL, NULL, 1, NULL, 'admin@admin.com', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 1);

--
-- Dumping data for table `auth`
--

INSERT INTO `auth` (`name`, `auth`, `cuser`, `muser`, `cdate`, `mdate`, `state`) VALUES
('管理首页', 'main/manage/home', 'admin', 'admin', 1390091630, 1390189962, 1),
('管理员用户组列表', 'admin/manage/role_list', 'admin', 'admin', 1390092802, 1390190168, 1),
('管理员列表', 'admin/manage/list', 'admin', 'admin', 1390092804, 1390190159, 1),
('管理员职位列表', 'admin/manage/group_list', 'admin', 'admin', 1390092805, 1390190150, 1),
('管理员权限列表', 'admin/manage/auth_list', 'admin', 'admin', 1390092808, 1390190139, 1),
('管理员更新', 'admin/manage/update', 'admin', 'admin', 1390092814, 1390190132, 1),
('管理员修改资料', 'admin/manage/edit', 'admin', 'admin', 1390092883, 1390190125, 1),
('管理员删除', 'admin/manage/delete', 'admin', 'admin', 1390093567, 1390190089, 1),
('图库配置列表', 'pic/manage/list', 'admin', 'admin', 1390126885, 1390190033, 1),
('图库配置更新', 'pic/manage/update', 'admin', 'admin', 1390126893, 1390190025, 1),
('用户组更新', 'admin/manage/role_update', 'admin', 'admin', 1390189626, 1390189926, 1),
('权限更新', 'admin/manage/auth_update', 'admin', 'admin', 1390189909, 1390189936, 1),
('数据配置列表', 'happy/manage/list', 'admin', 'admin', 1418717455, 1418717455, 1),
('数据配置更新', 'happy/manage/update', 'admin', 'admin', 1418717457, 1418717457, 1),
('数据分类列表', 'happy/manage/cate_list', '', '', 1418801301, 1418801301, 1),
('数据分类更新', 'happy/manage/cate_update', '', '', 1418801303, 1418801303, 1),
('数据列表', 'happy/manage/data_list', 'admin', 'admin', 1418801662, 1418801662, 1),
('数据内容列表', 'happy/manage/content_list', 'admin', 'admin', 1418802733, 1418802733, 1),
('数据更新', 'happy/manage/data_update', 'admin', 'admin', 1418805083, 1418805083, 1),
('数据字段更新', 'happy/manage/data_col_update', 'admin', 'admin', 1418808179, 1418808179, 1),

--
-- Dumping data for table `pic_config`
--

INSERT INTO `pic_config` (`id`, `name`, `key`, `width`, `height`, `size`, `t_width`, `t_height`, `t_type`, `c_width`, `c_height`, `c_type`, `w_type`, `w_pic`, `filename`, `quality`, `content`, `cuser`, `muser`, `cdate`, `mdate`, `status`, `state`) VALUES
(1, '图库', 'pic', 0, 0, 0, 0, 0, 0, 0, 0, 0, -1, 1, 0, 0, '', 'admin', 'admin', 1389715757, 1389715757, 1, 1),
(2, '文章标准图', 'news_pic', 1000, 1000, 0, 350, 350, 0, 0, 0, 0, 4, 1, 0, 0, '', 'admin', 'admin', 1389715809, 1392441266, 1, 1),
(3, '文章内容图', 'news_content', 0, 0, 0, 0, 0, 0, 0, 0, 0, -1, 1, 0, 0, '', 'admin', 'admin', 1389715854, 1389715854, 1, 1),
(4, '公共配置', 'common', 0, 0, 0, 0, 0, 0, 0, 0, 0, -1, 1, 0, 0, '', 'admin', 'admin', 1389944960, 1389944960, 1, 1),

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `auth`, `cuser`, `muser`, `cdate`, `mdate`, `state`, `menu`) VALUES
(1, '系统管理员', 'all', '', '', 1390091392, 1390091392, 1, ''),
(2, '编辑', 'all', '', 'admin', 1390091392, 1392435104, 1, 'YTozOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MTI6IuWGheWuueeuoeeQhiI7czo1OiJjaGlsZCI7YToxOntpOjc7YToyOntzOjQ6Im5hbWUiO3M6MTg6Iuenu+WKqOS4k+mimOi9rOWMliI7czo0OiJsaW5rIjtzOjE5OiJmZWF0dXJlL21hbmFnZS9saXN0Ijt9fX1pOjE7YToyOntzOjQ6Im5hbWUiO3M6MjQ6Iue9keermeeUqOaIt+aVsOaNrueuoeeQhiI7czo1OiJjaGlsZCI7YToyOntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MTI6IueUqOaIt+euoeeQhiI7czo0OiJsaW5rIjtzOjE2OiJ1c2VyL21hbmFnZS9saXN0Ijt9aToxO2E6Mjp7czo0OiJuYW1lIjtzOjEyOiLor4TorrrnrqHnkIYiO3M6NDoibGluayI7czoxOToiY29tbWVudC9tYW5hZ2UvbGlzdCI7fX19aToyO2E6MTp7czo0OiJuYW1lIjtzOjE4OiLlk4HniYzljZXlk4HnrqHnkIYiO319');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

