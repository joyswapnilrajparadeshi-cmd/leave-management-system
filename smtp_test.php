<?php
$host = 'smtp.gmail.com';
$port = 587;
$timeout = 5;

$connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

if ($connection) {
    echo "Connection to $host on port $port successful!";
    fclose($connection);
} else {
    echo "Cannot connect to $host on port $port. Error $errno: $errstr";
}
?>
