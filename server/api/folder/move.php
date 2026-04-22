<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['document_id', 'folder_id', 'user_id']);

$documentId = (int) $input['document_id'];
$folderId = (int) $input['folder_id'];
$userId = (int) $input['user_id'];

try {
	$pdo = getDbConnection();

	if (!hasDocumentPermission($pdo, $userId, $documentId, 'edit')) {
		jsonResponse(false, 'You do not have permission to move this document', null, 403);
	}

	$folderStmt = $pdo->prepare('SELECT folder_id FROM folder WHERE folder_id = :folder_id');
	$folderStmt->execute(['folder_id' => $folderId]);
	if (!$folderStmt->fetchColumn()) {
		jsonResponse(false, 'Folder not found', null, 404);
	}

	$pdo->beginTransaction();

	$pdo->prepare('DELETE FROM document_folder WHERE document_id = :document_id')->execute([
		'document_id' => $documentId,
	]);

	$insertStmt = $pdo->prepare(
		'INSERT INTO document_folder (document_id, folder_id)
		 VALUES (:document_id, :folder_id)'
	);
	$insertStmt->execute([
		'document_id' => $documentId,
		'folder_id' => $folderId,
	]);

	logActivity($pdo, $userId, $documentId, 'Move to Folder');
	$pdo->commit();

	jsonResponse(true, 'Document moved to folder successfully');
} catch (Throwable $e) {
	if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
		$pdo->rollBack();
	}
	jsonResponse(false, 'Unable to move document', ['error' => $e->getMessage()], 500);
}
