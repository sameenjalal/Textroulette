<?	
$new_member_message = 'Welcome to TextRoulette. You are being paired up with a stranger. Text \'end chat\' to quit and \'help\' for more options.';
	/** Returns the id of the member if found. If not found, it creates it and returns the id of the member. If DB fails, returns NULL */
	function get_phone_id($phone)
	{	
		global $new_member_message;

		//BUILD QUERY
		$query = sprintf("SELECT * FROM members WHERE phone=\"%s\"", mysql_real_escape_string($phone));

		//RUN QUERY
		$res = mysql_query($query);
		
		//PARSE RESULT
		//if db error
		if(!$res)
		{
			echo mysql_error();
			return NULL;
		}
		
		//GET NUMBER OF MATCHING PHONE NUMBERS
		$num_rows = mysql_num_rows($res);
		
		//IF NO MEMBER FOUND, ADD TO TABLE
		if($num_rows < 1)
		{
			//Add member to table
			$query = sprintf("INSERT INTO members SET phone=\"%s\"", mysql_real_escape_string($phone));
			mysql_query($query);

			$new_member = true;
			
			//get member object
			$query = sprintf("SELECT * FROM members WHERE phone=\"%s\"", mysql_real_escape_string($phone));
			$res = mysql_query($query);
		}
		
		//RETURN PHONE ID
		$row = mysql_fetch_assoc($res);
		if(isset($new_member) && $new_member)
			send_message($row['id'], $new_member_message);
		return $row['id'];
	}

	/** Returns the phone number of a member when given it's phone id */
	function get_phone_number($phone_id)
	{
		$query = sprintf("SELECT * FROM members WHERE id=\"%s\"", mysql_real_escape_string($phone_id));
		$res = mysql_query($query);
		if(!$res)
		{
			echo mysql_error();
			return NULL;
		}
		else
		{
			$num_rows = mysql_num_rows($res);
			if($num_rows < 1)
			{
				return NULL;
			}
			$row = mysql_fetch_assoc($res);
			return $row['phone'];
		}
	}


?>
