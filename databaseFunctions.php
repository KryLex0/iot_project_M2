<?php

//api gouv location
#https://adresse.data.gouv.fr/api-doc/adresse

//api weather
#https://open-meteo.com/en
#https://api.open-meteo.com/v1/forecast?longitude=2.326235&latitude=48.971019&hourly=temperature_2m,relativehumidity_2m,windspeed_10m
require("credentials/credentials.php");


if(isset($_POST["chart"])){
    $chart = $_POST["chart"];
    if($chart=="weatherData"){
        print_r(getWeatherData($mysqlClient));
    }
}

if(isset($_POST["chartSummary"])){
    $chart = $_POST["chartSummary"];
    if($chart=="weatherData"){
        $dateStart = $_POST["dateStart"];
        $dateEnd = $_POST["dateEnd"];
        if($dateStart != "" && $dateEnd != ""){
            //print("dateStart: " . $dateStart . " dateEnd: " . $dateEnd);
            print_r(json_encode(getWeatherDataSummaryByDateDB($mysqlClient, $dateStart, $dateEnd)));
        }else{
             print_r(json_encode(getWeatherDataSummaryDB($mysqlClient)));
        }
    }
}

if(isset($_POST["refresh"])){
    updateWeatherData($mysqlClient);
}

function checkAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    //$resultAddressData = getAddressData($mysqlClient);
    /*
    if($resultAddressData){
        updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
    }else{
        insertAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
        //insertWeatherData($mysqlClient);
    }*/
    updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
    updateWeatherData($mysqlClient);


}
/*
function insertAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $user_location = getLocation($address_user, $town_user, $postcode_user);
    $longitude_user = $user_location["features"][0]["geometry"]["coordinates"][0];
    $latitude_user = $user_location["features"][0]["geometry"]["coordinates"][1];
    $sqlQuery = "INSERT INTO user_location (address_user, postcode_user, town_user, country_user, longitude_user, latitude_user) VALUES ('$address_user', '$postcode_user', '$town_user', '$country_user', '$longitude_user', '$latitude_user')";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
}

function insertWeatherData($mysqlClient){
    $weatherData = getWeatherData($mysqlClient);
    $weatherData = json_decode($weatherData);
    $dateTime = $weatherData->hourly->time;
    $temperature = $weatherData->hourly->temperature_2m;
    $humidity = $weatherData->hourly->relativehumidity_2m;
    $precipitation = $weatherData->hourly->precipitation;
    $rain = $weatherData->hourly->rain;
    //$rain = 0;
    for($i=0; $i<7; $i++){
        $dateTimeTmp = array_slice($dateTime, $i*24, 24);
        $temperatureTmp = array_slice($temperature, $i*24, 24);
            $minTempTmp = min($temperatureTmp);
            $maxTempTmp = max($temperatureTmp);
        $humidityTmp = array_slice($humidity, $i*24, 24);
            $minHumidityTmp = min($humidityTmp);
            $maxHumidityTmp = max($humidityTmp);
        $rainTmp = array_slice($rain, $i*24, 24);
            $rain = max($rainTmp); // if there is rain, it will be 1
            if($rain >= 1){
                $rain = 1;
            }

        $sqlQuery = "SELECT * FROM weather_data WHERE date_time = '$dateTimeTmp[0]'";
        $result = $mysqlClient->prepare($sqlQuery);
        $result->execute();
        $result = $result->fetchAll();
        // get stored data for the day
        if(count($result) == 0){
            // if no data for the day, insert it
            $sqlQuery = "INSERT INTO weather_data (date_time, minTemp, maxTemp, minHumidity, maxHumidity, rain) VALUES ('$dateTimeTmp[0]', $minTempTmp, $maxTempTmp, $minHumidityTmp, $maxHumidityTmp, $rain)";
            $result = $mysqlClient->prepare($sqlQuery);
            $result->execute();
            print_r($result);
        }
    }

}
*/

function updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $user_location = getLocation($address_user, $town_user, $postcode_user);
    $longitude_user = $user_location["features"][0]["geometry"]["coordinates"][0];
    $latitude_user = $user_location["features"][0]["geometry"]["coordinates"][1];

    // get stored data for user location
    $sqlQuery = "SELECT * FROM user_location;";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $result = $result->fetchAll();
    // get stored data for the day
    if(count($result) == 0){
        // if no Address data found, insert it
        $sqlQuery = "INSERT INTO user_location (address_user, postcode_user, town_user, country_user, longitude_user, latitude_user) VALUES ('$address_user', '$postcode_user', '$town_user', '$country_user', '$longitude_user', '$latitude_user')";
    }else{
        // if Address data found, update it
        $sqlQuery = "UPDATE user_location SET address_user='$address_user', postcode_user='$postcode_user', town_user='$town_user', country_user='$country_user', longitude_user='$longitude_user', latitude_user='$latitude_user'";
    }

    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
}

