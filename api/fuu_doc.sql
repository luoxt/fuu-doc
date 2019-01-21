/*
Navicat MySQL Data Transfer

Source Server         : 本地数据库（127.0.0.1）
Source Server Version : 50562
Source Host           : 127.0.0.1:3306
Source Database       : fuu_doc

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2019-01-21 15:50:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `catalog`
-- ----------------------------
DROP TABLE IF EXISTS `catalog`;
CREATE TABLE `catalog` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` text,
  `item_id` int(11) DEFAULT NULL,
  `s_number` int(11) DEFAULT '99',
  `addtime` int(11) DEFAULT '0',
  `parent_cat_id` int(10) NOT NULL DEFAULT '0',
  `level` int(10) NOT NULL DEFAULT '2',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=608 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of catalog
-- ----------------------------

-- ----------------------------
-- Table structure for `item`
-- ----------------------------
DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` text,
  `item_description` text,
  `uid` int(11) DEFAULT NULL,
  `username` text,
  `password` text,
  `addtime` int(11) DEFAULT NULL,
  `last_update_time` int(11) DEFAULT '0',
  `item_domain` text,
  `item_type` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of item
-- ----------------------------

-- ----------------------------
-- Table structure for `item_member`
-- ----------------------------
DROP TABLE IF EXISTS `item_member`;
CREATE TABLE `item_member` (
  `item_member_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` text,
  `addtime` int(11) DEFAULT '0',
  `member_group_id` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`item_member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of item_member
-- ----------------------------

-- ----------------------------
-- Table structure for `page`
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `author_uid` int(11) DEFAULT NULL,
  `author_username` text,
  `item_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `page_title` text,
  `page_content` text,
  `s_number` int(11) DEFAULT '99',
  `addtime` int(11) DEFAULT '0',
  `page_comments` text NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `item_id` (`item_id`) USING BTREE,
  KEY `cat_id` (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2268 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of page
-- ----------------------------

-- ----------------------------
-- Table structure for `page_history`
-- ----------------------------
DROP TABLE IF EXISTS `page_history`;
CREATE TABLE `page_history` (
  `page_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) DEFAULT NULL,
  `author_uid` int(11) DEFAULT NULL,
  `author_username` text,
  `item_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `page_title` text,
  `page_content` text,
  `s_number` int(11) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `page_comments` text NOT NULL,
  PRIMARY KEY (`page_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4729 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of page_history
-- ----------------------------

-- ----------------------------
-- Table structure for `template`
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0',
  `username` char(200) NOT NULL,
  `template_title` char(200) NOT NULL,
  `template_content` text NOT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of template
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` text,
  `groupid` int(11) DEFAULT '2',
  `name` text,
  `avatar` text,
  `avatar_small` text,
  `email` text,
  `password` text,
  `cookie_token` blob,
  `cookie_token_expire` text,
  `reg_time` int(11) DEFAULT '0',
  `last_login_time` int(11) DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------

-- ----------------------------
-- Table structure for `user_token`
-- ----------------------------
DROP TABLE IF EXISTS `user_token`;
CREATE TABLE `user_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `token` char(200) NOT NULL,
  `token_expire` int(11) NOT NULL DEFAULT '0',
  `data_info` text NOT NULL,
  `ip` char(200) NOT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_token
-- ----------------------------
