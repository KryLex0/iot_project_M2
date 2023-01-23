// Camera stream by using the rear camera

$(document).ready(function () {
  console.log("ready!");
  activeLinkFunc();
  getChart("temperature");

  //init chart Summary for the 7 next days
  getChartSummary("", "");
  resetInputChartDate();
});


function activeLinkFunc() {
  const list = document.querySelectorAll(".list");
  function activeLink() {
    list.forEach((item) =>
      item.classList.remove("active"));
    this.classList.add("active")
  }
  list.forEach((item) =>
    item.addEventListener("click", activeLink)
  );
}

function getPageMenu(divName) {
  const list = document.querySelectorAll(".container");

  list.forEach((item) =>
    item.style.display = "none");

  list.forEach((item) => {
    if (item.classList.contains(divName)) {
      item.style.display = "block";
    }
  });

}

function refreshWeatherData() {
  $.ajax({
    type: "POST",
    url: "databaseFunctions.php",
    data: { refresh: "weatherData" }
  }).done(function (data) {
    window.location.reload();
    alert("Les données ont bien été actualisées!")
  });
}


function switchChartType() {
  var e = document.getElementById("selectChartType");
  var value = e.options[e.selectedIndex].value;
  console.log(value)
  getChart(value);
}

function switchChartDate() {
  var inputChartDateStart = document.getElementById("inputChartDateStart").value + " 00:00:00";
  var inputChartDateEnd = document.getElementById("inputChartDateEnd").value + " 00:00:00";

  console.log(inputChartDateStart + " :: " + inputChartDateEnd)
  getChartSummary(inputChartDateStart, inputChartDateEnd);
}

function resetInputChartDate() {
  const firstDayWeek = new Date().toISOString().slice(0, 10);
  const lastDayWeek = new Date(new Date().setDate(new Date().getDate() + 6)).toISOString().slice(0, 10);

  document.getElementById("inputChartDateStart").value = firstDayWeek;
  document.getElementById("inputChartDateEnd").value = lastDayWeek;
}

