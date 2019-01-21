<?php

require_once './vendor/autoload.php';

use Resty\Router;

function isPoint($str)
{
    return strpos($str, 'POINT') !== false;
}

function parsePoint($point) {
    $len = strlen($point);
    $coordinates = substr($point, 6, -1);
    $arr = explode(' ', $coordinates);
    return ['longitude' => floatval($arr[0]), 'latitude' => floatval($arr[1])];
}

function parseCoordinates($path)
{
    $handle = fopen($path, 'r');
    $arr = [];
    while ($data = fgetcsv($handle)) {
        $point = $data[0];
        $name = $data[1];
        if (isPoint($point)) {
            $arr[] = ['point' => parsePoint($point), 'name' => $name];
        }
    }
    return $arr;
}



$app = new Router();
$app->allowCors();

$app->get('/points', function ($request, $response) {
    $data1 = parseCoordinates('./data/data_1.csv');
    $data2 = parseCoordinates('./data/data_2.csv');
    $data = array_merge(['first' => $data1], ['second'=>$data2]);
    $response->json($data);
});

$app->serve();

