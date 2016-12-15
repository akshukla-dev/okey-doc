DROP TABLE IF EXISTS `#__okeydoc_document`;
CREATE TABLE `#__okeydoc_document` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` TINYTEXT NOT NULL,
  `alias` VARCHAR(80) NOT NULL ,
  `introtext` TEXT NOT NULL,
  `fulltext` TEXT NOT NULL,
  `file` TINYTEXT NOT NULL,
  `file_name` TINYTEXT NOT NULL,
  `file_type` TINYTEXT NOT NULL,
  `file_size` TINYTEXT NOT NULL,
  `file_path` TINYTEXT NOT NULL,
  `file_location` TINYTEXT NOT NULL,
  `file_icon` TINYTEXT NOT NULL,
  `folder_id` INT NOT NULL,
  `author` TINYTEXT NOT NULL,
  `catid` INT NOT NULL DEFAULT 0,
  `hits` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `downloads` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `published` TINYINT NOT NULL DEFAULT 0 ,
  `checked_out` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `access` TINYINT NOT NULL DEFAULT 0 ,
  `asset_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `ordering` INT NOT NULL,
  `created_by` INT UNSIGNED NOT NULL ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT UNSIGNED NOT NULL ,
  `params` TEXT NOT NULL ,
  `metakey` TEXT NOT NULL ,
  `metadesc` TEXT NOT NULL ,
  `metadata` TEXT NOT NULL ,
  `language` CHAR(7) NOT NULL,
  PRIMARY KEY  (`id`) )
ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__okeydoc_folder`;
CREATE TABLE `#__okeydoc_folder` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` TINYTEXT NOT NULL,
  `description` TEXT NOT NULL,
  `files` INT UNSIGNED NOT NULL DEFAULT 0,
  `symlink_path` TINYTEXT NOT NULL,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` INT UNSIGNED NOT NULL ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT UNSIGNED NOT NULL ,
  `checked_out` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY  (`id`) )
ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__okeydoc_folder_map`;
CREATE TABLE `#__okeydoc_folder_map` (
  `folder_id` INT UNSIGNED NOT NULL,
  `catid` INT UNSIGNED NOT NULL )
ENGINE = MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__okeydoc_doc_map`;
CREATE TABLE `#__okeydoc_doc_map` (
  `doc_id` INT UNSIGNED NOT NULL,
  `item_id` INT UNSIGNED NOT NULL,
  `item_type` CHAR(8) NOT NULL )
ENGINE = MyISAM DEFAULT CHARSET=utf8;

