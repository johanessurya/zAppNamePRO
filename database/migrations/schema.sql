DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `sub_category`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `calendar`;

CREATE TABLE `category` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  company_id INT,
  title VARCHAR(60),
  abbreviation VARCHAR(20),
  description TEXT,
  color VARCHAR(6)
) ENGINE=MyISAM;

CREATE TABLE `sub_category` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT,
  title VARCHAR(60),
  abbreviation VARCHAR(20),
  description TEXT,

  FOREIGN KEY (category_id) REFERENCES category(id)
) ENGINE=MyISAM;

CREATE TABLE `users` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(80) UNIQUE,
  password VARCHAR(64),
  user_type VARCHAR(20),
  email VARCHAR(90) UNIQUE,
  show_weekends TINYINT(1),
  day_start_time DATETIME,
  day_end_time DATETIME,
  dt DATETIME,
  company_id INT,
  office_no INT,
  office_name VARCHAR(20),
  office_size INT,
  zip VARCHAR(10),
  first_login DATETIME,
  last_login DATETIME,
  login_count INT,
  expires DATETIME,
  reset_token VARCHAR(64)
) ENGINE=MyISAM;

CREATE TABLE `client`(
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  client_code INT,
  name VARCHAR(60),
  gender TINYINT(1),
  type VARCHAR(10),

  FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=MyISAM;

CREATE TABLE `calendar` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  category_id INT,
  sub_category_id INT,
  sub_sub_category_id INT,
  client_id INT,
  title VARCHAR(60),
  description TEXT,
  all_day TINYINT(1),
  start DATETIME,
  `end` DATETIME,
  duration INT,
  color VARCHAR(6),

  FOREIGN KEY (user_id) REFERENCES user(id),
  FOREIGN KEY (category_id) REFERENCES category(id),
  FOREIGN KEY (sub_category_id) REFERENCES category(id),
  FOREIGN KEY (sub_sub_category_id) REFERENCES category(id)
) ENGINE=MyISAM;
