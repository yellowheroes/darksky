<?php
/**
 * Created by Yellow Heroes
 * Project: darksky
 * File: index.php
 * User: Robert
 * Date: 24/07/2019
 * Time: 19:04
 */

/*
 * set timezone to Paris
 */
date_default_timezone_set('Europe/Paris');
/*
 * reference to base directory
 */
$baseDir = dirname(__DIR__, 1);
require($baseDir . '/views/header.php');


// get all places on the planet
// store them in $arr
$target = $baseDir . "/assets/json/city.list.json";
$json = file_get_contents($target);
$arr = json_decode($json, true);

// allow user to choose a place(city) and country(code)
$form = <<<HEREDOC
<form method='POST'>
    <label for "name">name:</label>
    <input type="text" name="name" id="name" required />
    <label for "country">country:</label>
    <input type="text" name="country" id="country" value="FR" required />
    <input type="submit" name="submit" id="submit" value="submit" />
</form>
HEREDOC;

echo $form;

$name = $country = $id = "";
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $country = $_POST['country'];

    foreach ($arr as $key => $value) {
        if ($value['name'] == $name && $value['country'] == $country) {
            $lon = $value['coord']['lon']; // store city longitude, needed in URL
            $lat = $value['coord']['lat']; // store city latitude, needed in URL
            $idName = $value['name']; // store name of the place user wants weather data for
        }
    }
}

/*
 * new
 */

// https://api.darksky.net/forecast/[key]/[latitude],[longitude]
/*
 * to exclude data blocks from the response - e.g. exclude the minutely and hourly data points:
 * &exclude=minutely,hourly
 * (the datablocks that can be excluded:
 * currently, minutely, hourly, daily, alerts, flags
 */
if(isset($_POST['submit'])) {
    $params = "/?units=si&lang=en";
    $url = "https://api.darksky.net/forecast/cc2bcd44f8fc3686e76f1657f697335d/" . $lat . "," . $lon . $params;
//echo 'api endpoint : ' . $url;
//echo '<br />';
    echo 'place : ' . $idName;
    echo '<br />';
    $json = file_get_contents($url);
    $response = json_decode($json);

    $go = true;
    if ($go == true) {
        if ($response != null) {
            //Current Conditions
            $curTime = $response->currently->time;
            echo 'Time : ' . date('l Y-m-d H:i:s', $curTime);
            echo '<br />';
            $curSummary = $response->currently->summary;
            echo 'Summary : ' . $curSummary;
            echo '<br />';
            $curIcon = $response->currently->icon;
            $curPrecipProb = ($response->currently->precipProbability) * 100;
            if (isset($response->currently->precipType)) {
                $curPrecipType = $response->currently->precipType;
            }
            $curTemp = round($response->currently->temperature);
            echo 'current temperature : ' . $curTemp;
            echo '<br />';
            $curFeelsLike = round($response->currently->apparentTemperature);
            $curHumidity = ($response->currently->humidity) * 100;
            $curDewPoint = $response->currently->dewPoint;
            $curWindSpeed = $response->currently->windSpeed;
            echo 'Current wind speed : ' . $curWindSpeed;
            echo '<br />';
            $curWindBearing = $response->currently->windBearing;
            echo 'Current wind bearing : ' . $curWindBearing;
            echo '<br />';
            $curCloudCover = ($response->currently->cloudCover) * 100;
            $curPressure = round(($response->currently->pressure) * 0.0295301, 2);
            $curVis = round($response->currently->visibility);
            $curUvIndex = $response->currently->uvIndex;
            echo 'UV Index : ' . $curUvIndex;
            echo '<br />';
            }
        }
    }

echo "<div class='text-white'>";
echo "<pre class='text-white'>";
var_dump($response);
echo '</pre>';
echo "</div>";
/*
 * end new
 */

require($baseDir . '/views/footer.php');