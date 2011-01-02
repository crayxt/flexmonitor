
# Table structure for table `features`

CREATE TABLE `features` (
  `id` int(15) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `licenses`

CREATE TABLE `licenses` (
  `serverid` int(10) unsigned NOT NULL,
  `portid` int(10) unsigned NOT NULL,
  `productid` int(10) unsigned NOT NULL,
  `siteid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL auto_increment,
  `typeid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `licenses_available`

CREATE TABLE `licenses_available` (
  `id` int(15) unsigned NOT NULL auto_increment,
  `date` date NOT NULL,
  `featureid` int(10) unsigned NOT NULL,
  `num_licenses` int(11) unsigned NOT NULL,
  `licid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `featurid_date` (`date`,`featureid`,`licid`),
  KEY `licid` (`licid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `licenses_usage`

CREATE TABLE `licenses_usage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `featureid` smallint(5) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `users` smallint(5) unsigned NOT NULL,
  `licid` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `featureid_licid` (`featureid`,`licid`,`date`,`users`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `ports`

CREATE TABLE `ports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `port` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `port` (`port`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `products`

CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `servers`

CREATE TABLE `servers` (
  `id` int(11) NOT NULL auto_increment,
  `hostname` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `hostname` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `sites`

CREATE TABLE `sites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(40) NOT NULL,
  `Code` char(3) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Table structure for table `types`

CREATE TABLE `types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

# Data for the table `types`

insert  into `types`(`type`) values ('FlexLM'),('LMX'),('LUM'),('Other');