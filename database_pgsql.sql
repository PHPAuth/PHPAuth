DROP TABLE IF EXISTS config;
CREATE TABLE config (
  setting character varying(100) NOT NULL,
  value character varying(100) DEFAULT NULL,
  UNIQUE KEY setting (setting)
);

INSERT INTO config (setting, value) VALUES
('attack_mitigation_time',  '+30 minutes'),
('attempts_before_ban', '30'),
('attempts_before_verify',  '5'),
('bcrypt_cost', '10'),
('cookie_domain', NULL),
('cookie_forget', '+30 minutes'),
('cookie_http', '0'),
('cookie_name', 'authID'),
('cookie_path', '/'),
('cookie_remember', '+1 month'),
('cookie_secure', '0'),
('emailmessage_suppress_activation',  '0'),
('emailmessage_suppress_reset', '0'),
('mail_charset','UTF-8'),
('password_min_score',  '3'),
('site_activation_page',  'activate'),
('site_email',  'no-reply@phpauth.cuonic.com'),
('site_key',  'fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
('site_name', 'PHPAuth'),
('site_password_reset_page',  'reset'),
('site_timezone', 'Europe/Paris'),
('site_url',  'https://github.com/PHPAuth/PHPAuth'),
('smtp',  '0'),
('smtp_auth', '1'),
('smtp_host', 'smtp.example.com'),
('smtp_password', 'password'),
('smtp_port', '25'),
('smtp_security', NULL),
('smtp_username', 'email@example.com'),
('table_attempts',  'attempts'),
('table_requests',  'requests'),
('table_sessions',  'sessions'),
('table_users', 'users'),
('verify_email_max_length', '100'),
('verify_email_min_length', '5'),
('verify_email_use_banlist',  '1'),
('verify_password_min_length',  '3'),
('request_key_expiration', '+10 minutes');

DROP TABLE IF EXISTS attempts;
CREATE TABLE attempts (
  id serial NOT NULL,
  ip character varying(39) NOT NULL,
  expiredate timestamp without time zone NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS requests;
CREATE TABLE requests (
  id serial NOT NULL,
  uid integer NOT NULL,
  rkey character varying (20) NOT NULL,
  expire timestamp without time zone NOT NULL,
  type character varying (20) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
  id serial NOT NULL,
  uid integer NOT NULL,
  hash character varying(40) NOT NULL,
  expiredate timestamp without time zone NOT NULL,
  ip character varying(39) NOT NULL,
  agent character varying(200) NOT NULL,
  cookie_crc character varying(40) NOT NULL,
  PRIMARY KEY (id)
);


DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id serial NOT NULL,
  email character varying(100) DEFAULT NULL,
  password character varying(60) DEFAULT NULL,
  isactive smallint NOT NULL DEFAULT '0',
  dt timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
