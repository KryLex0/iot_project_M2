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
        print_r(json_encode(getWeatherDataSummaryDB($mysqlClient)));
    }
}


function checkAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $resultAddressData = getAddressData($mysqlClient);
    if($resultAddressData){
        updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
        updateWeatherData($mysqlClient);
    }else{
        insertAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
        insertWeatherData($mysqlClient);
    }

}

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
    //$rain = $weatherData->hourly->rain;
    $rain = 0;
    for($i=0; $i<7; $i++){
        $dateTimeTmp = array_slice($dateTime, $i*24, 24);
        $temperatureTmp = array_slice($temperature, $i*24, 24);
            $minTempTmp = min($temperatureTmp);
            $maxTempTmp = max($temperatureTmp);
        $humidityTmp = array_slice($humidity, $i*24, 24);
            $minHumidityTmp = min($humidityTmp);
            $maxHumidityTmp = max($humidityTmp);
        $precipitationTmp = array_slice($precipitation, $i*24, 24);
            $rain = max($precipitationTmp);
        /*
        foreach($precipitationTmp as $key => $value){
            if($value > 0.3){
                $rain = true;
                break;
            }
        }*/

        $sqlQuery = "SELECT * FROM weather_data WHERE date_time = '$dateTimeTmp[0]'";
        $result = $mysqlClient->prepare($sqlQuery);
        $result->execute();
        $result = $result->fetchAll();
        //print_r($result);
        // get stored data for the day
        if(count($result) == 0){
            echo "HERE";
            // if no data for the day, insert it
            $sqlQuery = "INSERT INTO weather_data (date_time, minTemp, maxTemp, minHumidity, maxHumidity, rain) VALUES ('$dateTimeTmp[0]', $minTempTmp, $maxTempTmp, $minHumidityTmp, $maxHumidityTmp, $rain)";
            $result = $mysqlClient->prepare($sqlQuery);
            $result->execute();
            print_r($result);
        }
    }

}


function updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $user_location = getLocation($address_user, $town_user, $postcode_user);
    $longitude_user = $user_location["features"][0]["geometry"]["coordinates"][0];
    $latitude_user = $user_location["features"][0]["geometry"]["coordinates"][1];
    $sqlQuery = "UPDATE user_location SET address_user='$address_user', postcode_user='$postcode_user', town_user='$town_user', country_user='$country_user', longitude_user='$longitude_user', latitude_user='$latitude_user'";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
}

function updateWeatherData($mysqlClient){
    $weatherData = getWeatherData($mysqlClient);
    $weatherData = json_decode($weatherData);
    $dateTime = $weatherData->hourly->time;
    $temperature = $weatherData->hourly->temperature_2m;
    $humidity = $weatherData->hourly->relativehumidity_2m;
    $precipitation = $weatherData->hourly->precipitation;
    //$rain = $weatherData->hourly->rain;
    $rain = 0;
    for($i=0; $i<7; $i++){
        $dateTimeTmp = array_slice($dateTime, $i*24, 24);
        $temperatureTmp = array_slice($temperature, $i*24, 24);
            $minTempTmp = min($temperatureTmp);
            $maxTempTmp = max($temperatureTmp);
        $humidityTmp = array_slice($humidity, $i*24, 24);
            $minHumidityTmp = min($humidityTmp);
            $maxHumidityTmp = max($humidityTmp);
        $precipitationTmp = array_slice($precipitation, $i*24, 24);
            $rain = max($precipitationTmp);
        /*
        foreach($precipitationTmp as $key => $value){
            if($value > 0.3){
                $rain = true;
                break;
            }
        }*/

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
        }else{
            // if data for the day, update it
            $sqlQuery = "UPDATE weather_data SET minTemp=$minTempTmp, maxTemp=$maxTempTmp, minHumidity=$minHumidityTmp, maxHumidity=$maxHumidityTmp, rain=$rain WHERE date_time='$dateTimeTmp[0]'";
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

function getWeatherDataSummaryDB($mysqlClient){
    $currentDate = (array) new DateTime('today midnight');
    $currentDate = $currentDate['date'];

    $sqlQuery = "SELECT * FROM `weather_data` WHERE date_time>='$currentDate'";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $resultWeatherData = $result->fetchAll();
    
    return $resultWeatherData;
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
    $url     = "https://api.open-meteo.com/v1/forecast?longitude=$longitude_user&latitude=$latitude_user&hourly=temperature_2m,relativehumidity_2m,windspeed_10m,precipitation,rain";

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

    curl_close( $ch );

    return $response;

}

?>