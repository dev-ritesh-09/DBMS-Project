<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

$documentId = isset($_GET['document_id']) ? (int) $_GET['document_id'] : 0;
$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($documentId <= 0) {
	jsonResponse(false, 'document_id is required', null, 422);
}

try {
	$pdo = getDbConnection();

	if ($userId > 0 && !hasDocumentPermission($pdo, $userId, $documentId, 'view')) {
		jsonResponse(false, 'You do not have permission to view versions', null, 403);
	}

	$stmt = $pdo->prepare(
		'SELECT v.version_id, v.document_id, v.modified_by, u.name AS modified_by_name, v.modified_date, v.content
		 FROM version v
		 JOIN user u ON v.modified_by = u.user_id
		 WHERE v.document_id = :document_id
		 ORDER BY v.modified_date DESC, v.version_id DESC'
	);
	$stmt->execute(['document_id' => $documentId]);
	$versions = $stmt->fetchAll();

	jsonResponse(true, 'Versions fetched successfully', [
		'versions' => $versions,
		'count' => count($versions),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch versions', ['error' => $e->getMessage()], 500);
}
