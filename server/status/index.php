<?php

$status["active"] = true;

header('Content-Type: application/json');

echo json_encode($status);
//var_dump($status)
?>
