<html>
	<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
	<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css"/>
	<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
	<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
	<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
	<link href="bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.3.1/css/flag-icon.min.css" rel="stylesheet"/>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>

<head></head>
	<body>

	<div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="index.php" class="navbar-brand">Dionaea Tracker</a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
            <li>
		<a href="index.php">Connections</a>
            </li>
	    <li>
		<a href="traffic.php">Attack Traffic</a>
	    <li>
              <a href="downloads.php" >Captured Samples </a>
            <li>
	    <li>
              <a href="rmap.php" >Real-Time Map</a>
            <li>

              <a href="http://pwning.fun">Blog</a>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li><a target="_blank">by n4x0r</a></li>
          </ul>

        </div>
      </div>

    </div>
	<div id="chartdiv"></div>



<script>

// svg path for target icon
var targetSVG = "M9,0C4.029,0,0,4.029,0,9s4.029,9,9,9s9-4.029,9-9S13.971,0,9,0z M9,15.93 c-3.83,0-6.93-3.1-6.93-6.93S5.17,2.07,9,2.07s6.93,3.1,6.93,6.93S12.83,15.93,9,15.93 M12.5,9c0,1.933-1.567,3.5-3.5,3.5S5.5,10.933,5.5,9S7.067,5.5,9,5.5 S12.5,7.067,12.5,9z";

var map = AmCharts.makeChart( "chartdiv", {
  "type": "map",
  "theme": "light",
  "imagesSettings": {
    "rollOverColor": "#089282",
    "rollOverScale": 3,
    "backgroundColor":"#ffffff",
    "selectedScale": 3,
    "selectedColor": "#ffffff",
    "markerBorderThickness" : 3,
    "color": "#FDA633"
  },

  "areasSettings": {
    "unlistedAreasColor": "#15A892"
  },

  "dataProvider": {
    "map": "worldLow",
    "images": []
  }
} );


// a function that actual adds the city to map

setInterval(function addCity() {
      $.ajax({
      type:"POST",
      url:"record_count.php",
      dataType:"json",
      success:function(data){

		for(var x in data.images){	
			
			var city = data.images[x];
			city.svgPath = targetSVG;
			city.zoomLevel = 5;
			city.scale = 0.5;


	 		 map.dataProvider.images.push( city );
	  		map.validateData();
		}
	}
	});
}, 10000);

</script>
<style>
html,
body {
  width: 100%;
  height: 100%;
  margin: 0px;
}

#chartdiv {
  width: 100%;
  height: 100%;
}

</style>
