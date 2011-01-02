ALTER TABLE `licenses_available` 
ADD UNIQUE INDEX `featurid_date` (`date` ASC, `featureid` ASC, `licid` ASC) 
, ADD INDEX `licid` (`licid` ASC) ;

ALTER TABLE `licenses_usage` CHANGE COLUMN `featureid` `featureid` SMALLINT(5) UNSIGNED NOT NULL  , CHANGE COLUMN `users` `users` SMALLINT(5) UNSIGNED NOT NULL  , CHANGE COLUMN `licid` `licid` SMALLINT(5) UNSIGNED NOT NULL  
, ADD INDEX `featureid_licid` (`featureid` ASC, `licid` ASC, `date` ASC, `users` ASC) ;