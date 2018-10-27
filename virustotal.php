<html>
	<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
	<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
	<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
	<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css"/>
	<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
	<link href="bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="styles.css" rel="stylesheet" type="text/css"/>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.3.1/css/flag-icon.min.css" rel="stylesheet"/>
  <thead>
<head>
</head>
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
	    </li>
	    <li>
              <a href="downloads.php" >Captured Samples </a>

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


<?php
	class MyDB extends SQLite3 {
		function __construct(){
	 		$this->open('/opt/dionaea/var/dionaea/vtcache.sqlite');
		}
	}

	$db1 = new MyDB();

	if(!$db1){
		echo $db1->lastErrorMsg();
		exit(0);   
	} 
	$tablesquery = $db1->query("SELECT name FROM sqlite_master WHERE type='table';");

    	while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
        	echo $table['name'] . '<br />';
    	}

	$tablesquery = $db1->query("SELECT * FROM backlogfiles;");

    	while ($row = $tablesquery->fetchArray()) {
    		print_r($row);
	}
	echo '[+] DONE';
			

?>

<script>
var map = AmCharts.makeChart( "chartdiv", {
  "type": "map",
  "theme": "dark",
  "projection": "miller",

  "imagesSettings": {
    "rollOverColor": "#089282",
    "rollOverScale": 3,
    "selectedScale": 3,
    "selectedColor": "#089282",
    "color": "#13564e"
  },

  "areasSettings": {
    "unlistedAreasColor": "#15A892"
  },

  "dataProvider": <?php echo json_encode($arr)?> 
  
} );

// add events to recalculate map position when the map is moved or zoomed
map.addListener( "positionChanged", updateCustomMarkers );

// this function will take current images on the map and create HTML elements for them
function updateCustomMarkers( event ) {
  // get map object
  var map = event.chart;

  // go through all of the images
  for ( var x in map.dataProvider.images ) {
    // get MapImage object
    var image = map.dataProvider.images[ x ];

    // check if it has corresponding HTML element
    if ( 'undefined' == typeof image.externalElement )
      image.externalElement = createCustomMarker( image );

    // reposition the element accoridng to coordinates
    var xy = map.coordinatesToStageXY( image.longitude, image.latitude );
    image.externalElement.style.top = xy.y + 'px';
    image.externalElement.style.left = xy.x + 'px';
  }
}

// this function creates and returns a new marker element
function createCustomMarker( image ) {
  // create holder
  var holder = document.createElement( 'div' );
  holder.className = 'map-marker';
  holder.title = image.title;
  holder.style.position = 'absolute';

  // maybe add a link to it?
  if ( undefined != image.url ) {
    holder.onclick = function() {
      window.location.href = image.url;
    };
    holder.className += ' map-clickable';
  }

  // create dot
  var dot = document.createElement( 'div' );
  dot.className = 'dot';
  holder.appendChild( dot );

  // create pulse
  var pulse = document.createElement( 'div' );
  pulse.className = 'pulse';
  holder.appendChild( pulse );

  // append the marker to the map container
  image.chart.chartDiv.appendChild( holder );

  return holder;
}
</script>
	</body>
</html>
