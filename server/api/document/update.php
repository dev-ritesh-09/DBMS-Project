<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['document_id', 'user_id']);

$documentId = (int) $input['document_id'];
$userId = (int) $input['user_id'];
$title = array_key_exists('title', $input) ? trim((string) $input['title']) : null;
$content = array_key_exists('content', $input) ? (string) $input['content'] : null;
$status = array_key_exists('document_status', $input) ? trim((string) $input['document_status']) : null;

if ($title === null && $content === null && $status === null) {
	jsonResponse(false, 'Nothing to update', null, 422);
}

try {
	$pdo = getDbConnection();

	if (!hasDocumentPermission($pdo, $userId, $documentId, 'edit')) {
		jsonResponse(false, 'You do not have permission to edit this document', null, 403);
	}

	$pdo->beginTransaction();

	$updates = [];
	$params = [
		'document_id' => $documentId,
	];

	if ($title !== null) {
		if ($title === '') {
			jsonResponse(false, 'Title cannot be empty', null, 422);
		}
		$updates[] = 'title = :title';
		$params['title'] = $title;
	}

	if ($status !== null) {
		$updates[] = 'document_status = :document_status';
		$params['document_status'] = $status;
	}

	if (!empty($updates)) {
		$updates[] = 'last_modified = NOW()';
		$stmt = $pdo->prepare('UPDATE document SET ' . implode(', ', $updates) . ' WHERE document_id = :document_id');
		$stmt->execute($params);
	}

	if ($content !== null) {
		$versionStmt = $pdo->prepare(
			'INSERT INTO version (document_id, modified_by, modified_date, content)
			 VALUES (:document_id, :modified_by, NOW(), :content)'
		);
		$versionStmt->execute([
			'document_id' => $documentId,
			'modified_by' => $userId,
			'content' => $content,
		]);

		$touchStmt = $pdo->prepare('UPDATE document SET last_modified = NOW() WHERE document_id = :document_id');
		$touchStmt->execute(['document_id' => $documentId]);
	}

	logActivity($pdo, $userId, $documentId, 'Edit');

	$pdo->commit();

	jsonResponse(true, 'Document updated successfully');
} catch (Throwable $e) {
	if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	jsonResponse(false, 'Unable to update document', ['error' => $e->getMessage()], 500);
}
