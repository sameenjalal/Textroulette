fromId<?php
	//SEND MESSAGE TO PARTNER OF ARG1 PHONE ID
	function send_message_to_partner($from_phone_id, $message) 
	{
		//LOAD TWILIO CONSTANTS
		global $AccountSid, $AuthToken, $TwilioNumber, $ApiVersion;
		
		//CHOP OFF TEXT OVER 160
		if(strlen($message) > 160)
			$message = substr($message, 0, 160);
		
		//FIND CORRESPONDING PAIR
		$pairsrow = mysql_fetch_assoc(mysql_query("SELECT * FROM pairs WHERE phone_id_1 = '$from_phone_id' OR phone_id_2 = '$from_phone_id'"));
		
		$to_phone_id= NULL;
		if($pairsrow['phone_id_2'] == $from_phone_id) {
			$to_phone_id= $pairsrow['phone_id_1'];
		}
		else {
			$to_phone_id= $pairsrow['phone_id_2'];
		}
		
		//FIND TO PHONE NUMBER
		$to_phone_number = get_phone_number($to_phone_id);
		
		//SEND MESSAGE
		$client = new TwilioRestClient($AccountSid, $AuthToken);
		
		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
			"POST", array(
			"To" => $to_phone_number,
			"From" => $TwilioNumber,
			"Body" => $message
		));

		//TWILIO ERRORS
		if($response->IsError)
		{
			echo "Error: ".$response->ErrorMessage. "\n<br />";
		}
		else
		{
			echo "Message to $to_phone_number:\n<br />";
			echo '<pre>';
			echo "$message\n";
			echo '</pre>';
		}	
	}

/** Sends the specified message to the specified phone_id */
	function send_message($to_phone_id, $message)
	{
		//LOAD TWILIO CONSTANTS
		global $AccountSid, $AuthToken, $TwilioNumber, $ApiVersion;
		
		//CHOP OFF TEXT OVER 160
		if(strlen($message) > 160)
			$message = substr($message, 0, 160);
		
		//FIND PHONE NUMBER
		$to_phone_number = get_phone_number($to_phone_id);

		//SEND MESSAGE
		$client = new TwilioRestClient($AccountSid, $AuthToken);
		
		$response = $client->request("/$ApiVersion/Accounts/$AccountSid/SMS/Messages",
			"POST", array(
			"To" => $to_phone_number,
			"From" => $TwilioNumber,
			"Body" => $message
		));

		//TWILIO ERRORS
		if($response->IsError)
		{
			echo "Error: ".$response->ErrorMessage."File: ".__FILE__. "\n<br />";
		}
		else
		{
			echo "Message to $to_phone_number:\n<br />";
			echo '<pre>';
			echo "$message\n";
			echo '</pre>';
		}
				
	}


?>
