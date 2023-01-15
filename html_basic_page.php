<!DOCTYPE HTML>
<html>

<head>
   <meta charset="UTF-8">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="script/script.js"></script>
   <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
   <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>  
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
      <div class="container homePage">
         <p>HOME PAGE</p>
         <button onclick="refreshWeatherData()">Actualiser</button>
         <?php 
         if(!empty($addressData)){
         ?>
            <label>Date de début</label>   
            <input type="date" style="width:100px" name="inputChartDateStart" id="inputChartDateStart">
            <label>Date de fin</label>
            <input type="date" style="width:100px" name="inputChartDateEnd" id="inputChartDateEnd">
            <button onclick="switchChartDate()">Rechercher</button>

            <div id="chartContainerSummary" style="height: 360px; width: 100%;"></div>
            <div>

            <?php
            if(checkRainNextDays($mysqlClient)){
               echo "<p style='color:darkred'>Attention, il pleut dans les prochains jours!</p>";
            }else{
               echo "<p style='color:darkgreen'>Il ne pleut pas dans les prochains jours!</p>";
            }
            
            ?>
            </div>


         <?php
         }else{?>
            <div class='menuPage divSearchBar' style="margin-top:10%;">
               <p style='color:darkred'>Vous n'avez pas encore ajouté d'adresse!</p>
               <p style='color:darkred'>Veuillez visiter le menu afin d'y ajouter votre adresse.</p>

            </div>
         <?php
         }         
         ?>
      </div>

      <div class="container graphPage" id="graphPage" style="display: none;">
         <p>PAGE graphique</p>

         <?php 
         if(!empty($addressData)){
         ?>
            <label for="labelChartType">Choix des données à afficher</label>

            <select name="selectChartType" id="selectChartType" onchange="switchChartType()">
               <option value="temperature" selected>Température</option>
               <option value="humidity">Humidité</option>
               <!-- <option value="windspeed">Vent</option> -->
               <option value="precipitation">Précipitation</option>
            </select>
            
            <div id="chartContainer" style="height: 360px; width: 100%; margin-top:5%;"></div>

         <?php
         }else{?>
            <div class="divSearchBar" style="margin-top:10%;">
               <p style='color:darkred'>Vous n'avez pas encore ajouté d'adresse!</p>
               <p style='color:darkred'>Veuillez visiter le menu afin d'y ajouter votre adresse.</p>

            </div>
         <?php
         }         
         ?>
         
         
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
         <li class="list active">
            <a href="#homePage" onclick="getPageMenu('homePage')">
               <span class="icon">
                  <ion-icon name="home-outline"></ion-icon>
               </span>
               <span class="text">Home</span>
            </a>
         </li>
         <li class="list">
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