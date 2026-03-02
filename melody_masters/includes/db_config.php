<?php
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "melody_masters";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Character set එක UTF8 වලට සැකසීම (සිංහල හෝ වෙනත් සංකේත සඳහා)
$conn->set_charset("utf8mb4");
?>