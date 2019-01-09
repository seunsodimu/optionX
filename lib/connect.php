<?php
$mysqli = new mysqli("localhost", "optionex_admin", "4pata34nasa25na!", "optionex_db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>
