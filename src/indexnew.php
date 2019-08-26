<?php
// get all places on the planet
// store them in $arr
$baseDir = dirname(__DIR__, 1);
$target = $baseDir . "/src/assets/json/city.list.json";
echo $target;
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

            echo 'longitude : ' . $lon;
            echo '<br />';
            echo 'latitude : ' . $lat;
            echo '<br />';
            echo 'name : ' . $idName;
            echo '<br />';
        }
    }

    /*
    $params = "/?units=si&lang=en";
    $url = "https://api.darksky.net/forecast/cc2bcd44f8fc3686e76f1657f697335d/48.9,-1.51" . $params;
    $json = file_get_contents($url);
    $response = json_decode($json);
    */
}