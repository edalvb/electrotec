<?php
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('MYSQL_USER') ?: 'app_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'electrodb12345';
$dbname = getenv('MYSQL_DATABASE') ?: 'electrodb_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión MySQL OK!";
}
