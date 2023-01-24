# iot_project_M2
 
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white) ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E) ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white) ![Python](https://img.shields.io/badge/python-3670A0?style=for-the-badge&logo=python&logoColor=ffdd54)

> Projet réalisé dans le cadre du cours IoT

Application météo pour plantes.

## Ajout de données utilisateur

Afin de pouvoir utiliser l'outil, il est nécessaire de renseigner son adresse postale ainsi que son adresse mail. Ces données sont utilisés afin de récolter les données météo de la localisation de l'utilisateur et ainsi, pouvoir notifier par mail si l'utilisateur doit arroser ses plantes ou non.
Après ça, l'utilisateur peut naviguer sur les différentes pages de l'outil.

## Page de météo détaillée

L'utilisateur peut consulter les données météo détaillée, par heure, pour les 7 prochains jours. C'est données sont représentés sous forme de graphique indiquant les températures, l'humidité, ainsi que les précipitations attendues. Cela permet à l'utilisateur de prévoir la sortie d'une plante ou non dans le but de profiter de l'eau de pluie pour être arrosée. Ces données proviennent de l'[API Open-Météo](https://open-meteo.com/).

## Page d'accueil

Sur cette page, un unique graphique est représenté. Il reprend également les données météo fournie via l'API. Cependant, elles sont traités afin de simplifier la lecture de l'utilisateur. En effet, seulement un résumé est présenté en indiquand les maximales et minimales des températures/humidité pour chaque jours ainsi qu'une icone indiquant s'il va pleuvoir, s'il sera ensoleillé ou bien nuageux.
Il est possible de changer les dates via les calendriers afin de visualiser le résumé pour la période voulue. De plus, un bouton permettant d'actualiser les données est présent. Il permet de mettre à jours les données dans la Base de Données en cas de données corrompues, ou si l'utilisateur à mis à jour son adresse.

## Notification de l'utilisateur

Un script python est également présent afin de notifier l'utilisateur sur la situation météorologique. Pour ce faire, un CronJob peut être créé. Il s'agit d'une tache qui sera effectué tout les jours à une certaine heure.

### Création du CronJob
```sh
crontab -e
```
Ajouter la ligne suivante en remplaçant le chemin vers le script python
> 00 10 * * * /usr/bin/env python3 /path/to/python/script/mail.py

Dans notre example, le fichier `mail.py` sera executé tout les jours à 10h00.

©KryLex0
