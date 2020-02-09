<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>raako</title>
    <style>
        a, p, h3, .success2 {text-align: center;font-family: monospace; color: #04ff00;}
		a{color: black;}
		input {background-color: black;color: #04ff00;border:1px solid #04ff00;text-align: center;}
		#mainBox {max-width: 300px; margin: 30px auto;padding: 20px; border: solid #04ff00;}
		.topBox { border-style: solid;border-width: 2px; border: solid #04ff00;}
		.bottomBox {width: 80px; line-height: 30px; height: 30px; margin-top: 18px; margin-left: auto; margin-right: auto;}
		input[type=text] { width: 50px;}
		.success2 {colour:red;background-color:white;}
		#time {height:18px}
		body { background: black;}
		
    </style>
	<?php
	$strJsonFileContents = file_get_contents("/home/user/data.json");
	// Get the contents of the JSON file 
	// Convert to array 
	$array = json_decode($strJsonFileContents, true);
	
		$put = 0;
		$result = "empty";
		$success = 0;
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$runTime = test_input($_POST["runTime"]);
			$period = test_input($_POST["period"]);
			$active = test_input($_POST["active"]);
			
			if (!filter_var($runTime, FILTER_VALIDATE_INT) === false && $runTime != $array[runTime] && 11 > $runTime && $runTime > 0)
			{
			$array[runTime] = $runTime;
			$put = 1;
			}
			
			if (!filter_var($period, FILTER_VALIDATE_INT) === false && $period != $array[period] && 600 > $period && $period > 0)
			{
			$array[period] = $period;
			$put = 1;
			}
			
			if (filter_var($active, FILTER_VALIDATE_BOOLEAN) === !$array[active])
			{
			if( $array[active] == True) $array[active] = 0;
			else $array[active] = True;
			$put = 1;
			}
			
			if($put == 1){
				$strJsonFileContents = json_encode($array);
				file_put_contents("/home/user/data.json", $strJsonFileContents);
				$put = 0;
				$change = 1;
			}
			$result = hook($_POST["command"]);
			$a = "Congratulations! You've fired the SwitchOn event";
			$b = "Congratulations! You've fired the SwitchOff event";
		}
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	

	function hook($data) {
		if($data == On) $ch = curl_init('https://maker.ifttt.com/trigger/SwitchOn/with/key/***');
		else if($data == Off) $ch = curl_init('https://maker.ifttt.com/trigger/SwitchOff/with/key/***');
		else return;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		return $result;
	}
	?>
	

	
</head>
<body>
    <div id="mainBox">
		<div class="topBox">
        <h3 style="font-style: italic;">O3 Duty Cycle</h3>
		<form method="post" class="redirect" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <p>Run for <input type='text' value='<?php echo $array[runTime] ?>' name='runTime'>  minutes</p> 
		<p>Sleep for <?php echo "<input type='text' value='$array[period]' name='period'/>"; ?> minutes</p> 
        <p>Active  <input type='checkbox' <?php if($array[active] == 1) echo "checked";?> value='true' name='active'/> </p>
		<p id='submit'>
		<?php if ($change == 1) echo "UPDATED"; else echo '<input type="submit" value="Submit">';?>
		</p>
		<p id='time'> </p>
		<!-- Next cycle in <?php echo $array[nextRun] - strtotime("now") ?> seconds -->
		</div>
		<div class="bottomBox" style="background-color: #0eff58">
		<a href='javascript:void(0)' style="display: block" class='button' var='On' id="RUN"><?php if($result == $a) echo '<div class="success2">Success</div>'; else echo 'RUN'; ?></a>
		</div>
		
		<div class="bottomBox" style="background-color: #ff2576">
		<a href='javascript:void(0)' style="display: block" class='button' var='Off' id="STOP"><?php if($result == $b) echo '<div class="success2">Success</div>'; else echo 'STOP'; ?></a>
		</div>
		<input type="hidden" class="post" name="command">
		
    </div>    

<script type="text/javascript">
$(".button").click(function() {
    var link = $(this).attr('var');
    $('.post').attr("value",link);
    $('.redirect').submit();
});
$(document).ready(show());

function show(){

setTimeout(hide, 4000);
}
function hide(){
	document.getElementById('RUN').innerHTML = 'RUN';
	document.getElementById('STOP').innerHTML = 'STOP';
	document.getElementById('submit').innerHTML = '<input type="submit" value="Submit">';
};

// Update the count down every 1 second
var x = setInterval("setTime();", 1000);

function setTime(){

  var now = new Date().getTime()/1000;

  var distance = parseInt(<?php echo $array[nextRun] ?> - now);
    
  document.getElementById('time').innerHTML = "<p> Next cycle in " + distance + " seconds</p>";
    
  if (distance < 0) {
    clearInterval(x);
    document.getElementById('time').innerHTML = "<p> Timer has run down</p>";
  }
}
</script>

</body>
