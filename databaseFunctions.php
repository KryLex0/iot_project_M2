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


function checkAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $resultAddressData = getAddressData($mysqlClient);
    if($resultAddressData){
        updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
    }else{
        insertAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);
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

function updateAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user){
    $user_location = getLocation($address_user, $town_user, $postcode_user);
    $longitude_user = $user_location["features"][0]["geometry"]["coordinates"][0];
    $latitude_user = $user_location["features"][0]["geometry"]["coordinates"][1];
    $sqlQuery = "UPDATE user_location SET address_user='$address_user', postcode_user='$postcode_user', town_user='$town_user', country_user='$country_user', longitude_user='$longitude_user', latitude_user='$latitude_user'";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
}

function getAddressData($mysqlClient){
    $sqlQuery = "SELECT * FROM user_location";
    $result = $mysqlClient->prepare($sqlQuery);
    $result->execute();
    $resultAddressData = $result->fetchAll();
    
    return $resultAddressData;
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

    $url     = "https://api.open-meteo.com/v1/forecast?longitude=$longitude_user&latitude=$latitude_user&hourly=temperature_2m,relativehumidity_2m,windspeed_10m";

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