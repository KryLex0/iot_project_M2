# Importing required module
import subprocess
import mysql.connector
from datetime import datetime

import os
import random

folder_name = "geeksforgeeks"

# datetime object containing current date and time
now = datetime.now()
dateTimeNow = now.strftime("%Y/%m/%d") + " 00:00:00"
isRaining = False
senderMail = "admin@admin.fr"
subjectMail = "IOT Project M2 - Prévision météo"

db=mysql.connector.connect(host="localhost", user="root", password="root",database="iot_project")


print("Starting the mail script...")

# Retrive the email of the user from the database
def getReceiverEmail(db):
    cursor=db.cursor()
    query="SELECT email_user FROM user_data"

    cursor.execute(query)
    return cursor.fetchone()

# Retrive the weather data from the database for the next 3 days (from today)
def getWeatherData(db):
    cursor=db.cursor()
    query="SELECT * FROM weather_data WHERE date_time>='" + dateTimeNow + "' ORDER BY id ASC LIMIT 3"
    print(query)
    cursor.execute(query)
    return cursor.fetchall()

# Check if it's raining in the next 3 days
def checkIfRaining(db, senderMail, subjectMail, isRaining):
    receiverMail = getReceiverEmail(db)[0]
    print("Receiver email: " + receiverMail)
    subjectMail1 = ""

    weather_data = getWeatherData(db)
    print("Weather data: " + str(weather_data))
    for row in weather_data:
        print(row[6])

        # If the probability of rain is higher than 60%, it's raining
        if(row[6] > 0.6):
            isRaining = True
            break

    # Prepare the mail body and subject depending on the weather
    if(isRaining):
        print("It's raining")
        mailBody = "Bonjour,\nDans les prochains jours, il va pleuvoir. Nous vous conseillons de sortir vos plantes dehors.\nCordialement,\nL'équipe IOT Project M2"
        subjectMail1 = subjectMail + " - Pluie prévue"
    else:
        print("It's not raining")
        mailBody = "Bonjour,\nDans les prochains jours, il ne va pas pleuvoir. Nous vous conseillons d'arroser vos plantes .\nCordialement,\nL'équipe IOT Project M2"
        subjectMail1 = subjectMail + " - Pas de pluie prévue"
    
    # Send the mail
    sendMail(mailBody, subjectMail, senderMail, receiverMail)

# Send the mail
def sendMail(mailBody, subjectMail, senderMail, receiverMail):
    print("Mail sent to '" + receiverMail + "' with subject '" + subjectMail + "' and body '" + mailBody + "' from '" + senderMail + "'")

    #subprocess.Popen('echo "' + mailBody + '" | mail -a "From: ' + senderMail + '" -s "' + subjectMail + '" ' + receiverMail + '', shell=True)


if(getReceiverEmail(db) != None):
    checkIfRaining(db, senderMail, subjectMail, isRaining)
else:
    print("No email found in the database")
db.close()
