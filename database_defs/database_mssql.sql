DROP TABLE IF EXISTS phpauth_config;
CREATE TABLE phpauth_config (
  id int NOT NULL IDENTITY(1,1),
  setting character varying(100) NOT NULL,
  value character varying(100) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE (setting)
);

INSERT INTO phpauth_config (setting, value) VALUES
('attack_mitigation_time',  '+30 minutes'),
('attempts_before_ban', '30'),
('attempts_before_verify',  '5'),
('bcrypt_cost', '10'),
('cookie_domain', NULL),
('cookie_forget', '+30 minutes'),
('cookie_http', '1'),
('cookie_name', 'phpauth_session_cookie'),
('cookie_path', '/'),
('cookie_remember', '+1 month'),
('cookie_samesite', 'Strict'),
('cookie_secure', '1'),
('cookie_renew', '+5 minutes'),
('allow_concurrent_sessions', FALSE),
('emailmessage_suppress_activation',  '0'),
('emailmessage_suppress_reset', '0'),
('mail_charset','UTF-8'),
('password_min_score',  '3'),
('site_activation_page',  'activate'),
('site_activation_page_append_code', '0'),
('site_email',  'no-reply@phpauth.cuonic.com'),
('site_key',  'fghuior.)/!/jdUkd8s2!7HVHG7777ghg'),
('site_name', 'PHPAuth'),
('site_password_reset_page',  'reset'),
('site_password_reset_page_append_code', '0'),
('site_timezone', 'Europe/Paris'),
('site_url',  'https://github.com/PHPAuth/PHPAuth'),
('site_language', 'en_GB'),
('smtp',  '0'),
('smtp_debug',  '0'),
('smtp_auth', '1'),
('smtp_host', 'smtp.example.com'),
('smtp_password', 'password'),
('smtp_port', '25'),
('smtp_security', NULL),
('smtp_username', 'email@example.com'),
('table_attempts',  'phpauth_attempts'),
('table_requests',  'phpauth_requests'),
('table_sessions',  'phpauth_sessions'),
('table_users', 'phpauth_users'),
('table_emails_banned', 'phpauth_emails_banned'),
('table_translations', 'phpauth_translation_dictionary'),
('verify_email_max_length', '100'),
('verify_email_min_length', '5'),
('verify_email_use_banlist',  '1'),
('verify_password_min_length',  '3'),
('request_key_expiration', '+10 minutes'),
('translation_source', 'php'),
('recaptcha_enabled', 0),
('recaptcha_site_key', ''),
('recaptcha_secret_key', ''),
('custom_datetime_format', 'Y-m-d H:i');

DROP TABLE IF EXISTS phpauth_attempts;
CREATE TABLE phpauth_attempts (
  id int NOT NULL IDENTITY(1,1),
  ip character(39) NOT NULL,
  expiredate datetime2 NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_requests;
CREATE TABLE phpauth_requests (
  id int NOT NULL IDENTITY(1,1),
  uid integer NOT NULL,
  token character (20) NOT NULL,
  expire datetime2 NOT NULL,
  type character varying (20) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_sessions;
CREATE TABLE phpauth_sessions (
  id int NOT NULL IDENTITY(1,1),
  uid integer NOT NULL,
  hash character varying(40) NOT NULL,
  expiredate datetime2 NOT NULL,
  ip character varying(39) NOT NULL,
  device_id character varying(36) DEFAULT NULL,
  agent character varying(200) NOT NULL,
  cookie_crc character (40) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_users;
CREATE TABLE phpauth_users (
  id int NOT NULL IDENTITY(1,1),
  email character varying(100) DEFAULT NULL,
  password character varying(255) DEFAULT NULL,
  isactive smallint NOT NULL DEFAULT '0',
  dt datetime2 NOT NULL DEFAULT GETDATE(),
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS phpauth_emails_banned;
CREATE TABLE phpauth_emails_banned (
  id int NOT NULL IDENTITY(1,1),
  domain character varying(100) DEFAULT NULL,
  PRIMARY KEY (id)
);
