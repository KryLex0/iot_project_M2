<?php

//load html page with all div and navbar
require("html_basic_page.php");
$pathParent = dirname(__FILE__);

require $pathParent . "/credentials/credentials.php";



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST)) {
    try {
        $postData = $_POST;
        //print_r($postData);
        $address_user = $postData["address_user"];
        $postcode_user = $postData["postcode_user"];
        $town_user = $postData["town_user"];
        $country_user = $postData["country_user"];

        checkAddressData($mysqlClient, $address_user, $postcode_user, $town_user, $country_user);

        unset($_POST);
        unset($_REQUEST);
        header("Location: index.php");
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}








?>