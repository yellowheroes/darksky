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
    $url = "https://api.darksky.net/forecast/$myapikey/" . $lat . "," . $lon . $params;
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
}
/*
 * end new
 */

require($baseDir . '/views/footer.php');
