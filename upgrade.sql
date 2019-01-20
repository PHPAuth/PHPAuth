DELETE FROM phpauth_attempts;
DELETE FROM phpauth_sessions;

ALTER TABLE phpauth_attempts DROP id;
ALTER TABLE phpauth_attempts CHANGE ip ip_hash BINARY(16) NOT NULL;
ALTER TABLE phpauth_sessions CHANGE ip ip_hash BINARY(16) NOT NULL;
ALTER TABLE phpauth_sessions CHANGE agent user_agent_hash BINARY(16) NOT NULL;