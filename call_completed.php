<?php
	$fh = fopen("call-status-log", "a");
	fwrite($fh, print_r($_REQUEST, true));
	fwrite($fh, "\n");
	fclose($fh);

	require("connection.php");
	require("member.php");
	require("pairs.php");
	require("twilio.php");

	$phone_id = get_phone_id(@$_REQUEST['From']);

	$pairdata = get_pair($phone_id);

	if(starts_with($pairdata['data'], 'Conference'))
	{
		modify_pair($phone_id, 0, '');
	}


	function starts_with($haystack,$needle,$case=false) {
	    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
		    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
	}
?>
