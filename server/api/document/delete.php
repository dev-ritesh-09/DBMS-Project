<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['document_id', 'user_id']);

$documentId = (int) $input['document_id'];
$userId = (int) $input['user_id'];

try {
	$pdo = getDbConnection();

	$ownerStmt = $pdo->prepare('SELECT owner_id FROM document WHERE document_id = :document_id');
	$ownerStmt->execute(['document_id' => $documentId]);
	$ownerId = $ownerStmt->fetchColumn();

	if ($ownerId === false) {
		jsonResponse(false, 'Document not found', null, 404);
	}

	if ((int) $ownerId !== $userId) {
		jsonResponse(false, 'Only the owner can delete this document', null, 403);
	}

	$pdo->beginTransaction();

	$pdo->prepare('DELETE FROM activity_log WHERE document_id = :document_id')->execute(['document_id' => $documentId]);
	$pdo->prepare('DELETE FROM comment WHERE document_id = :document_id')->execute(['document_id' => $documentId]);
	$pdo->prepare('DELETE FROM collaboration WHERE document_id = :document_id')->execute(['document_id' => $documentId]);
	$pdo->prepare('DELETE FROM document_folder WHERE document_id = :document_id')->execute(['document_id' => $documentId]);
	$pdo->prepare('DELETE FROM version WHERE document_id = :document_id')->execute(['document_id' => $documentId]);
	$pdo->prepare('DELETE FROM document WHERE document_id = :document_id')->execute(['document_id' => $documentId]);

	$pdo->commit();

	jsonResponse(true, 'Document deleted successfully');
} catch (Throwable $e) {
	if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	jsonResponse(false, 'Unable to delete document', ['error' => $e->getMessage()], 500);
}
