<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['user_id', 'document_id', 'comment_text']);

$userId = (int) $input['user_id'];
$documentId = (int) $input['document_id'];
$commentText = trim((string) $input['comment_text']);

if ($commentText === '') {
	jsonResponse(false, 'comment_text cannot be empty', null, 422);
}

try {
	$pdo = getDbConnection();

	if (!hasDocumentPermission($pdo, $userId, $documentId, 'view')) {
		jsonResponse(false, 'You do not have permission to comment on this document', null, 403);
	}

	$stmt = $pdo->prepare(
		'INSERT INTO comment (user_id, document_id, comment_text, timestamp)
		 VALUES (:user_id, :document_id, :comment_text, NOW())'
	);
	$stmt->execute([
		'user_id' => $userId,
		'document_id' => $documentId,
		'comment_text' => $commentText,
	]);

	logActivity($pdo, $userId, $documentId, 'Comment');

	jsonResponse(true, 'Comment added successfully', [
		'comment_id' => (int) $pdo->lastInsertId(),
	], 201);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to add comment', ['error' => $e->getMessage()], 500);
}
