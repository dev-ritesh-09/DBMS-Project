USE multi_user_editor;

INSERT INTO user (name, email, password, role, created_at)
VALUES
('Ritesh', 'ritesh@example.com', '$2y$10$MZ8bnizQx2Faj5gVxJbWjO/ZZ6HnWV7kNfWBw46I1A9OWREfGp5bW', 'Admin', NOW()),
('Kajal', 'kajal@example.com', '$2y$10$MZ8bnizQx2Faj5gVxJbWjO/ZZ6HnWV7kNfWBw46I1A9OWREfGp5bW', 'Editor', NOW()),
('Rudra', 'rudra@example.com', '$2y$10$MZ8bnizQx2Faj5gVxJbWjO/ZZ6HnWV7kNfWBw46I1A9OWREfGp5bW', 'Editor', NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), role = VALUES(role);

INSERT INTO document (title, created_date, last_modified, document_status, owner_id)
VALUES
('Project BRD', NOW(), NOW(), 'Active', 1),
('Sprint Notes', NOW(), NOW(), 'Active', 2);

INSERT INTO version (document_id, modified_by, modified_date, content)
VALUES
(1, 1, NOW(), 'Initial BRD draft'),
(2, 2, NOW(), 'Initial sprint notes');

INSERT INTO collaboration (user_id, document_id, permission_type, shared_date)
VALUES
(2, 1, 'Edit', NOW()),
(3, 1, 'View', NOW()),
(1, 2, 'Edit', NOW())
ON DUPLICATE KEY UPDATE permission_type = VALUES(permission_type), shared_date = VALUES(shared_date);

INSERT INTO folder (folder_name, created_by, created_date)
VALUES
('Team Docs', 1, NOW()),
('Meeting Notes', 2, NOW());

INSERT INTO document_folder (document_id, folder_id)
VALUES
(1, 1),
(2, 2);

INSERT INTO comment (user_id, document_id, comment_text, timestamp)
VALUES
(2, 1, 'Looks good. Add timeline section.', NOW()),
(3, 1, 'Read-only review completed.', NOW());

INSERT INTO activity_log (user_id, document_id, action_type, action_time)
VALUES
(1, 1, 'Create', NOW()),
(2, 1, 'Edit', NOW()),
(2, 1, 'Comment', NOW()),
(1, 1, 'Share', NOW());
