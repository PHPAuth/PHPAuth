DROP TABLE config;
CREATE TABLE config (
  setting varchar(100) NOT NULL,
  value varchar(100) DEFAULT NULL,
  PRIMARY KEY (setting)
);

INSERT INTO config (setting, value) VALUES ('attack_mitigation_time',  '+30 minutes');
INSERT INTO config (setting, value) VALUES ('attempts_before_ban', '30');
INSERT INTO config (setting, value) VALUES ('attempts_before_verify',  '5');
INSERT INTO config (setting, value) VALUES ('bcrypt_cost', '10');
INSERT INTO config (setting, value) VALUES ('cookie_domain', NULL);
INSERT INTO config (setting, value) VALUES ('cookie_forget', '+30 minutes');
INSERT INTO config (setting, value) VALUES ('cookie_http', '0');
INSERT INTO config (setting, value) VALUES ('cookie_name', 'authID');
INSERT INTO config (setting, value) VALUES ('cookie_path', '/');
INSERT INTO config (setting, value) VALUES ('cookie_remember', '+1 month');
INSERT INTO config (setting, value) VALUES ('cookie_secure', '0');
INSERT INTO config (setting, value) VALUES ('emailmessage_suppress_activation',  '0');
INSERT INTO config (setting, value) VALUES ('emailmessage_suppress_reset', '0');
INSERT INTO config (setting, value) VALUES ('mail_charset','UTF-8');
INSERT INTO config (setting, value) VALUES ('password_min_score',  '3');
INSERT INTO config (setting, value) VALUES ('site_activation_page',  'activate');
INSERT INTO config (setting, value) VALUES ('site_email',  'no-reply@phpauth.cuonic.com');
INSERT INTO config (setting, value) VALUES ('site_key',  'fghuior.)/!/jdUkd8s2!7HVHG7777ghg');
INSERT INTO config (setting, value) VALUES ('site_name', 'PHPAuth');
INSERT INTO config (setting, value) VALUES ('site_password_reset_page',  'reset');
INSERT INTO config (setting, value) VALUES ('site_timezone', 'Europe/Paris');
INSERT INTO config (setting, value) VALUES ('site_url',  'https://github.com/PHPAuth/PHPAuth');
INSERT INTO config (setting, value) VALUES ('smtp',  '1');
INSERT INTO config (setting, value) VALUES ('smtp_auth', '0');
INSERT INTO config (setting, value) VALUES ('smtp_host', 'smtp.example.com');
INSERT INTO config (setting, value) VALUES ('smtp_password', 'password');
INSERT INTO config (setting, value) VALUES ('smtp_port', '25');
INSERT INTO config (setting, value) VALUES ('smtp_security', NULL);
INSERT INTO config (setting, value) VALUES ('smtp_username', 'email@example.com');
INSERT INTO config (setting, value) VALUES ('table_attempts',  'attempts');
INSERT INTO config (setting, value) VALUES ('table_requests',  'requests');
INSERT INTO config (setting, value) VALUES ('table_sessions',  'sessions');
INSERT INTO config (setting, value) VALUES ('table_users', 'users');
INSERT INTO config (setting, value) VALUES ('verify_email_max_length', '100');
INSERT INTO config (setting, value) VALUES ('verify_email_min_length', '5');
INSERT INTO config (setting, value) VALUES ('verify_email_use_banlist',  '1');
INSERT INTO config (setting, value) VALUES ('verify_password_min_length',  '3');
INSERT INTO config (setting, value) VALUES ('request_key_expiration', '+10 minutes');

DROP TABLE attempts;
CREATE TABLE attempts (
  id SERIAL,
  ip varchar(39) NOT NULL,
  expiredate DATETIME YEAR TO SECOND,
  PRIMARY KEY (id)
);

DROP TABLE requests;
CREATE TABLE requests (
  id SERIAL,
  uid integer NOT NULL,
  rkey varchar(20) NOT NULL,
  expire DATETIME YEAR TO SECOND,
  type varchar(20) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE sessions;
CREATE TABLE sessions (
  id SERIAL,
  uid integer NOT NULL,
  hash varchar(40) NOT NULL,
  expiredate DATETIME YEAR TO SECOND,
  ip varchar(39) NOT NULL,
  agent varchar(200) NOT NULL,
  cookie_crc varchar(40) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE users;
CREATE TABLE users (
  id SERIAL,
  email varchar(100) DEFAULT NULL,
  password varchar(60) DEFAULT NULL,
  isactive smallint DEFAULT 0 NOT NULL,
  dt DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
  PRIMARY KEY (id)
);
