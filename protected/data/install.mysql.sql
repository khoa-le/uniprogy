/**
 * Various access hashes
 */
DROP TABLE IF EXISTS `{{Hash}}`;
CREATE TABLE `{{Hash}}` (
	`hash`				char(32) not null default '',
	`type`				tinyint unsigned not null default 0,	
	`id`				bigint unsigned not null default 0,
	/* 0 - won't expire */
	`expire`			bigint unsigned not null default 0,
	
	primary key(`hash`)
) engine = MyISAM ;

DROP TABLE IF EXISTS `{{MailQueue}}`;
CREATE TABLE `{{MailQueue}}` (
	`id`				bigint unsigned not null auto_increment,
	`email`				varchar(250) not null default '',
	`template`			varchar(250) not null default '',
	`params`			text not null,
	
	primary key (`id`)
) engine = MyISAM;

DROP TABLE IF EXISTS `{{StorageBin}}`;
CREATE TABLE `{{StorageBin}}` (
	`id`				bigint unsigned not null auto_increment,
	`owner`				varchar(250) not null default '',
	`status`			tinyint unsigned not null default 0,
	`created`			bigint unsigned not null default 0,
	
	primary key(`id`)
) engine = MyISAM;

DROP TABLE IF EXISTS `{{StorageFile}}`;
CREATE TABLE `{{StorageFile}}` (
	`id`				bigint unsigned not null auto_increment,
	`binId`				bigint unsigned not null default 0,
	`name`				varchar(50) not null,
	`hash`				varchar(32) not null,
	`extension`			char(10) not null default '',
	`created`			bigint unsigned not null default 0,
	`size`				bigint unsigned not null default 0,
	
	primary key(`id`),
	index(`binId`)
) engine = MyISAM;