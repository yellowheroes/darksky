<?php
/**
 * Created by Yellow Heroes
 * Project: darksky
 * File: latlong.php
 * User: Robert
 * Date: 27/07/2019
 * Time: 22:22
 */

// get access to all cities on the planet...
// We retrieve city-id from this array, and use it in our URL call to the API
$baseDir = dirname(__DIR__, 1);
$target = $baseDir . "/src/assets/json/city.list.json";
echo $target;
$json = file_get_contents($target);
$arr = json_decode($json, true);
echo '<pre>';
var_dump($arr);
echo '</pre>';