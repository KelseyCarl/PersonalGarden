-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-02-16 17:42:53
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mygarden`
--

-- --------------------------------------------------------

--
-- 表的结构 `photo`
--

CREATE TABLE IF NOT EXISTS `photo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `binarydata` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- 表的结构 `rent_soil`
--

CREATE TABLE IF NOT EXISTS `rent_soil` (
  `rent_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_phone` char(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `soil_id` char(20) NOT NULL,
  `farm_belong` varchar(40) NOT NULL,
  `soil_area` float NOT NULL,
  `soil_status` int(11) NOT NULL DEFAULT '0',
  `rent_period` float NOT NULL,
  `verify_status` int(11) NOT NULL DEFAULT '0',
  `verify_desc` varchar(20) NOT NULL DEFAULT '待审核',
  `unpass_cause` varchar(20) NOT NULL,
  `verify_time` datetime NOT NULL,
  PRIMARY KEY (`rent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='土地租赁申请表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `soil`
--

CREATE TABLE IF NOT EXISTS `soil` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `soil_id` char(20) NOT NULL,
  `soil_name` varchar(40) NOT NULL,
  `farm_belong` varchar(40) NOT NULL,
  `soil_area` float NOT NULL,
  `soil_temper` float NOT NULL,
  `soil_wet` int(11) NOT NULL,
  `soil_status` int(11) NOT NULL DEFAULT '0',
  `monitor_temper_time` datetime NOT NULL,
  `monitor_wet_time` datetime NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='土地信息表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `upload_img`
--

CREATE TABLE IF NOT EXISTS `upload_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_name` varchar(255) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='将页面上传的图片(路径)保存到该表' AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- 表的结构 `userinfor`
--

CREATE TABLE IF NOT EXISTS `userinfor` (
  `token` char(32) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `user_phone` char(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `user_sex` char(10) NOT NULL,
  `user_addr` varchar(100) NOT NULL,
  `user_photo` blob NOT NULL,
  `photo_type` varchar(30) NOT NULL,
  `photo_url` text NOT NULL,
  `login_pass` varchar(20) NOT NULL,
  `soil_nums` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_phone` (`user_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
