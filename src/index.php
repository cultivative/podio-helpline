<?php

$data = ['message' => 'Invalid access point'];
header('Content-type:application/json;charset=utf-8');
echo json_encode($data);