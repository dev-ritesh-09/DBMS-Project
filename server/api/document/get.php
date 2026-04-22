<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

$documentId = isset($_GET['document_id']) ? (int) $_GET['document_id'] : null;
$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;

try {
	$pdo = getDbConnection();

	if ($documentId !== null && $documentId > 0) {
		if ($userId !== null && $userId > 0 && !hasDocumentPermission($pdo, $userId, $documentId, 'view')) {
			jsonResponse(false, 'You do not have permission to view this document', null, 403);
		}

		$stmt = $pdo->prepare(
			'SELECT d.document_id, d.title, d.created_date, d.last_modified, d.document_status, d.owner_id,
					u.name AS owner_name,
					(
						SELECT v.content
						FROM version v
						WHERE v.document_id = d.document_id
						ORDER BY v.modified_date DESC, v.version_id DESC
						LIMIT 1
					) AS latest_content
			 FROM document d
			 JOIN user u ON d.owner_id = u.user_id
			 WHERE d.document_id = :document_id'
		);
		$stmt->execute(['document_id' => $documentId]);
		$document = $stmt->fetch();

		if (!$document) {
			jsonResponse(false, 'Document not found', null, 404);
		}

		jsonResponse(true, 'Document fetched successfully', [
			'document' => $document,
		]);
	}

	if ($userId !== null && $userId > 0) {
		$stmt = $pdo->prepare(
			'SELECT DISTINCT d.document_id, d.title, d.created_date, d.last_modified, d.document_status, d.owner_id,
					u.name AS owner_name
			 FROM document d
			 JOIN user u ON d.owner_id = u.user_id
			 LEFT JOIN collaboration c ON c.document_id = d.document_id
			 WHERE d.owner_id = :user_id OR c.user_id = :user_id
			 ORDER BY d.last_modified DESC'
		);
		$stmt->execute(['user_id' => $userId]);
	} else {
		$stmt = $pdo->query(
			'SELECT d.document_id, d.title, d.created_date, d.last_modified, d.document_status, d.owner_id,
					u.name AS owner_name
			 FROM document d
			 JOIN user u ON d.owner_id = u.user_id
			 ORDER BY d.last_modified DESC'
		);
	}

	$documents = $stmt->fetchAll();

	jsonResponse(true, 'Documents fetched successfully', [
		'documents' => $documents,
		'count' => count($documents),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch documents', ['error' => $e->getMessage()], 500);
}