function getChart(chartType) {
  //https://canvasjs.com/javascript-charts/

  //pour la temperature
  //https://canvasjs.com/javascript-charts/chart-image-overlay/
  $.ajax({
    type: "POST",
    url: "databaseFunctions.php",
    data: { chart: "weatherData" }
  }).done(function (data) {
    var reponse = JSON.parse(data)
    //console.log(data.longitude);
    weatherData = [{}];
    dataToPush = {}
    finalArray = [];
    stripLineData = [{}];


    //const dayName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const dayName = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];

    //init stripLine with first day of week
    dateFormated = new Date(reponse.hourly.time[0]);
    dayDate = dayName[dateFormated.getDay()];
    stripLineData.push(
      {
        value: i,
        label: dayDate
      }
    )

    const currentDate = new Date();
    const currentDay = currentDate.getDay();
    const currentHour = currentDate.getHours();
    //console.log(dateFormated.getDay() + " :: " + currentDay);
    //console.log(dateFormated);// + " :: " + currentHour);
    if (chartType == "temperature") {

      var i = 0;

      temperature_data = reponse.hourly.temperature_2m;
      for (const data1 in temperature_data) {
        dateFormated = new Date(reponse.hourly.time[i]);

        if (i % 24 == 0) {
          console.log()
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth() + 1;
          stripLineData.push(
            {
              value: data1,
              label: dayDate
            }
          );
          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: temperature_data[data1], label: dayDate, indexLabel: "Now", markerColor: "red" };
          }
        } else {
          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: temperature_data[data1], label: dayDate, indexLabel: "Now", markerColor: "red" };
          } else {
            dataToPush = { y: temperature_data[data1], label: dayDate };
          }
        }
        weatherData.push(dataToPush);
        i += 1;
      }
      generateChart(weatherData, chartType, stripLineData)

    } else if (chartType == "humidity") {
      var i = 0;
      humidity_data = reponse.hourly.relativehumidity_2m;
      xVal = 10
      for (const data1 in humidity_data) {
        dateFormated = new Date(reponse.hourly.time[i]);

        if (i % 24 == 0) {
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth() + 1;
          stripLineData.push(
            {
              value: data1,
              label: dayDate
            }
          );
          //dataToPush = { x: xVal, y: humidity_data[data1], indexLabel: "\u2191 " + dayDate, markerColor: "red" };
        }// else {
        //}
        //dataToPush = { x: xVal, y: humidity_data[data1] }
        if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
          dataToPush = { /*x: xVal, */ label: dayDate, y: humidity_data[data1], indexLabel: "Now", markerColor: "red" };
        } else {
          dataToPush = { /*x: xVal,*/ label: dayDate, y: humidity_data[data1] }
        }
        i += 1;

        weatherData.push(dataToPush);
        xVal += 10
      }
      generateChart(weatherData, chartType, stripLineData)

    } else if (chartType == "windspeed") {
      var i = 0;
      windspeed_data = reponse.hourly.windspeed_10m;
      for (const data1 in windspeed_data) {
        dateFormated = new Date(reponse.hourly.time[i]);

        if (i % 24 == 0) {
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth() + 1;
          //echo("i: " + i);
          // stripLineData.push(
          //   {
          //     value: windspeed_data[data1],
          //     label: dayDate
          //   }
          // );
          //dataToPush = { x: data1, y: windspeed_data[data1], indexLabel: "\u2191 " + dayDate, markerColor: "red" };
        } //else {
        //}
        if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
          dataToPush = { x: data1, y: windspeed_data[data1], indexLabel: "Now", markerColor: "red" };
        } else {
          dataToPush = { x: data1, y: windspeed_data[data1] }
        }

        //dataToPush = { x: data1, y: windspeed_data[data1] }
        i += 1;


        weatherData.push(dataToPush);
      }
      generateChart(weatherData, chartType, stripLineData)

    } else if (chartType == "precipitation") {
      //console.log(reponse.hourly.precipitation)

      var i = 0;
      precipitation_data = reponse.hourly.precipitation;
      for (const data1 in precipitation_data) {
        dateFormated = new Date(reponse.hourly.time[i]);

        if (precipitation_data[data1] >= 1) {
          precipitation_data_tmp = 1;
        } else {
          precipitation_data_tmp = precipitation_data[data1];
        }

        if (i % 24 == 0) {
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth() + 1;
          stripLineData.push(
            {
              value: data1,
              label: dayDate
            }
          );


          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: precipitation_data_tmp, label: dayDate, indexLabel: "Now", markerColor: "red" };
          }
        } else {
          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: precipitation_data_tmp, label: dayDate, indexLabel: "Now", markerColor: "red" };
          } else {
            dataToPush = { y: precipitation_data_tmp, label: dayDate }
          }
        }
        weatherData.push(dataToPush);
        i += 1;

      }
      generateChart(weatherData, chartType, stripLineData)

    }




  });

}

