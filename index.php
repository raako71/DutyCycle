<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>raako</title>
    <style>
        p, .success2 {text-align: center;font-family: sans-serif;}
        h1 {font-family: 'Indie Flower', cursive;text-align: center;}
		#mainBox {max-width: 300px; margin: 30px auto; background-color: #dddddd;padding: 20px;}
		.topBox { border-style: solid;border-width: 2px;}
		.bottomBox {width: 80px; line-height: 30px; height: 30px; margin: auto}
		input[type=text] { width: 50px;}
		.success {background: #f3ff3c; padding: 2px;opacity:0; height: 50px;transition:height 3s, opacity 0.4s;}
		.success2 {opacity:1; height: 34px; transition: opacity 1s, margin-block-start 1s, height 3s;, display: block; margin-block-start: 1em;}
		#time {height:18px}
		
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
			//if($result == $a || $result == $b) echo "<br>Success";
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
        <p style="font-style: italic;">O3 Duty Cycle</p>
		<form method="post" class="redirect" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <p>Run for <input type='text' value='<?php echo $array[runTime] ?>' name='runTime'>  minutes</p> 
		<p>Sleep for <?php echo "<input type='text' value='$array[period]' name='period'/>"; ?> minutes</p> 
        <p>Active  <input type='checkbox' <?php if($array[active] == 1) echo "checked";?> value='true' name='active'/> </p>
		<p><input type="submit" value="Submit"></p>
		<p id='time'> </p>
		<!-- Next cycle in <?php echo $array[nextRun] - strtotime("now") ?> seconds -->
		<div class="success"><p>Updated</p></div>
		</div>
		<div class="bottomBox" style="background-color: #0eff58">
		<a href='javascript:void(0)' style="display: block" class='button' var='On'><p>run</p></a>
		</div>
		<?php if($result == $a) echo '<div class="success2">Success</div>'; ?>
		<div class="bottomBox" style="background-color: #ff2576">
		<a href='javascript:void(0)' style="display: block" class='button' var='Off'><p>Stop</p></a>
		<input type="hidden" class="post" name="command">
		</div>
		<?php if($result == $b) echo '<div class="success2">Success</div>'; ?>
    </div>    

<script type="text/javascript">
$(".button").click(function() {
    var link = $(this).attr('var');
    $('.post').attr("value",link);
    $('.redirect').submit();
});
$(document).ready(show());

function show(){
<?php if ($change == 1) echo "$('.success').css('opacity', '1');"?>
setTimeout(hide, 1000);
}
function hide(){
	$('.success').css('opacity', '0');
	//$('.success').css('height', '0');
	$('.success2').css('opacity', '0');
	$('.success2').css('height', '0');
	$('.success2').css('margin-block-start', '0');
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

