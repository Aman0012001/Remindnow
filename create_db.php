<?php
$mysqli = new mysqli('127.0.0.1', 'root', '');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}
if ($mysqli->query('CREATE DATABASE IF NOT EXISTS festival_db')) {
    echo 'Database created successfully' . PHP_EOL;
} else {
    echo 'Error creating database: ' . $mysqli->error . PHP_EOL;
}
$mysqli->close();
