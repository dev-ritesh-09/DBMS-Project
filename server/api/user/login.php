<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['email', 'password']);

$email = strtolower(trim((string) $input['email']));
$password = (string) $input['password'];

try {
	$pdo = getDbConnection();

	$stmt = $pdo->prepare(
		'SELECT user_id, name, email, password, role, created_at FROM user WHERE email = :email LIMIT 1'
	);
	$stmt->execute(['email' => $email]);
	$user = $stmt->fetch();

	if (!$user) {
		jsonResponse(false, 'Invalid email or password', null, 401);
	}

	$storedPassword = (string) $user['password'];
	$isValid = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

	if (!$isValid) {
		jsonResponse(false, 'Invalid email or password', null, 401);
	}

	jsonResponse(true, 'Login successful', [
		'user' => [
			'user_id' => (int) $user['user_id'],
			'name' => $user['name'],
			'email' => $user['email'],
			'role' => $user['role'],
			'created_at' => $user['created_at'],
		],
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to login', ['error' => $e->getMessage()], 500);
}
