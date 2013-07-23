DROP TABLE IF EXISTS `{{User}}`;
CREATE TABLE `{{User}}` (
	`id`				bigint unsigned not null auto_increment,
	`email`				varchar(250),
	/* salted-md5-encrypted password */
	`password`			varchar(32),
	/* password salt */
	`salt`				char(3),
	/* 0 - nothing, 1 - user password needs to be updated */
	`changePassword`	tinyint unsigned,
	`role`				varchar(50) not null default 'user',
	`created`			bigint unsigned,
	`firstName`			varchar(250),
	`lastName`			varchar(250),
	`avatar`			bigint unsigned,
	`timeZone`			double default 0,
	
	primary key (`id`),
	unique(`email`)
) engine = MyISAM ;