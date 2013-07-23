DROP TABLE IF EXISTS `{{Location}}`;
CREATE TABLE `{{Location}}` (
	`id`				bigint unsigned not null auto_increment,
	`country`			varchar(2) not null default '',
	`state`				varchar(5) not null default '',
	`city`				varchar(250) not null default '',
	primary key(`id`)
) engine = MyISAM;