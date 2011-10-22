<?php
	//ENTER CALL INTO LOG
	$fh = fopen("log", "a");
	fwrite($fh, print_r($_REQUEST, true));
	fwrite($fh, "\n");
	fclose($fh);

	require("connection.php");
	require("member.php");
	require("pairs.php");
	require("twilio.php");
	
	//GET PHONE ID
	$phone_id = get_phone_id(@$_REQUEST['From']);

	//CHECK IF PAIR EXISTS
	$pairdata = get_pair($phone_id);
	
	header('Content-type: text/xml');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

	//IF PAIR EXISTS AND CONFERENCE ACCEPTED
	if(starts_with($pairdata['data'], 'Conference'))
	{
		echo "<Response>\n";
		echo "<Say>Please wait for your partner to connect.</Say>\n";
		echo "<Dial>\n";
		echo "<Conference>".$pairdata['data']."</Conference>\n";
		echo "</Dial>\n";
		echo "</Response>\n";
	}
	//ELSE PAIR DOESNT EXIST OR NOT CONFERENCED
	else
	{
		echo "<Response>\n";
		echo "<Say>Please use the offer voice option in the text chat before making a call.</Say>\n";
		echo "</Response>\n";
	}
	
	function starts_with($haystack,$needle,$case=false) {
	    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
		    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
	}
	//TODO WHAT HAPPENS WHEN SOMEONE CALLS WITHOUT EVER TEXING
	
?>
