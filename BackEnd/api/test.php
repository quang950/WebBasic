<?php
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['test' => 'OK', 'timestamp' => date('Y-m-d H:i:s')]);
?>
