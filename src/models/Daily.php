<!doctype html>
<html lang="en">
<head>

    <!-- required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
          integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400">
    <link rel="stylesheet" href="https://www.yellowheroes.com/jimmy/src/system/assets/css/sticky-footer-navbar.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.0.0/slate/bootstrap.min.css">
    <link rel="stylesheet" href="https://www.yellowheroes.com/jimmy/src/system/assets/css/super.css">
    <link rel="stylesheet" href="https://www.yellowheroes.com/jimmy/src/system/assets/css/scroll-to-top.css">
    <!-- weather icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/weather-icons/2.0.9/css/weather-icons.min.css"
          integrity="sha256-KcCcakqMaamBrTFaxb9tkrP2iq1X8vUnsm86W8pRcgI=" crossorigin="anonymous"/>


    <!-- Libraries - jQuery, Popper.js, Bootstrap.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid">
<?php
/**
 * Created by Yellow Heroes
 * Project: scratchpad
 * File: index.php
 * User: Robert
 * Date: 22/07/2019
 * Time: 18:06
 * Note: Code based on - http://lekkerlogic.com/2015/10/dark-sky-forecast-io-weather-api-part-2/
 *                     - file: api.php
 */
date_default_timezone_set('Europe/Paris');
// https://api.darksky.net/forecast/[key]/[latitude],[longitude]
/*
 * to exclude data blocks from the response - e.g. exclude the minutely and hourly data points:
 * &exclude=minutely,hourly
 * (the datablocks that can be excluded:
 * currently, minutely, hourly, daily, alerts, flags
 */
$params = "/?units=si&lang=en";
$url = "https://api.darksky.net/forecast/cc2bcd44f8fc3686e76f1657f697335d/48.9,-1.51" . $params;
$json = file_get_contents($url);
$response = json_decode($json);

$go = true;
if ($go == true) {
    if ($response != null) {

        //$lat = $response['latitude'];
        $lat = $response->latitude;
        $lon = $response->longitude;
        $tz = $response->timezone;
        $offset = $response->offset;

        //Current Conditions
        $curTime = $response->currently->time;
        //echo date('Y-m-d H:i:s', $curTime);
        //echo '<br />';
        $curSummary = $response->currently->summary;
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
        $curWindBearing = $response->currently->windBearing;
        $curCloudCover = ($response->currently->cloudCover) * 100;
        $curPressure = round(($response->currently->pressure) * 0.0295301, 2);
        $curVis = round($response->currently->visibility);

        //Now Conditions
        //$nowSummary = $response->minutely->summary;
        //$nowIcon = $response->minutely->icon;

        //Hourly Forecast
        $hourlySummary = $response->hourly->summary;
        $hourlyIcon = $response->hourly->icon;
        $hourlyCond = array();
        foreach ($response->hourly->data as $d) {
            $hourlyCond[] = $d;
        }


        //daily Forecast
        $dailySummary = $response->daily->summary;
        $dailyIcon = $response->daily->icon;
        $dailyCond = array();
        foreach ($response->daily->data as $d) {
            $dailyCond[] = $d;
        }
    }
}


// daily forecast
echo "<div class='row'>";
echo "<h3 class='ml-15'>Week Forecast <small class='text-info'>" . $dailySummary . "</small></h3>";
echo "</div>"; // end row with 'summary'

$count = 0;
foreach ($dailyCond as $cond) {

    $wTime = $cond->time;
    $wSummary = $cond->summary;
    /*
     * icon optional
     * this property will have one of the following values:
     * clear-day, clear-night, rain, snow, sleet, wind, fog, cloudy, partly-cloudy-day, or partly-cloudy-night.
     *
     * We link this property to the following weather-icons:
     *   prop value - weather-icon-name
     * 1. clear-day - wi-day-sunny
     * 2. clear-night - wi-night-clear
     * 3. rain - wi-day-rain
     * 4. snow - wi-day-snow
     * 5. sleet - wi-day-sleet
     * 6. wind - wi-day-windy
     * 7. fog - wi-day-fog
     * 8. cloudy - wi-day-cloudy-high
     * 9. partly-cloudy-day - wi-day-cloudy
     * 10. partly-cloudy-night - wi-night-alt-cloudy
     */
    $weatherIcons = ['clear-day' => 'wi wi-day-sunny', 'clear-night' => 'wi wi-night-clear', 'rain' => 'wi wi-day-rain', 'snow' => 'wi wi-day-snow', 'sleet' => 'wi wi-day-sleet', 'wind' => 'wi wi-day-windy', 'fog' => 'wi wi-day-fog', 'cloudy' => 'wi wi-day-cloudy-high', 'partly-cloudy-day' => 'wi wi-day-cloudy', 'partly-cloudy-night' => 'wi wi-night-alt-cloudy'];
    $wIcon = $cond->icon;
    foreach ($weatherIcons as $key => $value) {
        if ($wIcon == $key) {
            $iconImg = '<div class="row">';
            $iconImg .= '<div class="col" style="font-size: 2.5em;">';
            $iconImg .= '<i class="' . $value . '"></i>';
            $iconImg .= "</div>";
            $iconImg .= "</div>";
            break;
        }
    }
    $wTempHigh = round($cond->temperatureMax);
    $wTempLow = round($cond->temperatureMin);
    $wPrecipProb = $cond->precipProbability * 100;
    if (isset($cond->precipType)) {
        $wPrecipType = $cond->precipType;
    }
    $wClouds = $cond->cloudCover * 100;
    $wHumidity = $cond->humidity * 100;
    $wWindSpeed = round($cond->windSpeed);
    $wSunRise = $cond->sunriseTime;
    $wSunSet = $cond->sunsetTime;

    $cardCount = 4; // the number of cards to show on each row
    $newRow = $count % $cardCount == 0 ? true : false;
    if ($newRow) {
        echo "</div><div class='row' style='margin-top: 50px;'>"; // close previous row, open new row
    }
    echo '<div class="col-3">';
    echo '<div class="card">';
    echo '<div class="card-header"><div class="text-warning"><strong>' . date("l, M jS", $wTime) . '</strong></div></div>';
    echo '<div class="card-body">';

    echo $iconImg;
    echo '<strong>' . $wSummary . '</strong>';

    echo '<div class="row" style="margin: 20px;">';
    echo '<div class="col">';
    echo '<div class="text-success"><span>Max: </span><b>' . $wTempHigh . '</b><i class="wi wi-degrees"></i>C</div>';
    echo '<br />';
    echo '<div class="text-info"><span>Min: </span><b>' . $wTempLow . '</b><i class="wi wi-degrees"></i>C</div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="row" style="margin: 20px;">';
    echo '<div class="col text-right">';
    echo '<i class="wi wi-umbrella"></i> ' . $wPrecipProb . '% &nbsp;&nbsp; <i class="wi wi-cloudy"></i> ' . $wClouds . '%';
    echo '<br>';
    echo '<small><i class="wi wi-sunrise"></i> ' . date("g:i", $wSunRise) . ' &nbsp; <i class="wi wi-sunset"></i> ' . date("g:i", $wSunSet) . '</small>';
    echo '</div>';
    echo '</div>';

    // close card
    echo "</div>";
    echo "</div>";
    echo "</div>";

    $count++;
}
?>
</div> <!-- close container -->
</body>
</html>