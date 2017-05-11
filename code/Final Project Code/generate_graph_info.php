<?php
	$info = $_POST["id_info"];
	$type = "temperature";
	if($info[4] == "humidity"){
		$type = "humidity";
	}
	//global array for json return
	if($info[2] == 0){ //if range not selected, find data based on one date
		$data_info_all = array();
		//fix date format
		$date = $info[0];
		$date_array = explode("-", $date);
		$year = $date_array[0];
		$month = $date_array[1];
		$day = $date_array[2];
		$new_date = $month . "/" . $day . "/" . $year;
		//--------------------------------
		$new_date = date("m/d/y", strtotime($new_date));
		$sql = "select * from data_collection where date = '$new_date'";
		//----------------------------------------
		//fix temperature with range
		$db = new SQLite3("/home/pi/db_files/tempData.db");
		$result = $db->query($sql);
		$dates_array = array();
		$times_array = array();
		$sensor_array = array();
		while($row = $result->fetchArray()){
			array_push($dates_array, $row["date"]);
			$normal = date( 'g:i A', strtotime( $row["time"] ) );
			array_push($times_array, $normal);
			array_push($sensor_array, $row[$type]);
		}
		$info1 = array($dates_array, $times_array, $sensor_array);
		array_push($data_info_all, $info1);
		//second data------------------------
		$db2 = new SQLite3("/home/pi/db_files/retrieveData1.db");
		$result = $db2->query($sql);
		$dates_array = array();
		$times_array = array();
		$sensor_array = array();
		while($row = $result->fetchArray()){
			array_push($dates_array, $row["date"]);
			$normal = date( 'g:i A', strtotime( $row["time"] ) );
			array_push($times_array, $normal);
			array_push($sensor_array, $row[$type]);
		}
		$info2 = array($dates_array, $times_array, $sensor_array);
		array_push($data_info_all, $info2);
		//third data------------------------
		$db3 = new SQLite3("/home/pi/db_files/retrieveData2.db");
		$result = $db3->query($sql);
		$dates_array = array();
		$times_array = array();
		$sensor_array = array();
		while($row = $result->fetchArray()){
			array_push($dates_array, $row["date"]);
			$normal = date( 'g:i A', strtotime( $row["time"] ) );
			array_push($times_array, $normal);
			array_push($sensor_array, $row[$type]);
		}
		$info3 = array($dates_array, $times_array, $sensor_array); //package and send back to tempSensor.php
		array_push($data_info_all, $info3);
		echo json_encode($data_info_all);
		exit();
	}
	else{ //else, use range date to get average temperatures or humidity for each day in range
		$data_info_all = array();
		//fix date format
		$date = $info[0];
		$date_array = explode("-", $date);
		$year = $date_array[0];
		$month = $date_array[1];
		$day = $date_array[2];
		$new_from_date = $month . "/" . $day . "/" . $year;
		//fix to date format
		$date = $info[3];
		$date_array = explode("-", $date);
		$year = $date_array[0];
		$month = $date_array[1];
		$day = $date_array[2];
		$new_to_date = $month . "/" . $day . "/" . $year;
		 //Set up range to check
		$begin = new DateTime($new_from_date);
		$end = new DateTime($new_to_date);
		$end = $end->modify( '+1 day' ); 
		$interval = new DateInterval('P1D');
		$daterange = new DatePeriod($begin, $interval ,$end);
		$dates_1 = array();
		$times_1 = array();
		$sensors_1 = array();
		$dates_2 = array();
		$times_2 = array();
		$sensors_2 = array();
		$dates_3 = array();
		$times_3 = array();
		$sensors_3 = array();
		foreach($daterange as $temp_date){
			$date = $temp_date->format("m/d/y");
			$new_date = date("m/d/y", strtotime($date));
			//first data
			$db = new SQLite3("/home/pi/db_files/tempData.db");
			$result = $db->query("select avg(" . $type . ") as data from data_collection where date = '$new_date'");
			$row = $result->fetchArray();
			array_push($dates_1, $new_date);
			array_push($times_1, $new_date);
			array_push($sensors_1, $row["data"]);
			//------------------------------------------second data------------------------
			$db = new SQLite3("/home/pi/db_files/retrieveData1.db");
			$result = $db->query("select AVG(" . $type . ") as data from data_collection where date = '$new_date'");
			$row = $result->fetchArray();
			array_push($dates_2, $new_date);
			array_push($times_2, $new_date);
			array_push($sensors_2, $row["data"]);
			//------------------------------------------------third data-------------------------
			$db = new SQLite3("/home/pi/db_files/retrieveData2.db");
			$result = $db->query("select AVG(" . $type . ") as data from data_collection where date = '$new_date'");
			$row = $result->fetchArray();
			array_push($dates_3, $new_date);
			array_push($times_3, $new_date);
			array_push($sensors_3, $row["data"]);
		}
		$info1 = array($dates_1, $times_1, $sensors_1); //package and send back to tempSensor.php
		array_push($data_info_all, $info1);
		$info2 = array($dates_2, $times_2, $sensors_2);
		array_push($data_info_all, $info2);
		$info3 = array($dates_3, $times_3, $sensors_3);
		array_push($data_info_all, $info3);
		echo json_encode($data_info_all);
		exit();
	}
?>
