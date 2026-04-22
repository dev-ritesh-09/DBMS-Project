<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

$documentId = isset($_GET['document_id']) ? (int) $_GET['document_id'] : 0;

if ($documentId <= 0) {
	jsonResponse(false, 'document_id is required', null, 422);
}

try {
	$pdo = getDbConnection();
	$stmt = $pdo->prepare(
		'SELECT c.comment_id, c.user_id, u.name AS user_name, c.document_id, c.comment_text, c.timestamp
		 FROM comment c
		 JOIN user u ON c.user_id = u.user_id
		 WHERE c.document_id = :document_id
		 ORDER BY c.timestamp DESC, c.comment_id DESC'
	);
	$stmt->execute(['document_id' => $documentId]);
	$comments = $stmt->fetchAll();

	jsonResponse(true, 'Comments fetched successfully', [
		'comments' => $comments,
		'count' => count($comments),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch comments', ['error' => $e->getMessage()], 500);
}
