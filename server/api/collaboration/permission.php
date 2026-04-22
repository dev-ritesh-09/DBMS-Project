<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['document_id', 'owner_id', 'user_id', 'permission_type']);

$documentId = (int) $input['document_id'];
$ownerId = (int) $input['owner_id'];
$targetUserId = (int) $input['user_id'];
$permissionType = normalizePermission((string) $input['permission_type']);

try {
	$pdo = getDbConnection();

	$ownerStmt = $pdo->prepare('SELECT owner_id FROM document WHERE document_id = :document_id');
	$ownerStmt->execute(['document_id' => $documentId]);
	$actualOwnerId = $ownerStmt->fetchColumn();

	if ($actualOwnerId === false) {
		jsonResponse(false, 'Document not found', null, 404);
	}

	if ((int) $actualOwnerId !== $ownerId) {
		jsonResponse(false, 'Only owner can change permissions', null, 403);
	}

	$stmt = $pdo->prepare(
		'UPDATE collaboration
		 SET permission_type = :permission_type, shared_date = NOW()
		 WHERE document_id = :document_id AND user_id = :user_id'
	);
	$stmt->execute([
		'permission_type' => $permissionType,
		'document_id' => $documentId,
		'user_id' => $targetUserId,
	]);

	if ($stmt->rowCount() === 0) {
		jsonResponse(false, 'Collaboration entry not found', null, 404);
	}

	logActivity($pdo, $ownerId, $documentId, 'Permission Update');

	jsonResponse(true, 'Permission updated successfully', [
		'document_id' => $documentId,
		'user_id' => $targetUserId,
		'permission_type' => $permissionType,
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to update permission', ['error' => $e->getMessage()], 500);
}
