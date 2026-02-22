-- CREATE DATABASE
CREATE DATABASE multi_user_editor;
USE multi_user_editor;

-- USER TABLE
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50),
    created_at DATETIME
);

-- DOCUMENT TABLE
CREATE TABLE document (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    created_date DATETIME,
    last_modified DATETIME,
    document_status VARCHAR(50),
    owner_id INT,
    FOREIGN KEY (owner_id) REFERENCES user(user_id)
);

-- COLLABORATION TABLE
CREATE TABLE collaboration (
    collaboration_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    document_id INT,
    permission_type VARCHAR(20),
    shared_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (document_id) REFERENCES document(document_id)
);

-- VERSION TABLE
CREATE TABLE version (
    version_id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT,
    modified_by INT,
    modified_date DATETIME,
    content TEXT,
    FOREIGN KEY (document_id) REFERENCES document(document_id),
    FOREIGN KEY (modified_by) REFERENCES user(user_id)
);

-- COMMENT TABLE
CREATE TABLE comment (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    document_id INT,
    comment_text TEXT,
    timestamp DATETIME,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (document_id) REFERENCES document(document_id)
);

-- ACTIVITY LOG TABLE
CREATE TABLE activity_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    document_id INT,
    action_type VARCHAR(50),
    action_time DATETIME,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (document_id) REFERENCES document(document_id)
);

-- FOLDER TABLE
CREATE TABLE folder (
    folder_id INT AUTO_INCREMENT PRIMARY KEY,
    folder_name VARCHAR(100),
    created_by INT,
    created_date DATETIME,
    FOREIGN KEY (created_by) REFERENCES user(user_id)
);

-- DOCUMENT FOLDER TABLE
CREATE TABLE document_folder (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT,
    folder_id INT,
    FOREIGN KEY (document_id) REFERENCES document(document_id),
    FOREIGN KEY (folder_id) REFERENCES folder(folder_id)
);

-- INSERT USER
INSERT INTO user(name,email,password,role,created_at)
VALUES ('Harsh','harsh@gmail.com','12345','Editor',NOW());

-- INSERT DOCUMENT
INSERT INTO document(title,created_date,last_modified,document_status,owner_id)
VALUES ('Project Report',NOW(),NOW(),'Active',1);

-- SHARE DOCUMENT
INSERT INTO collaboration(user_id,document_id,permission_type,shared_date)
VALUES (1,1,'Edit',NOW());

-- ADD VERSION
INSERT INTO version(document_id,modified_by,modified_date,content)
VALUES (1,1,NOW(),'Initial Content');

-- ADD COMMENT
INSERT INTO comment(user_id,document_id,comment_text,timestamp)
VALUES (1,1,'Good Work',NOW());

-- CREATE FOLDER
INSERT INTO folder(folder_name,created_by,created_date)
VALUES ('Project Files',1,NOW());

-- MOVE DOCUMENT TO FOLDER
INSERT INTO document_folder(document_id,folder_id)
VALUES (1,1);

-- ACTIVITY LOG
INSERT INTO activity_log(user_id,document_id,action_type,action_time)
VALUES (1,1,'Edited Document',NOW());

-- SELECT DOCUMENTS
SELECT * FROM document;

-- SELECT COLLABORATIONS
SELECT * FROM collaboration;

-- SELECT COMMENTS
SELECT * FROM comment;

-- SELECT ACTIVITY LOG
SELECT * FROM activity_log;