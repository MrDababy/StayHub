<?php
$conn = new mysqli('localhost', 'root', '', 'stayhub_db');
if ($conn->connect_error) {
    die('DB FAILED: ' . $conn->connect_error);
}
$tables = [];
$res = $conn->query('SHOW TABLES');
while ($row = $res->fetch_row()) {
    $tables[] = $row[0];
}
$schema = ['tables' => $tables, 'columns' => []];
foreach ($tables as $table) {
    $res = $conn->query('SHOW COLUMNS FROM ' . $table);
    $schema['columns'][$table] = [];
    while ($col = $res->fetch_assoc()) {
        $schema['columns'][$table][] = $col;
    }
}
header('Content-Type: application/json');
echo json_encode($schema, JSON_PRETTY_PRINT);
?>