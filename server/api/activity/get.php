<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

$documentId = isset($_GET['document_id']) ? (int) $_GET['document_id'] : 0;
$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;

if ($limit <= 0 || $limit > 200) {
	$limit = 50;
}

try {
	$pdo = getDbConnection();

	$query =
		'SELECT a.log_id, a.user_id, u.name AS user_name, a.document_id, d.title AS document_title,
				a.action_type, a.action_time
		 FROM activity_log a
		 JOIN user u ON a.user_id = u.user_id
		 LEFT JOIN document d ON a.document_id = d.document_id';

	$conditions = [];
	$params = [];

	if ($documentId > 0) {
		$conditions[] = 'a.document_id = :document_id';
		$params['document_id'] = $documentId;
	}

	if ($userId > 0) {
		$conditions[] = 'a.user_id = :user_id';
		$params['user_id'] = $userId;
	}

	if (!empty($conditions)) {
		$query .= ' WHERE ' . implode(' AND ', $conditions);
	}

	$query .= ' ORDER BY a.action_time DESC, a.log_id DESC LIMIT :limit';

	$stmt = $pdo->prepare($query);
	foreach ($params as $key => $value) {
		$stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
	}
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->execute();

	$activities = $stmt->fetchAll();

	jsonResponse(true, 'Activities fetched successfully', [
		'activities' => $activities,
		'count' => count($activities),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch activities', ['error' => $e->getMessage()], 500);
}
