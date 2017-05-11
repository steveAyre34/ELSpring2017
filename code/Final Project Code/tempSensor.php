<style>
table, th, td{
	border: 1px solid black;
}
</style>
<body style = "background: #e0e3e5">
<h1>Last 10 recorded Temperatures</h1>
<div>
<?php
	$db = new SQLite3("/home/pi/db_files/tempData.db");
	$db_retrieved1 = new SQLite3("/home/pi/db_files/retrieveData1.db");
	$db_retrieved2 = new SQLite3("/home/pi/db_files/retrieveData2.db");
	$result = $db->query("select * from data_collection ORDER BY date DESC, time DESC LIMIT 10");
	echo "<div style = 'width: 33%; float: left'>";
	echo "<h2>Window</h2>";
	echo "<table id = 'last_10_table'><thead>";
	echo "<tr><th>Date</th><th>Time</th><th>Temperature</th><th>Humidity</th></tr></thead><tbody>";

	while($row = $result->fetchArray()){
		$normal = date( 'g:i A', strtotime( $row["time"] ) );
		echo "<tr><td>" .  $row["date"] . "</td><td>" .  $normal . "</td><td>" 
		.  $row["temperature"] . "</td><td>" . $row["humidity"];
		echo "</td></tr>";
	}
	echo "</tbody></table>";
	echo "</div>";
	//-----------------------------------------------------
	$result = $db_retrieved1->query("select * from data_collection ORDER BY date DESC, time DESC LIMIT 10");
	echo "<div style = 'width: 33%; float: right'>";
	echo "<h2>Bathroom</h2>";
	echo "<table id = 'last_10_table'><thead>";
	echo "<tr><th>Date</th><th>Time</th><th>Temperature</th><th>Humidity</th></tr></thead><tbody>";

	while($row = $result->fetchArray()){
		$normal = date( 'g:i A', strtotime( $row["time"] ) );
		echo "<tr><td>" .  $row["date"] . "</td><td>" .  $normal . "</td><td>" 
		.  $row["temperature"] . "</td><td>" . $row["humidity"];
		echo "</td></tr>";
	}
	echo "</tbody></table>";
	echo "</div>";
	//-----------------------------------------------------
	$result = $db_retrieved2->query("select * from data_collection ORDER BY date DESC, time DESC LIMIT 10");
	echo "<div style = 'width: 33%; float: right'>";
	echo "<h2>Living Room</h2>";
	echo "<table id = 'last_10_table'><thead>";
	echo "<tr><th>Date</th><th>Time</th><th>Temperature</th><th>Humidity</th></tr></thead><tbody>";

	while($row = $result->fetchArray()){
		$normal = date( 'g:i A', strtotime( $row["time"] ) );
		echo "<tr><td>" .  $row["date"] . "</td><td>" .  $normal . "</td><td>" 
		.  $row["temperature"] . "</td><td>" . $row["humidity"];
		echo "</td></tr>";
	}
	echo "</tbody></table>";
	echo "</div>";
?>
</div>
<div style = "padding: 10px"></div>
<div style = "width: 5%">
<div>
	<label id = "date_temp_label">Date</label>
	<input id = "date_temp" type = "date">
	<label id = "to_date_label" style = "display: none">To</label>
	<input id = "to_date" type = "date" style = "display: none">
</div>
<div>
	<label>Range</label>
	<input id = "check_range" type = "checkbox" onchange = "range()">
</div>
<div style = "margin-top: 20%">
	<label>Type</label>
	<select id = "type">
	<option selected = "selected" value = "temp">Temperature(F)</option>
	<option value = "humidity">Humidity</option>
	</select>
</div>
</div>
<div style = "padding: 10px"></div>
<button onclick = "generateGraph()">Submit Query</button>
<label onclick = "showSingular('window')" style = "background: #42a1f4; color: #ffffff; padding: 5px 5px 5px 5px">Window</label>
<label onclick = "showSingular('bathroom')" style = "background: #f47d42; color: #ffffff; padding: 5px 5px 5px 5px">Bathroom</label>
<label onclick = "showSingular('living')" style = "background: #686b4f; color: #ffffff; padding: 5px 5px 5px 5px">Living Room</label>
<label onclick = "showSingular('all')" style = "background: #000000; color: #ffffff; padding: 5px 5px 5px 5px">All</label>
<div style="width:75%">
			<div id = "chart_container">
				<canvas id="canvas" height="450" width="600"></canvas>
			</div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="Chart.js"></script>
