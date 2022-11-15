<!DOCTYPE HTML>
<html>

<head>
   <meta charset="UTF-8">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="script/script.js"></script>
   <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
   <link rel="stylesheet" href="style/style.css">

   <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>



</head>

<?php
$pathParent = dirname(__FILE__);

//include $pathParent . "/credentials/credentials.php";
require("credentials/credentials.php");
require("databaseFunctions.php");


$addressData = getAddressData($mysqlClient);

?>

<body>
   <div class="containerDiv">
      <div class="container homePage" style="display: none;">
         <p>HOME PAGE</p>
      </div>

      <div class="container graphPage" id="graphPage">
         <p>PAGE graphique</p>
         <label for="labelChartType">Choix des données à afficher</label>

         <select name="selectChartType" id="selectChartType" onchange="switchChartType()">
            <option value="temperature" selected>Température</option>
            <option value="humidity">Humidité</option>
            <!-- <option value="windspeed">Vent</option> -->
            <option value="precipitation">Précipitation</option>
         </select>

         <?php
         //$weatherArrayData = getWeatherData($mysqlClient);
         //print_r($weatherArrayData["hourly"]["time"]); //heures 7 jours
         //print_r($weatherArrayData); //temperature 7 jours
         //print_r($weatherArrayData["hourly"]["relativehumidity_2m"]); //humidity 7 jours
         //print_r($weatherArrayData["hourly"]["windspeed_10m"]); //vent 7 jours
         ?>
         <div id="chartContainer" style="height: auto; width: auto;"></div>

      </div>

      <div class="container menuPage divSearchBar" style="display: none;">
         <h1>Adresse de l'utilisateur</h1></br>
         <?php

         if (empty($addressData)) {
            echo "<p style='color:darkred'>Vous n'avez pas encore ajouté d'adresse!</p>";
         } else {
            $formatedAddress = urlencode($addressData[0]["address_user"] . " " . $addressData[0]["postcode_user"] . " " . $addressData[0]["town_user"] . " " . $addressData[0]["country_user"]);
            echo "<div style='width: 100%'><iframe scrolling='no' marginheight='0' marginwidth='0' src='https://maps.google.com/maps?width=65%25&amp;height=400&amp;hl=en&amp;q=$formatedAddress&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed' width='65%' height='400' frameborder='0'></iframe></div>"
         ?>


         <?php
            $formatedAddress = $addressData[0]["address_user"] . "</br>" . $addressData[0]["postcode_user"] . ", " . $addressData[0]["town_user"] . "</br>" . $addressData[0]["country_user"];
            echo "<h3>Votre adresse est:</h3><p style='color:green'>" . $formatedAddress . "</p>";
         }
         echo "<p>Vous pouvez modifier votre adresse à tout moemnt via le formulaire ci-dessous.</p>";

         ?>
         <form method="post">
            <label><b>Adresse postale :</b>
               <input type="text" name="address_user" required>
            </label>
            <label><b>Code Postal :</b>
               <input type="number" name="postcode_user" required>
            </label>
            <label><b>Ville :</b>
               <input type="text" name="town_user" required>
            </label>
            <label><b>Pays :</b>
               <input type="text" name="country_user" required>
            </label>
            <button>Sauvegarder</button>
         </form>
      </div>
   </div>

   <div id="empty" style="margin-top: 10%;"></div>

   <div class="navigation">
      <ul>
         <li class="list">
            <a href="#homePage" onclick="getPageMenu('homePage')">
               <span class="icon">
                  <ion-icon name="home-outline"></ion-icon>
               </span>
               <span class="text">Home</span>
            </a>
         </li>
         <li class="list active">
            <a href="#graphPage" onclick="getPageMenu('graphPage')">
               <span class="icon">
                  <ion-icon name="analytics-outline"></ion-icon>
               </span>
               <span class="text">Graphiques</span>
            </a>
         </li>
         <li class="list">
            <a href="#menuPage" onclick="getPageMenu('menuPage')">
               <span class="icon">
                  <ion-icon name="menu-outline"></ion-icon>
               </span>
               <span class="text">Menu</span>
            </a>
         </li>
         <!-- <div class="indicator"></div> -->
      </ul>
   </div>


</body>


</html>