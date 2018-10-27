<html>
	<script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
	<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css"/>
	<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
	<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
	<script src="https://www.amcharts.com/lib/3/serial.js"></script>
	<link href="bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="styles.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript" src="https://www.amcharts.com/lib/3/pie.js"></script>
  <thead>
<head>
</head>
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
              <a href="http://pwning.fun">Blog</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
	<div id="chartdiv"></div>

<?php
	class MyDB extends SQLite3 {
		function __construct(){
	 		$this->open('/opt/cowrie/cowrie.db');
		}
	}

	$db1 = new MyDB();

	if(!$db1){
		echo $db1->lastErrorMsg();
		exit(0);   
	} 

	if (isset($_GET['page'])) { 
		$page  = $_GET['page']; 
	} else { 
		$page=1; 
	}; 

	$results_per_page = 10;
	$start_from = ($page-1) * $results_per_page;	
	$BigArray = array();

	$sql = 'SELECT strftime("%Y-%m-%dT%H", starttime) as startt, COUNT(DISTINCT(ip)) count FROM sessions GROUP BY strftime("%Y-%m-%dT%H", starttime)';
	$result = $db1->query($sql);

	$Index=0;

	while($row=$result->fetchArray(SQLITE3_ASSOC)) {
		$time = $row['startt'];
		$date = strtok($time, 'T');
		$hour = explode('T', $time);
		$hour = strtok($hour[1], ':');
		$ip = $row['ip'];
		$count = $row['count'];
	
		$rowdate = sprintf("%s %s", $date, $hour);

		$BigArray[$Index] = array(
			 'date' => $rowdate,
			 'column-1' => $count
		 );

		$Index++;
	}
?>

<script>
		AmCharts.makeChart("chartdiv",
				{
					"type": "serial",
					"categoryField": "date",
					"dataDateFormat": "YYYY-MM-DD HH",
					
					"handDrawScatter": 1,
					"theme": "dark",
					"categoryAxis": {
						"minPeriod": "hh",
						"parseDates": true
					},
					"chartCursor": {
						"enabled": true,
						"categoryBalloonDateFormat": "JJ:NN"
					},
					"chartScrollbar": {
						"enabled": true
					},
					"trendLines": [],
					"graphs": [
						{
							"bullet": "round",
							"id": "AmGraph-1",
							"title": "unique IPs",
							"valueField": "column-1"
						}
					],
					"guides": [],
					"valueAxes": [
						{
							"id": "ValueAxis-1",
							"title": "Number of Attacks"
						}
					],
					"allLabels": [],
					"color": "#000000",
					"balloon": {},
					"legend": {
						"enabled": true,
						"color": "#000000" ,
						"useGraphSettings": true
					},
					"titles": [
						{
							"id": "Title-1",
							"size": 15,
							"text": "Attack Traffic"
						}
					],
					"dataProvider": <?php echo json_encode(array_values($BigArray))?>
				}
			);

</script>
	</body>
</html>
