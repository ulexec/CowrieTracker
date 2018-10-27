<html>
	<link href="bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.3.1/css/flag-icon.min.css" rel="stylesheet"/>
	<script src="https://www.amcharts.com/lib/3/ammap.js"></script>
	<script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
	<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
	<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
	<link rel="stylesheet" href="styles.css" type="text/css" media="all" />
	<script src="https://www.amcharts.com/lib/3/themes/none.js"></script>

<head></head>
	<body>

	<div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="index.php" class="navbar-brand">Cowrie Tracker</a>
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
	   <li>
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

	ini_set('display_errors',1); 
	error_reporting(E_ALL);	
	
	class MyDB extends SQLite3 {
		function __construct(){
	 		$this->open('/opt/cowrie/cowrie.db');
		}
	}

	$db = new MyDB();

	if(!$db){
		echo $db->lastErrorMsg();
		exit(0);   
	} 


	if (isset($_GET['page'])) { 
		$page  = $_GET['page']; 
	} else { 
		$page=1; 
	}; 

	$results_per_page = 25;
	$start_from = ($page-1) * $results_per_page;	

	$sql = "SELECT COUNT(shasum) AS total FROM downloads"; 
	$result = $db->query($sql);
	$row = $result->fetchArray(SQLITE3_ASSOC);
	$total_pages = ceil($row['total'] / $results_per_page);
	$Index = 0;
		
	echo '<br /><br />';
	
	echo '<div>';
	echo '<table class="table table-striped table-hover" width="100%" border="0" cellspacing="0" cellpadding="0">';
	echo '<tr>';
	echo '	<th class="text-center">Timestamp</th>';
	echo '	<th class="text-center">RHOST</th>';
	echo '	<th class="text-center">Country</th>';
	echo '	<th class="text-center">SHA256</th>';
	echo '  <th class="text-center">Replay</th>';
	echo '  <th class="text-center">VT Analyis</th>';
	echo '</tr>';

	$tablesquery = $db->query("SELECT session, outfile, timestamp, url, shasum FROM downloads ORDER BY timestamp DESC LIMIT $start_from, $results_per_page");
  	while($tables = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
         	$ses = $tables['session'];
		$outfile = $tables['outfile'];
		
		$ipquery = $db->query("SELECT ip FROM sessions WHERE id='" . $tables['session'] . '\'');
		if($ip = $ipquery->fetchArray(SQLITE3_ASSOC)) {
			$result = json_decode(file_get_contents("http://reallyfreegeoip.org/json/" . $ip['ip']));
			$cmd = "file /opt/cowrie/" . $outfile;
			$process = shell_exec($cmd);
		//	if(strstr($process, "ELF")){
				$SmallArray[$Index] = array(
					 'zoomLevel' => 5,
					 'scale' => 0.5,
					 'title' => $ip['ip'],
					 'latitude' => $result->latitude,
					 'longitude' => $result->longitude 
					 );
				 $Index++;

				echo '<tr>';
				echo '	<td class="text-center">' . $tables['timestamp'] .'</td>';
				echo '  <td class="text-center">' . $ip['ip'] . '</td>';
				echo '  <td class="text-center"><span class="flag-icon flag-icon-' . strtolower($result->country_code) .'"></span></td>';
				echo '	<td class="text-center"><a href="' . $tables["outfile"] . '\">' . $tables['shasum'] .'</a></td>';
				
				echo '<td class="text-center"><a href="replay.php?id='.$tables['session'].'">Available</a></td>';
				echo '<td class="text-center"><a href="https://www.virustotal.com/en/file/'.$tables['shasum'].'/analysis/">Available</a></td>';	

				echo '</tr>';
				
		//	}
		}
    	}
	echo '</table>';
	echo '</div>';
	/*
	echo '<br /><br />';
	$tablesquery = $db->query("SELECT * FROM sessions");
  	while($tables = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
		print_r($tables);
	}
	*/
	$arr = [
		"map" => "worldLow",
		"images" => $SmallArray
	];
	echo '<div align="center">';
	echo '<ul class="pagination" >';
	echo '<li class="disabled">';

	$prev = $page-1;
	$next = $page+1;

	if($page != 1){ 
		echo '<li class="active">';
		echo '<a href="downloads.php?page='. $prev.'">';
	} else {
		echo '<li class="disabled">';
		echo '<a>';
	}
	echo '&laquo;</a></li>';

	 if ($total_pages < 3) {
                for ($i=1; $i<= $total_pages; $i++) {  // print links for all pages
                    if($i == 1) echo '<li class="active">';
                    else echo '<li>';

                    echo "<a href='downloads.php?page={$i}'".' class="btn btn-default">'.$i."</a></li>";
                };

                echo '<li class="disabled">';
                echo '<a> ... </a>';
                echo '</li>';

        }else{
                for ($i=1; $i<= 3; $i++) {  // print links for all pages
                    if($i == 1) echo '<li class="active">';
                    else echo '<li>';

                    echo "<a href='downloads.php?page={$i}'".' class="btn btn-default">'.$i."</a></li>";
                };

                echo '<li class="disabled">';
                echo '<a> ... </a>';
                echo '</li>';

                if($page >= 3 && $page < $total_pages-2 ) {
                        for($i=$page+1; $i<=$page+3; $i++) {
                                if($i == $page) echo '<li class="active">';
                                else echo '<li>';

                                echo "<a href='downloads.php?page={$i}'".' class="btn btn-default">'.$i."</a></li>";
                        }

                        echo '<li class="disabled">';
                        echo '<a> ... </a>';
                        echo '</li>';
                }else{
                        for ($i = $total_pages-2; $i<= $total_pages; $i++) {  // print links for all pages
                                if($i == $page) echo '<li class="active">';
                                else echo '<li>';

                                echo "<a href='downloads.php?page={$i}'" . ' class="btn btn-default">'.$i."</a></li>";
                        };

                }
        }

	if($page != $total_pages){
		echo '<li class="active">';	
		echo '<a href="downloads.php?page=' . $next . '">';
	}else{
		echo '<li class="disabled">';	
		 echo '<a>';
	}
	echo '&raquo;</a></li>';
	
	echo '</ul>';
	echo '</div>';
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
