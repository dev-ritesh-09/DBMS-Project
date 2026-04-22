CREATE DATABASE IF NOT EXISTS multi_user_editor;
USE multi_user_editor;

CREATE TABLE IF NOT EXISTS user (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role VARCHAR(50) NOT NULL DEFAULT 'Editor',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS document (
	document_id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	created_date DATETIME NOT NULL,
	last_modified DATETIME NOT NULL,
	document_status VARCHAR(50) NOT NULL DEFAULT 'Active',
	owner_id INT NOT NULL,
	CONSTRAINT fk_document_owner FOREIGN KEY (owner_id) REFERENCES user(user_id)
);

CREATE TABLE IF NOT EXISTS collaboration (
	collaboration_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	document_id INT NOT NULL,
	permission_type VARCHAR(20) NOT NULL,
	shared_date DATETIME NOT NULL,
	CONSTRAINT fk_collaboration_user FOREIGN KEY (user_id) REFERENCES user(user_id),
	CONSTRAINT fk_collaboration_document FOREIGN KEY (document_id) REFERENCES document(document_id),
	CONSTRAINT uq_collaboration_user_document UNIQUE (user_id, document_id)
);

CREATE TABLE IF NOT EXISTS version (
	version_id INT AUTO_INCREMENT PRIMARY KEY,
	document_id INT NOT NULL,
	modified_by INT NOT NULL,
	modified_date DATETIME NOT NULL,
	content TEXT,
	CONSTRAINT fk_version_document FOREIGN KEY (document_id) REFERENCES document(document_id),
	CONSTRAINT fk_version_user FOREIGN KEY (modified_by) REFERENCES user(user_id)
);

CREATE TABLE IF NOT EXISTS comment (
	comment_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	document_id INT NOT NULL,
	comment_text TEXT NOT NULL,
	timestamp DATETIME NOT NULL,
	CONSTRAINT fk_comment_user FOREIGN KEY (user_id) REFERENCES user(user_id),
	CONSTRAINT fk_comment_document FOREIGN KEY (document_id) REFERENCES document(document_id)
);

CREATE TABLE IF NOT EXISTS activity_log (
	log_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	document_id INT NULL,
	action_type VARCHAR(50) NOT NULL,
	action_time DATETIME NOT NULL,
	CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES user(user_id),
	CONSTRAINT fk_activity_document FOREIGN KEY (document_id) REFERENCES document(document_id)
);

CREATE TABLE IF NOT EXISTS folder (
	folder_id INT AUTO_INCREMENT PRIMARY KEY,
	folder_name VARCHAR(100) NOT NULL,
	created_by INT NOT NULL,
	created_date DATETIME NOT NULL,
	CONSTRAINT fk_folder_user FOREIGN KEY (created_by) REFERENCES user(user_id)
);

CREATE TABLE IF NOT EXISTS document_folder (
	id INT AUTO_INCREMENT PRIMARY KEY,
	document_id INT NOT NULL,
	folder_id INT NOT NULL,
	CONSTRAINT fk_document_folder_document FOREIGN KEY (document_id) REFERENCES document(document_id),
	CONSTRAINT fk_document_folder_folder FOREIGN KEY (folder_id) REFERENCES folder(folder_id)
);