<script>
var date = $("#date_temp").val();
var time = $("#time_temp").val();
var info = [date, time];
var datasets = [];
var timeLabels = [];
function generateGraph(){ //generates the appropriate data based on user interface criteria
	var date = $("#date_temp").val();
	var time = $("#time_temp").val();
	var range = 0;
	if($("#check_range").is(":checked")){
		range = 1;
	}
	var to_date = $("#to_date").val();
	var type = $("#type").val();
	var info = [date, time, range, to_date, type];
    $.ajax({ //ajax call returns data for all three raspberry pis
    type: "POST",
    url: "generate_graph_info.php",
    data: {id_info: info},
    dataType: "json", // Set the data type so jQuery can parse it for you
    success: function (data) { //uses Chart.js to display a line graph with labels and either temperature or humidity
		datasets[0] = {
					label: "Temperature",
					fillColor : "rgba(66, 161, 244, 0.5)",
					strokeColor : "rgba(66, 161, 244, 0.5)",
					pointColor : "rgba(66, 161, 244, 0.5)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
					data : data[0][2]
				}
		datasets[1] = {
					label: "Temperature",
					fillColor : "rgba(244, 125, 66, 0.5)",
					strokeColor : "rgba(244, 125, 66, 0.5)",
					pointColor : "rgba(244, 125, 66, 0.5)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
					data : data[1][2]
				}
		datasets[2] = {
					label: "Temperature",
					fillColor : "rgba(104, 107, 79, 0.5)",
					strokeColor : "rgba(104, 107, 79, 0.5)",
					pointColor : "rgba(104, 107, 79, 0.5)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(220,220,220,1)",
					data : data[2][2]
				}
		timeLabels[0] = data[0][1];
		timeLabels[1] = data[1][1];
		timeLabels[2] = data[2][1];
        var lineChartData = {
				labels : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
				datasets : datasets
		}
		$('#chart_container').html('');
		$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});
    }
});
}
var lineChartData = {
							labels : ["Set 1", "Set 2"],
							datasets : [
								{
									label: "My First dataset",
									fillColor : "rgba(66, 161, 244, 0.5)",
									strokeColor : "rgba(66, 161, 244, 0.5)",
									pointColor : "rgba(66, 161, 244, 0.5)",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "rgba(220,220,220,1)",
									data : [0, 0]
								}
							]

						}
						$('#chart_container').html('');
						$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
						var ctx = document.getElementById("canvas").getContext("2d");
						window.myLine = new Chart(ctx).Line(lineChartData, {
							responsive: true
						});
function showSingular(dataSet){ //filters table to show only one of the pi's data
	if(dataSet == "window"){
		if(timeLabels[0].length != 0){
			var lineChartData = {
				labels : timeLabels[0],
				datasets : [datasets[0]]

				}
				$('#chart_container').html('');
				$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myLine = new Chart(ctx).Line(lineChartData, {
					responsive: true
				});
		}
		else{
			alert("No data");
		}
	}
	else if(dataSet == "bathroom"){
		if(timeLabels[1].length != 0){
			var lineChartData = {
				labels : timeLabels[1],
				datasets : [datasets[1]]

				}
				$('#chart_container').html('');
				$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myLine = new Chart(ctx).Line(lineChartData, {
					responsive: true
				});
		}
		else{
			alert("No data");
		}
	}
	else if(dataSet == "living"){
		if(timeLabels[2].length != 0){
			var lineChartData = {
				labels : timeLabels[2],
				datasets : [datasets[2]]

				}
				$('#chart_container').html('');
				$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
				var ctx = document.getElementById("canvas").getContext("2d");
				window.myLine = new Chart(ctx).Line(lineChartData, {
					responsive: true
				});
		}
		else{
			alert("No data");
		}
	}
	else{
		var lineChartData = {
				labels : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
				datasets : datasets
		}
		$('#chart_container').html('');
		$('#chart_container').html('<canvas id="canvas" height="450" width="600"></canvas>');
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});
	}
}
function range(){ //displays either two dates or one for a range or no range of dates
	if($("#check_range").is(":checked")){
		$("#date_temp_label").html("From");
		$("#to_date_label").show();
		$("#to_date").show();
	}
	else{
		$("#date_temp_label").html("Date");
		$("#to_date_label").hide();
		$("#to_date").hide();
	}
}
</script>