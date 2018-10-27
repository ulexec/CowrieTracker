<style>
	body {
		background-color: #000000;
		color: #ffffff;
	}
</style>
<body>
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


		$session_name = $_GET['id'];
		

	        $getty = $db->query("SELECT ttylog from ttylog where session='$session_name'");
		if($logs = $getty->fetchArray(SQLITE3_ASSOC)) {
			playLog($logs['ttylog']);
		}

		function playLog($log_name) {
			ob_implicit_flush(true);
			ob_end_flush();
			
			$cmd = "/opt/cowrie/bin/playlog -f /opt/cowrie/" . $log_name;

			$descriptorspec = array(
				0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
				2 => array("pipe", "w")    // stderr is a pipe that the child will write to
			);
			flush();
		
			$process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
			echo "<pre>";
			if (is_resource($process)) {
				while ($s = fgets($pipes[1])) {
					print $s;
					flush();
				}
			}
			echo "</pre>";
		}

?>
</body>
