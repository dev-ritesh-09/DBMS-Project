<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['folder_name', 'created_by']);

$folderName = trim((string) $input['folder_name']);
$createdBy = (int) $input['created_by'];

if ($folderName === '') {
	jsonResponse(false, 'folder_name cannot be empty', null, 422);
}

try {
	$pdo = getDbConnection();

	if (!userExists($pdo, $createdBy)) {
		jsonResponse(false, 'User not found', null, 404);
	}

	$stmt = $pdo->prepare(
		'INSERT INTO folder (folder_name, created_by, created_date)
		 VALUES (:folder_name, :created_by, NOW())'
	);
	$stmt->execute([
		'folder_name' => $folderName,
		'created_by' => $createdBy,
	]);

	jsonResponse(true, 'Folder created successfully', [
		'folder_id' => (int) $pdo->lastInsertId(),
		'folder_name' => $folderName,
		'created_by' => $createdBy,
	], 201);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to create folder', ['error' => $e->getMessage()], 500);
}
