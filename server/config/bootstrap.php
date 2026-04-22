<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-User-Id');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function jsonResponse(bool $success, string $message, ?array $data = null, int $httpCode = 200): void
{
    http_response_code($httpCode);
    $response = [
        'success' => $success,
        'message' => $message,
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response, JSON_UNESCAPED_SLASHES);
    exit;
}

function requireMethod(string $method): void
{
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (strtoupper($requestMethod) !== strtoupper($method)) {
        jsonResponse(false, 'Method not allowed', null, 405);
    }
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        jsonResponse(false, 'Invalid JSON payload', null, 400);
    }

    return $decoded;
}

function requireFields(array $input, array $requiredFields): void
{
    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $input) || $input[$field] === '' || $input[$field] === null) {
            jsonResponse(false, sprintf('Missing required field: %s', $field), null, 422);
        }
    }
}

function normalizePermission(string $permissionType): string
{
    $permission = strtolower(trim($permissionType));
    if (!in_array($permission, ['view', 'edit'], true)) {
        jsonResponse(false, 'permission_type must be View or Edit', null, 422);
    }

    return ucfirst($permission);
}

function userExists(PDO $pdo, int $userId): bool
{
    $stmt = $pdo->prepare('SELECT user_id FROM user WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    return (bool) $stmt->fetchColumn();
}

function documentExists(PDO $pdo, int $documentId): bool
{
    $stmt = $pdo->prepare('SELECT document_id FROM document WHERE document_id = :document_id');
    $stmt->execute(['document_id' => $documentId]);
    return (bool) $stmt->fetchColumn();
}

function hasDocumentPermission(PDO $pdo, int $userId, int $documentId, string $requiredPermission = 'view'): bool
{
    $required = strtolower($requiredPermission);

    $ownerStmt = $pdo->prepare('SELECT owner_id FROM document WHERE document_id = :document_id');
    $ownerStmt->execute(['document_id' => $documentId]);
    $ownerId = $ownerStmt->fetchColumn();

    if ($ownerId !== false && (int) $ownerId === $userId) {
        return true;
    }

    $stmt = $pdo->prepare(
        'SELECT permission_type FROM collaboration WHERE user_id = :user_id AND document_id = :document_id LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'document_id' => $documentId,
    ]);

    $permissionType = $stmt->fetchColumn();
    if ($permissionType === false) {
        return false;
    }

    $permission = strtolower((string) $permissionType);
    if ($required === 'view') {
        return in_array($permission, ['view', 'edit'], true);
    }

    return $permission === 'edit';
}

function logActivity(PDO $pdo, int $userId, ?int $documentId, string $actionType): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO activity_log (user_id, document_id, action_type, action_time)
         VALUES (:user_id, :document_id, :action_type, NOW())'
    );

    $stmt->execute([
        'user_id' => $userId,
        'document_id' => $documentId,
        'action_type' => $actionType,
    ]);
}
