<?php
$mysqli = new mysqli("localhost", "root", "", "training_redis_cache_testing");
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Insert data into MySQL
$data = "Sample Data";
$mysqli->query("INSERT INTO data_store (value) VALUES ('$data')");

// Get last inserted ID
$last_id = $mysqli->insert_id;

// Insert data into Redis with the same ID
$redis->set($last_id, $data);

// Verify that the data is the same in both MySQL and Redis
$mysqlData = $mysqli->query("SELECT value FROM data_store WHERE id = $last_id")->fetch_assoc();
$redisData = $redis->get($last_id);

if ($mysqlData['value'] === $redisData) {
    echo "Data is the same in both MySQL and Redis.\n";
} else {
    echo "Data is different.\n";
}

// Update MySQL data
$newData = "Updated Data";
$mysqli->query("UPDATE data_store SET value = '$newData' WHERE id = $last_id");

// Check if the data is now different
$updatedMysqlData = $mysqli->query("SELECT value FROM data_store WHERE id = $last_id")->fetch_assoc();
if ($updatedMysqlData['value'] !== $redisData) {
    echo "Data is now different in MySQL and Redis.\n";
} else {
    echo "Data is still the same.\n";
}

$mysqli->close();
$redis->close();
?>