function generateChart(weatherData, chartType, stripLineData) {
  if (chartType == "temperature") {
    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      theme: "light2",
      title: {
        text: "Graphique des températures"
      },
      subtitles: [{
        text: "(7 prochains jours)"
      }], 
      axisY: {
        suffix: " °C"
      },
      axisX: {
        stripLines: stripLineData
      },
      data: [{
        type: "line",
        indexLabelFontSize: 16,
        dataPoints: weatherData
      }]
    });
    chart.render();
  }
  /*
      else if(chartType=="humidity"){
        var chart = new CanvasJS.Chart("chartContainer", {
          animationEnabled: true,
          exportEnabled: true,
          theme: "light1", // "light1", "light2", "dark1", "dark2"
          title: {
            text: "Humidity Chart"
          },
          subtitles: [{
            text: "(next 7 days)"
          }],
          axisY: {
            suffix: " %",
            includeZero: true
          },
  
          data: [{
            type: "column", //change type to bar, line, area, pie, etc
            //indexLabel: "{y}", //Shows y value on all Data Points
            indexLabelFontColor: "#5A5757",
            indexLabelFontSize: 16,
            indexLabelPlacement: "outside",
            dataPoints: weatherData
          }]
        });
        chart.render();
      }
      */
  else if (chartType == "humidity") {
    var chart = new CanvasJS.Chart("chartContainer", {
      theme: "light2", // "light1", "light2", "dark1", "dark2"
      animationEnabled: true,
      zoomEnabled: true,
      title: {
        text: "Graphique de l'humidité"
      },
      subtitles: [{
        text: "(7 prochains jours)"
      }], 
      axisY: {
        suffix: " %",
      },
      axisX: {
        stripLines: stripLineData,
      },
      data: [{
        indexLabelFontSize: 16,
        type: "area",
        dataPoints: weatherData
      }]
    });
    chart.render();
  }

  else if (chartType == "windspeed") {
    var chart = new CanvasJS.Chart("chartContainer", {
      theme: "light2",
      title: {
        text: "Graphique de la vitesse du vent",
      },
      subtitles: [{
        text: "(7 prochains jours)"
      }], 
      axisY: {
        suffix: " km/h"
      },
      axisX: {
        stripLines: stripLineData
      },
      toolTip: {
        shared: true
      },
      data: [{
        type: "area",
        name: "WindSpeed",
        markerSize: 0,
        xValueType: "dateTime",
        xValueFormatString: "MMM YYYY",
        dataPoints: weatherData
      }
      ]
    });
    chart.render();


  } else if (chartType == "precipitation") {
    var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      theme: "green",
      title: {
        text: "Graphique de la précipitation"
      },
      subtitles: [{
        text: "(7 prochains jours)"
      }], 
      axisY: {
        suffix: "",
        maximum: 1.1,
        minimum: 0,
      },
      axisX: {
        stripLines: stripLineData
      },
      data: [{
        type: "line",
        indexLabelFontSize: 16,
        dataPoints: weatherData
      }]
    });
    chart.render();
  }


}




//*******************************************************//


function getChartSummary(dateStart, dateEnd) {
  //https://canvasjs.com/javascript-charts/

  //pour la temperature
  //https://canvasjs.com/javascript-charts/chart-image-overlay/
  $.ajax({
    type: "POST",
    url: "databaseFunctions.php",
    data: {
      chartSummary: "weatherData",
      dateStart: dateStart,
      dateEnd: dateEnd
    }
  }).done(function (data) {

    var reponse = JSON.parse(data)
    //console.log(reponse);
    // console.log(reponse[0].date_time);
    // console.log(reponse[0].minTemp);
    // console.log(reponse[0].maxTemp);
    // console.log(reponse[0].minHumidity);
    // console.log(reponse[0].maxHumidity);
    // console.log(reponse[0].rain);

    weatherData = [];
    dataToPush = {}
    finalArray = [];
    maxTempSummary = 0;
    minTempSummary = 0;

    //const dayName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const dayName = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    const currentDate = new Date().toISOString().slice(0, 10) + " 00:00:00";

    //console.log(dayDate);


    if (reponse.length == 0) {
      alert("Aucune donnée pour cette période. Veuillez choisir une autre période.");
      resetInputChartDate();
      return;
    }

    var i = 0;

    for (const data1 in reponse) {
      if (data1 == 0) {
        document.getElementById("inputChartDateStart").value = reponse[data1].date_time.slice(0, 10);
      }
      if (data1 == reponse.length - 1) {
        document.getElementById("inputChartDateEnd").value = reponse[data1].date_time.slice(0, 10);
      }
      //reponse[data1]

      //init stripLine with first day of week
      dateFormated = new Date(Date.parse(reponse[data1].date_time));
      //dayDate = dayName[dateFormated.getDay()];
      dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth() + 1 + "/" + dateFormated.getFullYear();


      if (reponse[data1].rain < 0.4) {
        rainData = "ensoleille";
      } else if (reponse[data1].rain < 0.7) {
        rainData = "nuageux";
      } else {
        rainData = "pluvieux";
      }

      if (reponse[data1].maxTemp > maxTempSummary) {
        maxTempSummary = reponse[data1].maxTemp;
      }
      if (reponse[data1].minTemp < minTempSummary) {
        minTempSummary = reponse[data1].minTemp;
      }
      /*
              if(data1 == 0){
                dataToPush = { 
                  y: [+(reponse[data1].minTemp), +(reponse[data1].maxTemp)],
                  label: dayDate,
                  name: rainData,
                  indexLabel: "Today",
                  markerColor: "red"
                };
              } else {
                dataToPush = { 
                  y: [+(reponse[data1].minTemp), +(reponse[data1].maxTemp)],
                  label: dayDate,
                  name: rainData
                };
              }
              */

      //console.log(currentDate + " AND " + reponse[data1].date_time);
      if (reponse[data1].date_time == currentDate) {
        //console.log("currentDate");
        dataToPush = {
          y: [+(reponse[data1].minTemp), +(reponse[data1].maxTemp)],
          label: dayDate,
          name: rainData,
          indexLabel: "Today",
          markerColor: "red"
        };
      } else {
        //console.log("not currentDate");
        dataToPush = {
          y: [+(reponse[data1].minTemp), +(reponse[data1].maxTemp), +(reponse[data1].minHumidity), +(reponse[data1].maxHumidity)],
          label: dayDate,
          name: rainData
        };
      }

      //console.log(dateFormated);
      weatherData.push(dataToPush);

    }
    //console.log(weatherData);
    //set max temp for chart with little margin
    maxTempSummary = +(maxTempSummary) + 5;
    minTempSummary = +(minTempSummary) - 1;
    generateChartSummary(weatherData, maxTempSummary, minTempSummary)


  });

}

