<?php
$mysqli = new mysqli('74.225.249.224', 'root', 'Chaitanya!123#', 'supermart');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
    echo "Connection successful!";
}
?>
