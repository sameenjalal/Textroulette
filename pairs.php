<?
	function add_pair($phone_id_1, $phone_id_2, $type, $data) {
		$query = "DELETE FROM waiting WHERE phone_id IN ($phone_id_1, $phone_id_2)";
		$result = mysql_query($query) or die(mysql_error());

		$data = mysql_real_escape_string($data);
		$query = "INSERT INTO pairs (phone_id_1, phone_id_2, type, data) VALUES ($phone_id_1, $phone_id_2, $type, '$data')";
		$result = mysql_query($query) or die (mysql_error());
		return true;
	}

	function is_pair($phone_id) {
		$query = "SELECT * FROM pairs WHERE phone_id_1 = '$phone_id' OR phone_id_2 = '$phone_id'";
		$numResults = mysql_num_rows(mysql_query($query));
		
		if($numResults > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function modify_pair($phone_id, $type, $data) 
	{
		$query = "UPDATE pairs SET type = '$type', data = '$data' WHERE phone_id_1 = '$phone_id' OR phone_id_2 = '$phone_id'";
		mysql_query($query);
		return true;
		
	}
	
	function get_pair($phone_id)
	{
		$query = "SELECT * FROM pairs WHERE phone_id_1 = '$phone_id' OR phone_id_2 = '$phone_id'";
		$result = mysql_fetch_assoc(mysql_query($query));
		
		return $result;
	}
	
	function remove_pair($phone_id)
	{
		$query = sprintf("DELETE FROM pairs WHERE phone_id_1=\"%s\" OR phone_id_2=\"%s\"", mysql_real_escape_string($phone_id), mysql_real_escape_string($phone_id));
		$res = mysql_query($query);

		if(!$res)
		{
			echo mysql_error();
			return NULL;
		}
		else
		{
			return TRUE;
		}
	}

?>
