// Camera stream by using the rear camera

$( document ).ready(function() {
    console.log( "ready!" );
    activeLinkFunc();
    getChart("temperature");
    getChartSummary();
});


function activeLinkFunc(){
    const list = document.querySelectorAll(".list");
    function activeLink(){
        list.forEach((item) =>
        item.classList.remove("active"));
        this.classList.add("active")
    }
    list.forEach((item)=>
        item.addEventListener("click", activeLink)
    );
}

function getPageMenu(divName){
  const list = document.querySelectorAll(".container");
  
  list.forEach((item) =>
    item.style.display = "none");
    
  list.forEach((item) => {
    if(item.classList.contains(divName)){
      item.style.display = "block";
    }
  });

}


function switchChartType(){
  var e = document.getElementById("selectChartType");
  var value = e.options[e.selectedIndex].value;
  console.log(value)
  getChart(value);
}

function displayChartSummary(){
  getChartSummary();
}

function getChart(chartType) {
//https://canvasjs.com/javascript-charts/

//pour la temperature
//https://canvasjs.com/javascript-charts/chart-image-overlay/
  $.ajax({
    type: "POST",
    url: "databaseFunctions.php",
    data: {chart: "weatherData"}
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
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth();
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
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth();
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
          dataToPush = { x: xVal, y: humidity_data[data1], indexLabel: "Now", markerColor: "red" };
        } else {
          dataToPush = { x: xVal, y: humidity_data[data1] }
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
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth();
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
      var i = 0;
      precipitation_data = reponse.hourly.precipitation;
      for (const data1 in precipitation_data) {
        dateFormated = new Date(reponse.hourly.time[i]);

        if (i % 24 == 0) {
          dayDate = dayName[dateFormated.getDay()] + " " + dateFormated.getDate() + "/" + dateFormated.getMonth();
          stripLineData.push(
            {
              value: data1,
              label: dayDate
            }
          );


          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: precipitation_data[data1], label: dayDate, indexLabel: "Now", markerColor: "red" };
          }
        } else {
          if (dayName[dateFormated.getDay()] == dayName[currentDay] && dateFormated.getHours() == currentHour) {
            dataToPush = { y: precipitation_data[data1], label: dayDate, indexLabel: "Now", markerColor: "red" };
          } else {
            dataToPush = { y: precipitation_data[data1], label: dayDate }
          }
      }
        weatherData.push(dataToPush);
        i += 1;

      }
      generateChart(weatherData, chartType, stripLineData)

    }
    

    

  });
  
  }

function generateChart(weatherData, chartType, stripLineData){
    if(chartType=="temperature"){
      var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        theme: "light2",
        title:{
          text: "Temperature Chart"
        },
        subtitles: [{
          text: "(next 7 days)"
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
          suffix: " %"
        },
        axisY: {
          includeZero: true
        },
        /*
        axisX: {
          stripLines: stripLineData
        },*/
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

    else if(chartType=="windspeed"){
      var chart = new CanvasJS.Chart("chartContainer", {
        theme: "light2",
        title: {
          text: "WindSpeed Chart"
        },
        subtitles: [{
          text: "(next 7 days)"
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
          text: "Precipitation Chart"
        },
        subtitles: [{
          text: "(next 7 days)"
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
    

  }




//*******************************************************//


function getChartSummary() {
  //https://canvasjs.com/javascript-charts/
  
  //pour la temperature
  //https://canvasjs.com/javascript-charts/chart-image-overlay/
    $.ajax({
      type: "POST",
      url: "databaseFunctions.php",
      data: {chartSummary: "weatherData"}
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
  
      
      //console.log(dayDate);


      var i = 0;

      for (const data1 in reponse) {
        //reponse[data1]

        //init stripLine with first day of week
        dateFormated = new Date(Date.parse(reponse[data1].date_time));
        dayDate = dayName[dateFormated.getDay()];

        if(reponse[data1].rain < 0.4){
          rainData = "sunny";
        } else if (reponse[data1].rain < 0.7){
          rainData = "cloudy";
        } else {
          rainData = "rainy";
        }

        if(reponse[data1].maxTemp > maxTempSummary){
          maxTempSummary = reponse[data1].maxTemp;
        }
        if(reponse[data1].minTemp < minTempSummary){
          minTempSummary = reponse[data1].minTemp;
        }

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

        weatherData.push(dataToPush);

      }
      console.log(weatherData);
      //set max temp for chart with little margin
      maxTempSummary = +(maxTempSummary) + 5;
      minTempSummary = +(minTempSummary) - 1;
      generateChartSummary(weatherData, maxTempSummary, minTempSummary)
        
  
    });
    
    }
  
  function generateChartSummary(weatherData, maxTempSummary, minTempSummary){
    var chart = new CanvasJS.Chart("chartContainerSummary", {
      title:{
        text: "Temperature Chart"
      },
      subtitles: [{
        text: "(next 7 days)"
      }],
      axisY: {
        suffix: " °C",
        gridThickness: 0,
        maximum: maxTempSummary,
        minimum: minTempSummary
      },
      toolTip:{
        shared: true,
        content: "{name} </br> <strong>Temperature: </strong> </br> Min: {y[0]} °C, Max: {y[1]} °C"
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
	for(var i = 0; i < chart.data[0].dataPoints.length; i++){
		var dpsName = chart.data[0].dataPoints[i].name;
		if(dpsName == "cloudy"){
			images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/cloudy.png"));
		} else if(dpsName == "rainy"){
		images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/rainy.png"));
		} else if(dpsName == "sunny"){
			images.push($("<img>").attr("src", "https://canvasjs.com/wp-content/uploads/images/gallery/gallery-overview/sunny.png"));
		}
  
	images[i].attr("class", dpsName).appendTo($("#chartContainerSummary>.canvasjs-chart-container"));
	positionImage(chart, images[i], i);
	}
}

function positionImage(chart, image, index) {
	var imageCenter = chart.axisX[0].convertValueToPixel(chart.data[0].dataPoints[index].x);
	var imageTop =  chart.axisY[0].convertValueToPixel(chart.axisY[0].maximum);

	image.width("40px")
	.css({ "left": imageCenter - 20 + "px",
	"position": "absolute","top":imageTop + "px",
	"position": "absolute"});
}


function formatter(e) { 
	if(e.index === 0 && e.dataPoint.x === 0) {
		return " Min " + e.dataPoint.y[e.index] + "°";
	} else if(e.index == 1 && e.dataPoint.x === 0) {
		return " Max " + e.dataPoint.y[e.index] + "°";
	} else{
		return e.dataPoint.y[e.index] + "°";
	}
} 

