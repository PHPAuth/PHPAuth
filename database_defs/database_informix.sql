DROP TABLE phpauth_config;
CREATE TABLE phpauth_config (
  setting varchar(100) NOT NULL,
  value varchar(100) DEFAULT NULL,
  PRIMARY KEY (setting)
);

INSERT INTO phpauth_config (setting, value) VALUES ('attack_mitigation_time',  '+30 minutes');
INSERT INTO phpauth_config (setting, value) VALUES ('attempts_before_ban', '30');
INSERT INTO phpauth_config (setting, value) VALUES ('attempts_before_verify',  '5');
INSERT INTO phpauth_config (setting, value) VALUES ('bcrypt_cost', '10');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_domain', NULL);
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_forget', '+30 minutes');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_http', '1');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_name', 'phpauth_session_cookie');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_path', '/');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_remember', '+1 month');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_samesite', 'Strict');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_secure', '1');
INSERT INTO phpauth_config (setting, value) VALUES ('cookie_renew', '+5 minutes');
INSERT INTO phpauth_config (setting, value) VALUES ('allow_concurrent_sessions', FALSE);
INSERT INTO phpauth_config (setting, value) VALUES ('emailmessage_suppress_activation',  '0');
INSERT INTO phpauth_config (setting, value) VALUES ('emailmessage_suppress_reset', '0');
INSERT INTO phpauth_config (setting, value) VALUES ('mail_charset','UTF-8');
INSERT INTO phpauth_config (setting, value) VALUES ('password_min_score',  '3');
INSERT INTO phpauth_config (setting, value) VALUES ('site_activation_page',  'activate');
INSERT INTO phpauth_config (setting, value) VALUES ('site_activation_page_append_code', '0');
INSERT INTO phpauth_config (setting, value) VALUES ('site_email',  'no-reply@example.com');
INSERT INTO phpauth_config (setting, value) VALUES ('site_key',  'fghuior.)/!/jdUkd8s2!7HVHG7777ghg');
INSERT INTO phpauth_config (setting, value) VALUES ('site_name', 'PHPAuth');
INSERT INTO phpauth_config (setting, value) VALUES ('site_password_reset_page',  'reset');
INSERT INTO phpauth_config (setting, value) VALUES ('site_password_reset_page_append_code', '0');
INSERT INTO phpauth_config (setting, value) VALUES ('site_timezone', 'Europe/Paris');
INSERT INTO phpauth_config (setting, value) VALUES ('site_url',  'https://github.com/PHPAuth/PHPAuth');
INSERT INTO phpauth_config (setting, value) VALUES ('site_language', 'en_GB'),
INSERT INTO phpauth_config (setting, value) VALUES ('smtp',  '1');
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_auth', '0');
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_host', 'smtp.example.com');
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_password', 'password');
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_port', '25');
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_security', NULL);
INSERT INTO phpauth_config (setting, value) VALUES ('smtp_username', 'email@example.com');
INSERT INTO phpauth_config (setting, value) VALUES ('table_attempts',  'phpauth_attempts');
INSERT INTO phpauth_config (setting, value) VALUES ('table_requests',  'phpauth_requests');
INSERT INTO phpauth_config (setting, value) VALUES ('table_sessions',  'phpauth_sessions');
INSERT INTO phpauth_config (setting, value) VALUES ('table_users', 'phpauth_users');
INSERT INTO phpauth_config (setting, value) VALUES ('table_emails_banned', 'phpauth_emails_banned');
INSERT INTO phpauth_config (setting, value) VALUES ('table_translations', 'phpauth_translation_dictionary'),
INSERT INTO phpauth_config (setting, value) VALUES ('verify_email_max_length', '100');
INSERT INTO phpauth_config (setting, value) VALUES ('verify_email_min_length', '5');
INSERT INTO phpauth_config (setting, value) VALUES ('verify_email_use_banlist',  '1');
INSERT INTO phpauth_config (setting, value) VALUES ('verify_password_min_length',  '3');
INSERT INTO phpauth_config (setting, value) VALUES ('request_key_expiration', '+10 minutes');
INSERT INTO phpauth_config (setting, value) VALUES ('translation_source', 'php');
INSERT INTO phpauth_config (setting, value) VALUES ('recaptcha_enabled', 0);
INSERT INTO phpauth_config (setting, value) VALUES ('recaptcha_site_key', '');
INSERT INTO phpauth_config (setting, value) VALUES ('recaptcha_secret_key', 'php');
INSERT INTO phpauth_config (setting, value) VALUES ('custom_datetime_format', 'Y-m-d H:i');

DROP TABLE phpauth_attempts;
CREATE TABLE phpauth_attempts (
  id SERIAL,
  ip varchar(39) NOT NULL,
  expiredate DATETIME YEAR TO SECOND,
  PRIMARY KEY (id)
);

DROP TABLE phpauth_requests;
CREATE TABLE phpauth_requests (
  id SERIAL,
  uid integer NOT NULL,
  token varchar(20) NOT NULL,
  expire DATETIME YEAR TO SECOND,
  type varchar(20) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE phpauth_sessions;
CREATE TABLE phpauth_sessions (
  id SERIAL,
  uid integer NOT NULL,
  hash varchar(40) NOT NULL,
  expiredate DATETIME YEAR TO SECOND,
  ip varchar(39) NOT NULL,
  device_id varchar(36) DEFAULT NULL,
  agent varchar(200) NOT NULL,
  cookie_crc char(40) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE phpauth_users;
CREATE TABLE phpauth_users (
  id SERIAL,
  email varchar(100) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  isactive smallint DEFAULT 0 NOT NULL,
  dt DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
  PRIMARY KEY (id)
);

DROP TABLE phpauth_emails_banned;
CREATE TABLE phpauth_emails_banned (
  id serial NOT NULL,
  domain character varying(100) DEFAULT NULL,
  PRIMARY KEY (id)
);
