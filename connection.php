<?
	$db_user = "vverna";
	$db_password = "Verma123";

	$db_host = "mysql.vverma.net";
	$db_name = "text_roulette";

	mysql_connect($db_host, $db_user, $db_password) or die("Fail");
	mysql_select_db($db_name);

	$ApiVersion = '2010-04-01';
	$AccountSid = 'AC606e1cfc219ffe775e69d5736766cb30';
	$AuthToken = 'b89f7f39d1142cf3d4ea23173ee7ae97';
	$TwilioNumber = '19177254001';
?>
