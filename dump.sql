     CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) DEFAULT NULL, department VARCHAR(100) DEFAULT NULL, active TINYINT(1) DEFAULT '1' NOT NULL COMMENT 'boolean to describe status. true=active, false=inactive/deleted', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
