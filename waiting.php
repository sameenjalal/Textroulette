<?php

//ADDS ENTRY TO WAITING, (ID, PHONE, BODY)
function add_waiting($phone_id, $body, $type) {
	//FILTER BODY
	$body = mysql_real_escape_string($body);

	//REMOVE OLD ENTRIES TO AVOID MULTIPLE WAITING
	mysql_query("DELETE FROM waiting WHERE phone_id=$phone_id");
	
	//ADD AS WAITING WITH LATEST TYPE
	mysql_query("INSERT INTO waiting (phone_id, type, body) VALUES ($phone_id, $type, '$body')");
	echo 'ADDED WAITING';
	return true;
}

//COUNTS NUMBER OF ENTRIES IN WAITING, RETURNS INT
function num_waiting($type) {
	$result = mysql_query("SELECT * FROM waiting where type=".mysql_real_escape_string($type));
	return mysql_num_rows($result);
}

//SELECT THE TOP ENTRY IN WAITING, REMOVES FROM WAITING, RETURNS ARRAY (PHONE ID, BODY)
function select_and_remove_waiting($type) {
	$result = mysql_query("SELECT * FROM waiting WHERE type = '$type'");
	$row = mysql_fetch_assoc($result);
	
	//Build Return Array
	$result_array = array("phone_id" => $row['phone_id'], "body" => $row['body']);
	
	//Remove Entry
	mysql_query("DELETE FROM waiting WHERE phone_id = '".$row['phone_id']."'");
	
	return $result_array;
}

?>
