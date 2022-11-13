// Camera stream by using the rear camera

$( document ).ready(function() {
    console.log( "ready!" );
    activeLinkFunc();
    getChart("temperature");
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

    if(chartType == "temperature"){
      temperature_data = reponse.hourly.temperature_2m;
      for (const data1 in temperature_data) {
  
        console.log(temperature_data[data1])
        dataToPush = { y: temperature_data[data1]}
        weatherData.push(dataToPush);
  
      }
      generateChart(weatherData, chartType)
      
    }else if(chartType == "humidity"){
      humidity_data = reponse.hourly.relativehumidity_2m;
      xVal = 10
      for (const data1 in humidity_data) {

        console.log(humidity_data[data1])

        dataToPush = { x: xVal, y: humidity_data[data1] }
        weatherData.push(dataToPush);
        xVal += 10
      }
      generateChart(weatherData, chartType)

    }else if(chartType == "windspeed"){
      windspeed_data = reponse.hourly.windspeed_10m;
      for (const data1 in windspeed_data) {
        console.log(windspeed_data[data1])
        //dataToPush = { y: windspeed_data[data1]}

        dataToPush = { x: data1, y: windspeed_data[data1] }
        weatherData.push(dataToPush);
      }
      generateChart(weatherData, chartType)

    }
    

    

  });
  
  }

  function generateChart(weatherData, chartType){
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
        title:{
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


    }
    

  }