function generateChartSummary(weatherData, maxTempSummary, minTempSummary) {
  var chart = new CanvasJS.Chart("chartContainerSummary", {
    title: {
      text: "Graphique de la température"
    },
    axisY: {
      suffix: " °C",
      gridThickness: 0,
      maximum: maxTempSummary,
      minimum: minTempSummary
    },
    toolTip: {
      shared: true,
      content: "{name} </br> <strong>Temperature: </strong> </br> Min: {y[0]} °C, Max: {y[1]} °C </br> <strong>Humidité: </strong> </br> Min: {y[2]} %, Max: {y[3]} %"
    },
    data: [{
      type: "rangeSplineArea",
      fillOpacity: 0.1,
      color: "#91AAB1",
      indexLabelFormatter: formatter,
      dataPoints: weatherData
    }]
  });
  chart.render();
  addImages(chart);

}

var images = [];

function addImages(chart) {
  for (var i = 0; i < chart.data[0].dataPoints.length; i++) {
    var dpsName = chart.data[0].dataPoints[i].name;
    if (dpsName == "nuageux") {
      images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/cloudy.png"));
    } else if (dpsName == "pluvieux") {
      images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/rainy.png"));
    } else if (dpsName == "ensoleille") {
      images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/sunny.png"));
    }

    images[i].attr("class", dpsName).appendTo($("#chartContainerSummary>.canvasjs-chart-container"));
    positionImage(chart, images[i], i);
  }
}

function positionImage(chart, image, index) {
  var imageCenter = chart.axisX[0].convertValueToPixel(chart.data[0].dataPoints[index].x);
  var imageTop = chart.axisY[0].convertValueToPixel(chart.axisY[0].maximum);

  image.width("40px")
    .css({
      "left": imageCenter - 20 + "px",
      "position": "absolute", "top": imageTop + "px",
      "position": "absolute"
    });
}


function formatter(e) {
  if (e.index === 0 && e.dataPoint.x === 0) {
    return " Min " + e.dataPoint.y[e.index] + "°";
  } else if (e.index == 1 && e.dataPoint.x === 0) {
    return " Max " + e.dataPoint.y[e.index] + "°";
  } else {
    return e.dataPoint.y[e.index] + "°";
  }
}

