<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140421135942 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
 		$this->addSql("
			CREATE TABLE IF NOT EXISTS `guest` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `userId` int(11) NOT NULL DEFAULT '0'  COMMENT '用户Id',
			  `lastAccessTime` int(11) NOT NULL DEFAULT '0',
			  `lastAccessIp` varchar(64) NOT NULL,
			  `lastAccessTookeen` varchar(255) NOT NULL,			 
			  `createdIp` varchar(64) NOT NULL,
			  `createdTime` int(11) NOT NULL DEFAULT '0',
			  `createdTookeen` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8　COMMENT='游客表' AUTO_INCREMENT=1 ;
		");
        

		$this->addSql("
	        CREATE TABLE IF NOT EXISTS `guest_state` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `totalGuest` int(10) NOT NULL COMMENT '游客总数',
			  `dayNewGuest` int(10) NOT NULL COMMENT '日新增游客数',
			  `dayActGuest` int(10) NOT NULL COMMENT '日活跃游客数',
			  `oneWeekActGuest` int(10) NOT NULL COMMENT '过去一周活跃游客数',
			  `oneMonthActGuest` int(10) NOT NULL COMMENT '过去一月活跃游客数',
			  `oneMonthLoseGuest` int(10) NOT NULL COMMENT '过去一月游客流失数',
			  `twoMonthLoseGuest` int(10) NOT NULL COMMENT '过去一个月游客流失总数',
			  `threeMonthLoseGuest` int(10) NOT NULL COMMENT '过去三个月游客流失总数',
			  `sixMonthLoseGuest` int(10) NOT NULL COMMENT '过去六个月游客流失总数',
			  `oneMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '1个月前那天访问的游客超过1个月没有访问的游客总数',
			  `oneMonthAgoRegGuest` int(10) NOT NULL COMMENT '1个月前那天访问的游客总数',
			  `twoMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '2个月前那天访问的2个月内没有访问的游客总数',
			  `twoMonthAgoRegGuest` int(10) NOT NULL COMMENT '2个月前那天访问的游客总数',
			  `threeMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '3个月前那天访问的超过3个月没有访问的游客总数',
			  `threeMonthAgoRegGuest` int(10) NOT NULL COMMENT '3个月前那天访问的新游客总数',
			  `sixMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '6个月前那天访问的超过6个月没有访问的游客总数',
			  `sixMonthAgoRegGuest` int(10) NOT NULL COMMENT '6个月前那天访问的新游客总数',
			  `date` date NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游客统计表' AUTO_INCREMENT=1 ;
		");

 		


		$this->addSql("
	        CREATE TABLE IF NOT EXISTS `user_state` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `totalUser` int(10) NOT NULL COMMENT '用户总数',
			  `dayRegUser` int(10) NOT NULL COMMENT '日用户注册总数',
			  `dayActUser` int(10) NOT NULL COMMENT '日用户活跃总数',
			  `oneWeekActUser` int(10) NOT NULL COMMENT '过去一周活跃用户数',
			  `oneMonthActUser` int(10) NOT NULL COMMENT '过去一月活跃用户数',
			  `oneMonthLoseUser` int(10) NOT NULL COMMENT '过去一月用户流失数',
			  `twoMonthLoseUser` int(10) NOT NULL COMMENT '过去一个月用户流失总数',
			  `threeMonthLoseUser` int(10) NOT NULL COMMENT '过去三个月用户流失总数',
			  `sixMonthLoseUser` int(10) NOT NULL COMMENT '过去六个月用户流失总数',
			  `oneMonthAgoLoseNewUser` int(10) NOT NULL COMMENT '1个月前那天注册的用户超过1个月没有登录的用户总数',
			  `oneMonthAgoRegUser` int(10) NOT NULL COMMENT '1个月前那天注册的用户总数',
			  `twoMonthAgoLoseNewUser` int(10) NOT NULL COMMENT '2个月前那天注册的2个月内没有登录的用户总数',
			  `twoMonthAgoRegUser` int(10) NOT NULL COMMENT '2个月前那天注册的用户总数',
			  `threeMonthAgoLoseNewUser` int(10) NOT NULL COMMENT '3个月前那天注册的超过3个月没有登录的用户总数',
			  `threeMonthAgoRegUser` int(10) NOT NULL COMMENT '3个月前那天注册的新用户总数',
			  `sixMonthAgoLoseNewUser` int(10) NOT NULL COMMENT '6个月前那天注册的超过6个月没有登录的用户总数',
			  `sixMonthAgoRegUser` int(10) NOT NULL COMMENT '6个月前那天注册的新用户总数',
			  `date` date NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户统计表' AUTO_INCREMENT=1 ;
		");


		$this->addSql("
			CREATE TABLE IF NOT EXISTS `access_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `guestId` int(11) NOT NULL DEFAULT '0'  COMMENT '游客Id',
			  `userId` int(11) NOT NULL DEFAULT '0'  COMMENT '用户Id',
			  `accessUri` varchar(1024) NOT NULL,
			  `accessUriName` varchar(255) NOT NULL,			 
			  `createdIp` varchar(64) NOT NULL,
			  `createdTime` int(11) NOT NULL DEFAULT '0',
			  `createdTookeen` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8　COMMENT='游客表' AUTO_INCREMENT=1 ;
		");

		$this->addSql("
	        CREATE TABLE IF NOT EXISTS `orders_state` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `totalOrders` int(10) NOT NULL COMMENT '订单总数',
			  `dayNewOrders` int(10) NOT NULL COMMENT '日新增订单数',
			  `dayActGuest` int(10) NOT NULL COMMENT '日新增收费订单数',
			  `oneWeekActGuest` int(10) NOT NULL COMMENT '过去一周活跃游客数',
			  `oneMonthActGuest` int(10) NOT NULL COMMENT '过去一月活跃游客数',
			  `oneMonthLoseGuest` int(10) NOT NULL COMMENT '过去一月游客流失数',
			  `twoMonthLoseGuest` int(10) NOT NULL COMMENT '过去一个月游客流失总数',
			  `threeMonthLoseGuest` int(10) NOT NULL COMMENT '过去三个月游客流失总数',
			  `sixMonthLoseGuest` int(10) NOT NULL COMMENT '过去六个月游客流失总数',
			  `oneMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '1个月前那天注册的游客超过1个月没有登录的游客总数',
			  `oneMonthAgoRegGuest` int(10) NOT NULL COMMENT '1个月前那天注册的游客总数',
			  `twoMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '2个月前那天注册的2个月内没有登录的游客总数',
			  `twoMonthAgoRegGuest` int(10) NOT NULL COMMENT '2个月前那天注册的游客总数',
			  `threeMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '3个月前那天注册的超过3个月没有登录的游客总数',
			  `threeMonthAgoRegGuest` int(10) NOT NULL COMMENT '3个月前那天注册的新游客总数',
			  `sixMonthAgoLoseNewGuest` int(10) NOT NULL COMMENT '6个月前那天注册的超过6个月没有登录的游客总数',
			  `sixMonthAgoRegGuest` int(10) NOT NULL COMMENT '6个月前那天注册的新游客总数',
			  `date` date NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游客统计表' AUTO_INCREMENT=1 ;
		");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
