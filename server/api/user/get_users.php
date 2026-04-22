<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
	$pdo = getDbConnection();
	$stmt = $pdo->query('SELECT user_id, name, email, role, created_at FROM user ORDER BY user_id DESC');
	$users = $stmt->fetchAll();

	jsonResponse(true, 'Users fetched successfully', [
		'users' => $users,
		'count' => count($users),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch users', ['error' => $e->getMessage()], 500);
}
