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

if ($ownerId === $targetUserId) {
	jsonResponse(false, 'Owner already has full access', null, 422);
}

try {
	$pdo = getDbConnection();

	$ownerStmt = $pdo->prepare('SELECT owner_id FROM document WHERE document_id = :document_id');
	$ownerStmt->execute(['document_id' => $documentId]);
	$actualOwnerId = $ownerStmt->fetchColumn();

	if ($actualOwnerId === false) {
		jsonResponse(false, 'Document not found', null, 404);
	}

	if ((int) $actualOwnerId !== $ownerId) {
		jsonResponse(false, 'Only owner can share document', null, 403);
	}

	if (!userExists($pdo, $targetUserId)) {
		jsonResponse(false, 'Target user not found', null, 404);
	}

	$existingStmt = $pdo->prepare(
		'SELECT collaboration_id FROM collaboration WHERE document_id = :document_id AND user_id = :user_id LIMIT 1'
	);
	$existingStmt->execute([
		'document_id' => $documentId,
		'user_id' => $targetUserId,
	]);
	$existingId = $existingStmt->fetchColumn();

	if ($existingId) {
		$updateStmt = $pdo->prepare(
			'UPDATE collaboration
			 SET permission_type = :permission_type, shared_date = NOW()
			 WHERE collaboration_id = :collaboration_id'
		);
		$updateStmt->execute([
			'permission_type' => $permissionType,
			'collaboration_id' => (int) $existingId,
		]);
	} else {
		$insertStmt = $pdo->prepare(
			'INSERT INTO collaboration (user_id, document_id, permission_type, shared_date)
			 VALUES (:user_id, :document_id, :permission_type, NOW())'
		);
		$insertStmt->execute([
			'user_id' => $targetUserId,
			'document_id' => $documentId,
			'permission_type' => $permissionType,
		]);
	}

	logActivity($pdo, $ownerId, $documentId, 'Share');

	jsonResponse(true, 'Document shared successfully', [
		'document_id' => $documentId,
		'user_id' => $targetUserId,
		'permission_type' => $permissionType,
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to share document', ['error' => $e->getMessage()], 500);
}
