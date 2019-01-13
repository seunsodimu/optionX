<?php
$mysqli = new mysqli("localhost", "optionex_admin", "4pata34nasa25na!", "optionex_db");
//$mysqli = new mysqli("localhost", "root", "godzilla", "carmax");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