function updateWeatherData($mysqlClient){
    //get data from API
    $weatherData = getWeatherData($mysqlClient);
    $weatherData = json_decode($weatherData);
    $dateTime = $weatherData->hourly->time;
    $temperature = $weatherData->hourly->temperature_2m;
    $humidity = $weatherData->hourly->relativehumidity_2m;
    //$precipitation = $weatherData->hourly->precipitation;
    $rain = $weatherData->hourly->rain;
    //$rain = 0;
    for($i=0; $i<7; $i++){
        // get data for each day with the median of the values for each day
        $dateTimeTmp = array_slice($dateTime, $i*24, 24);
        $temperatureTmp = array_slice($temperature, $i*24, 24);
            $minTempTmp = min($temperatureTmp);
            $maxTempTmp = max($temperatureTmp);
        $humidityTmp = array_slice($humidity, $i*24, 24);
            $minHumidityTmp = min($humidityTmp);
            $maxHumidityTmp = max($humidityTmp);
        echo "<br>Humidity:<br>";
        print_r($humidity);
        echo "<br>Pluie:<br>";
        print_r($rain);
        //echo gettype($temperature);
        $rainTmp = array_slice($rain, $i*24, 24);
            $rainTmp = max($rainTmp);  // if there is rain, it will be 1
            if($rainTmp >= 1){
                $rainTmp = 1;
            }

        $sqlQuery = "SELECT * FROM weather_data WHERE date_time = '$dateTimeTmp[0]'";
        $result = $mysqlClient->prepare($sqlQuery);
        $result->execute();
        $result = $result->fetchAll();
        // get stored data for the day
        if(count($result) == 0){
            // if no data for the day, insert it
            $sqlQuery = "INSERT INTO weather_data (date_time, minTemp, maxTemp, minHumidity, maxHumidity, rain) VALUES ('$dateTimeTmp[0]', $minTempTmp, $maxTempTmp, $minHumidityTmp, $maxHumidityTmp, $rainTmp)";
            $result = $mysqlClient->prepare($sqlQuery);
            $result->execute();
        }else{
            // if data for the day, update it
            $sqlQuery = "UPDATE weather_data SET minTemp=$minTempTmp, maxTemp=$maxTempTmp, minHumidity=$minHumidityTmp, maxHumidity=$maxHumidityTmp, rain=$rainTmp WHERE date_time='$dateTimeTmp[0]'";
            $result = $mysqlClient->prepare($sqlQuery);
            $result->execute();
        }

    }
    

}

function getAddressData($mysqlClient){
    $sqlQuery = "SELECT * FROM user_location";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $resultAddressData = $result->fetchAll();
    
    return $resultAddressData;
}

function getWeatherDataSummaryDB($mysqlClient, $limitResult=null){
    $currentDate = (array) new DateTime('today midnight');
    $currentDate = $currentDate['date'];

    if($limitResult != null){
        $sqlQuery = "SELECT * FROM `weather_data` WHERE date_time>='$currentDate' ORDER BY date_time ASC LIMIT $limitResult";
    }else{
        $sqlQuery = "SELECT * FROM `weather_data` WHERE date_time>='$currentDate' ORDER BY date_time ASC";
    }
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $resultWeatherData = $result->fetchAll();
    
    return $resultWeatherData;
}

function getWeatherDataSummaryByDateDB($mysqlClient, $dateStart, $dateEnd){
    $sqlQuery = "SELECT * FROM `weather_data` WHERE date_time BETWEEN '$dateStart' AND '$dateEnd' ORDER BY date_time ASC";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $resultWeatherData = $result->fetchAll();
    
    return $resultWeatherData;
}


function checkRainNextDays($mysqlClient){
    $resultWeatherData = getWeatherDataSummaryDB($mysqlClient, 3);
    $isRaining = false;

    foreach($resultWeatherData as $weatherData){
        if($weatherData['rain'] == 1){
            $isRaining = true;
        }
    }

    return $isRaining;


}



function getLocation($address_user, $town_user, $postcode_user) {
    $address = $address_user . "+" . $town_user;
    $address = urlencode($address);
    $url     = "https://api-adresse.data.gouv.fr/search/?q=$address&postcode=$postcode_user&limit=1";

    // Create a curl call
    $ch      = curl_init();
    $timeout = 0;

    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );

    $data = curl_exec( $ch );
    // send request and wait for response

    $response = json_decode( $data, true );

    curl_close( $ch );

    return $response;
}

function getWeatherData($mysqlClient){
    $addressData = getAddressData($mysqlClient);
    $longitude_user = $addressData[0]["longitude_user"];
    $latitude_user = $addressData[0]["latitude_user"];

    //https://open-meteo.com/en/docs
    $url     = "https://api.open-meteo.com/v1/forecast?longitude=$longitude_user&latitude=$latitude_user&hourly=temperature_2m,relativehumidity_2m,windspeed_10m,precipitation,rain&lang=fr";

    // Create a curl call
    $ch      = curl_init();
    $timeout = 0;

    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));


    $data = curl_exec( $ch );
    // send request and wait for response

    //$response = json_decode( $data, true );
    $response = $data;
    //echo $response;

    curl_close( $ch );

    return $response;

}
