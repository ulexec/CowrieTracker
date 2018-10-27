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
	<br/><br/></br>
	<br/>
<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);	
	
	class MyDB extends SQLite3 {
		function __construct(){
	 		$this->open('/opt/cowrie/cowrie.db');
		}
	}

	if (!isset($_GET['id'])) { 
		echo '<h3> No Analysis Record found for Specified Connection!</h3>';
	} else { 
		$cn= $_GET['id']; 

		$db1 = new MyDB();

		if(!$db1){
			echo $db1->lastErrorMsg();
			exit(0);   
		}
		
		$tablesquery = $db1->query("SELECT outfile, shasum FROM downloads where id =\"' . $cn . '\"");
		$hash = '';
		$outfile = '';
		if($row = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
			$hash = $row['shasum'];
			$outfile = $row['outfile'];
		}

		$post = array('apikey' => '3dda53c99e64d05a2041b439a20b566612fec65f4c67566d734bbfd71b880ac3','resource'=>$hash);

require_once('virustotal.class.php');
$vt = new virustotal('3dda53c99e64d05a2041b439a20b566612fec65f4c67566d734bbfd71b880ac3');
$res = $vt->checkFile($outfile,$hash);
		echo $res;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.virustotal.com/vtapi/v2/file/report');
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); // please compress data
		curl_setopt($ch, CURLOPT_USERAGENT, "gzip, My php curl client");
		curl_setopt($ch, CURLOPT_VERBOSE, 1); // remove this if your not debugging
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		 
		$result = curl_exec ($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
		print_r($result);				
		if ($status_code == 200) { // OK 
			$js = json_decode($result, true);
			$total = count($js['scans']);
			$arr = $js['scans'];
			
			echo '<div>';
			echo '<table class="table table-striped table-hover">';
			echo '<tr class="success">';
			echo '	<th class="text-center">Analysis information</th>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';

			echo '<div>';
			echo '<table class="table table-striped table-hover">';	
			echo '<tr>';
			echo '	<th class="text-center">SHA1</th>';
			echo '	<th class="text-left">'. $js['sha1'] . '</th>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th class="text-center">SHA256</th>';
			echo '	<th class="text-left">'. $js['sha256'] . '</th>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th class="text-center">MD5</th>';
			echo '	<th class="text-left">'. $js['md5'] . '</th>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th class="text-center">Submission URL</th>';
			echo '	<th class="text-l3ft"><a href="'.$js['permalink'].'">'. $js['permalink'] . '</a></th>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th class="text-center">Positive Rate</th>';
			echo '	<th class="text-left">'. $js['positives'] . '/' . $js['total'] . '</th>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';

			echo '<div style="width: auto; height: auto;">';
			echo '<table class="table table-striped table-hover">';
			echo '<tr class="info">';
			echo '	<th class="text-center">#</th>';
			echo '	<th class="text-center">Antivirus</th>';
			echo '  <th class="text-center">Result</th>';
			echo '</tr>';

			$i = 1;
			foreach($arr as $key => $va) {
				foreach($va as $k => $v) {
					if($k == 'result' && !empty($v)) {
						echo '<tr>';
						echo '<td class="text-center">' . $i++ . "</th>";
						echo '<td class="text-center">' . $key . "</th>";
						echo '<td class="text-center">' . $v . "</th>";
						echo '</tr>';
					}
				}
			}	
			
			echo '</table>';
			echo '</div>';
		} else {  // Error occured
		  print($result);
		}
		curl_close ($ch);
	}
?>
	</body>
</html>
