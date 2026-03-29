<?php
header('Content-Type: application/json');

require_once '../../../config/db_connect.php';
require_once '../../../controllers/ProductController.php';

$controller = new ProductController($conn);

echo json_encode(
	$controller->handleCreate($_POST)
);