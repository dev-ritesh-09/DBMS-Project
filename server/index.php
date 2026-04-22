<?php
declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

$routes = [
	'POST /api/user/register.php',
	'POST /api/user/login.php',
	'GET /api/user/get_users.php',
	'POST /api/document/create.php',
	'GET /api/document/get.php',
	'POST /api/document/update.php',
	'POST /api/document/delete.php',
	'GET /api/document/version.php',
	'POST /api/folder/create.php',
	'GET /api/folder/get.php',
	'POST /api/folder/move.php',
	'POST /api/collaboration/share.php',
	'POST /api/collaboration/permission.php',
	'POST /api/comment/add.php',
	'GET /api/comment/get.php',
	'POST /api/activity/log.php',
	'GET /api/activity/get.php',
];

jsonResponse(true, 'Backend is running', [
	'service' => 'multi-user-document-editor-api',
	'date' => date('c'),
	'routes' => $routes,
]);
