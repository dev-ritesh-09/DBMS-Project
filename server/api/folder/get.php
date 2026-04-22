<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

try {
	$pdo = getDbConnection();

	if ($userId > 0) {
		$stmt = $pdo->prepare(
			'SELECT f.folder_id, f.folder_name, f.created_by, f.created_date,
					COUNT(df.document_id) AS document_count
			 FROM folder f
			 LEFT JOIN document_folder df ON f.folder_id = df.folder_id
			 WHERE f.created_by = :user_id
			 GROUP BY f.folder_id, f.folder_name, f.created_by, f.created_date
			 ORDER BY f.created_date DESC'
		);
		$stmt->execute(['user_id' => $userId]);
	} else {
		$stmt = $pdo->query(
			'SELECT f.folder_id, f.folder_name, f.created_by, f.created_date,
					COUNT(df.document_id) AS document_count
			 FROM folder f
			 LEFT JOIN document_folder df ON f.folder_id = df.folder_id
			 GROUP BY f.folder_id, f.folder_name, f.created_by, f.created_date
			 ORDER BY f.created_date DESC'
		);
	}

	$folders = $stmt->fetchAll();

	jsonResponse(true, 'Folders fetched successfully', [
		'folders' => $folders,
		'count' => count($folders),
	]);
} catch (Throwable $e) {
	jsonResponse(false, 'Unable to fetch folders', ['error' => $e->getMessage()], 500);
}
