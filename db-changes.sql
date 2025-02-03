use estrosdb1;

CREATE TABLE users (
	google_id VARCHAR(255) PRIMARY KEY,
    email VARCHAR(255) 
);

ALTER TABLE airdefenses
ADD google_id VARCHAR(255);
ALTER TABLE airdefenses
ADD CONSTRAINT fk_google_id_airdefenses FOREIGN KEY (google_id) REFERENCES users(google_id) ON DELETE CASCADE;

ALTER TABLE launchers
ADD google_id VARCHAR(255);
ALTER TABLE launchers
ADD CONSTRAINT fk_google_id_launchers FOREIGN KEY (google_id) REFERENCES users(google_id) ON DELETE CASCADE;