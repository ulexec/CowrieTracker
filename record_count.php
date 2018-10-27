
<?php
 header('Content-type:text/JSON');

	class MyDB extends SQLite3 {
		function __construct(){
	 		$this->open('/opt/dionaea/var/dionaea/dionaea.sqlite');
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

	$results_per_page = 15;
	$start_from = ($page-1) * $results_per_page;	

	$sql = "SELECT COUNT(connection_timestamp) AS total FROM connections"; 
	$result = $db1->query($sql);
	$row = $result->fetchArray(SQLITE3_ASSOC);
	$total_pages = ceil($row['total'] / $results_per_page);
	
	$Index = 0;
	$cons = $db1->query("SELECT connection_transport, connection_protocol, connection_timestamp, local_host, local_port, remote_host, remote_port FROM connections ORDER BY connection_timestamp DESC LIMIT $start_from, $results_per_page");
	while($row = $cons->fetchArray(SQLITE3_ASSOC)) {
		$result = json_decode(file_get_contents("http://freegeoip.net/json/" . $row['remote_host']));
		$SmallArray[$Index] = array(
			 'zoomLevel' => 5,
			 'scale' => 0.5,
			 'title' => $row['remote_host'],
			 'latitude' => $result->latitude,
			 'longitude' => $result->longitude 
			 );
		 $Index++;
	}

	$arr = [
		"map" => "worldLow",
		"images" => $SmallArray
	];
	echo json_encode($arr);

?>


