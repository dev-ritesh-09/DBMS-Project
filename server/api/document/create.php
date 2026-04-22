<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['title', 'owner_id']);

$title = trim((string) $input['title']);
$ownerId = (int) $input['owner_id'];
$content = trim((string) ($input['content'] ?? ''));
$status = trim((string) ($input['document_status'] ?? 'Active'));

if ($title === '') {
	jsonResponse(false, 'Title cannot be empty', null, 422);
}

try {
	$pdo = getDbConnection();

	if (!userExists($pdo, $ownerId)) {
		jsonResponse(false, 'Owner does not exist', null, 404);
	}

	$pdo->beginTransaction();

	$createStmt = $pdo->prepare(
		'INSERT INTO document (title, created_date, last_modified, document_status, owner_id)
		 VALUES (:title, NOW(), NOW(), :document_status, :owner_id)'
	);
	$createStmt->execute([
		'title' => $title,
		'document_status' => $status,
		'owner_id' => $ownerId,
	]);

	$documentId = (int) $pdo->lastInsertId();

	$versionStmt = $pdo->prepare(
		'INSERT INTO version (document_id, modified_by, modified_date, content)
		 VALUES (:document_id, :modified_by, NOW(), :content)'
	);
	$versionStmt->execute([
		'document_id' => $documentId,
		'modified_by' => $ownerId,
		'content' => $content,
	]);

	logActivity($pdo, $ownerId, $documentId, 'Create');

	$pdo->commit();

	jsonResponse(true, 'Document created successfully', [
		'document_id' => $documentId,
		'title' => $title,
		'owner_id' => $ownerId,
		'document_status' => $status,
	], 201);
} catch (Throwable $e) {
	if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	jsonResponse(false, 'Unable to create document', ['error' => $e->getMessage()], 500);
}
