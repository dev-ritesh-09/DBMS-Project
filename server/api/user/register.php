<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('POST');

$input = getJsonInput();
requireFields($input, ['name', 'email', 'password']);

$name = trim((string) $input['name']);
$email = strtolower(trim((string) $input['email']));
$password = (string) $input['password'];
$role = trim((string) ($input['role'] ?? 'Editor'));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	jsonResponse(false, 'Invalid email address', null, 422);
}

if (strlen($password) < 5) {
	jsonResponse(false, 'Password must be at least 5 characters', null, 422);
}

try {
	$pdo = getDbConnection();

	$checkStmt = $pdo->prepare('SELECT user_id FROM user WHERE email = :email');
	$checkStmt->execute(['email' => $email]);
	if ($checkStmt->fetchColumn()) {
		jsonResponse(false, 'Email already registered', null, 409);
	}

	$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

	$stmt = $pdo->prepare(
		'INSERT INTO user (name, email, password, role, created_at)
		 VALUES (:name, :email, :password, :role, NOW())'
	);

	$stmt->execute([
		'name' => $name,
		'email' => $email,
		'password' => $hashedPassword,
		'role' => $role,
	]);

	$userId = (int) $pdo->lastInsertId();

	jsonResponse(true, 'User registered successfully', [
		'user_id' => $userId,
		'name' => $name,
		'email' => $email,
		'role' => $role,
	], 201);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to register user', ['error' => $e->getMessage()], 500);
}
