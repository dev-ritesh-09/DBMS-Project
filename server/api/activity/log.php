<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['user_id', 'action_type']);

$userId = (int) $input['user_id'];
$documentId = isset($input['document_id']) ? (int) $input['document_id'] : null;
$actionType = trim((string) $input['action_type']);

if ($actionType === '') {
	jsonResponse(false, 'action_type cannot be empty', null, 422);
}

try {
	$pdo = getDbConnection();

	if (!userExists($pdo, $userId)) {
		jsonResponse(false, 'User not found', null, 404);
	}

	if ($documentId !== null && $documentId > 0 && !documentExists($pdo, $documentId)) {
		jsonResponse(false, 'Document not found', null, 404);
	}

	logActivity($pdo, $userId, $documentId, $actionType);
	jsonResponse(true, 'Activity logged successfully', null, 201);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to log activity', ['error' => $e->getMessage()], 500);
}
