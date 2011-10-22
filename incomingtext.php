<?php
	//TYPE 0 = text
	//TYPE 1 = tictactoe
	//TYPE 2 = connect4
	//TYPE 3 = voicechat
	//TODO MAYBE CHANGE NUMBERS INTO STRING VALUES
	//TODO MAYBE WAITING TABLE DOESNT NEED A BODY FIELD
	
	function string_equals($str1, $str2) {
		$str1 = remove_punctuation($str1);
		$str2 = remove_punctuation($str2);

		$result = strcasecmp($str1, $str2);
		
		if($result == 0) {
			return true;
		}
		else {
			return false;
		}
	}

	function remove_punctuation($str)
	{
		$chars = array('.', ',', ':', ';', ' ', '\\', '/', '?', '<', '>', '@', '#', "\$", '&', '%', '*', '(', ')', '!', '\'');

		return str_replace($chars, '', $str);
	}

	
	//ENTER CALL INTO LOG
	$fh = fopen("log", "a");
	fwrite($fh, print_r($_REQUEST, true));
	fwrite($fh, "\n");
	fclose($fh);
	
	//MAKE CONNECTION TO DATABASE AND LOAD TWILIO CONSTANTS
	require("connection.php");
	
	//LOAD MEMBER TABLE FUNCTIONS
	require("member.php");
	
	//LOAD WAITING TABLE FUNCTIONS
	require("waiting.php");
	
	//LOAD PAIRS TABLE FUNCTIONS
	require("pairs.php");
	
	//LOAD TEXT INPUT OUTPUT FUNCTIONS
	require("textio.php");
	
	//LOAD TWILIO API
	require("twilio.php");

	require("connect4_game.php");
	require("tictactoe_game.php");
	
	//TODO DICE ROLL?!
	/*
	require("diceroll.php"); 
	*/
	
	//GET PHONE ID
	$phone_id = get_phone_id(@$_REQUEST['From']);
	
	//GET TEXT BODY
	$body = trim($_REQUEST['Body']);

//---------PARSE TEXT BODY FOR COMMANDS--------
	
	//START NEW CONVERSATION
	if(string_equals($body, 'startchat') || string_equals($body, 'startnewchat'))
	{
		//IF IS A PAIR, END THE OLD ONE FIRST
		if(is_pair($phone_id)) {
			//REMOVE EXISTING PAIR
			send_message_to_partner($phone_id, "Your partner has ended the conversation. To initiate a new conversation, say \"start chat.\"");
			remove_pair($phone_id);
		}
		//FIND NEW PAIR
		//IF NONE WAITING, ADD TO DATABASE AND END
		if(num_waiting(0) == 0) {
			add_waiting($phone_id, $body, 0);
		}
		//IF POSSIBLE PAIR, REMOVE WAITINGS and MAKE PAIR
		else {
			//Remove From Waiting
			$selected_waiter = select_and_remove_waiting(0);
			//Add Pair
			add_pair($phone_id, $selected_waiter["phone_id"], 0, '');
			//Notify partners
			$notification_text = "You have been paired up. Chat away!";
			send_message($phone_id, $notification_text);
			send_message($selected_waiter["phone_id"], $notification_text);
		}
	}
	
	//END CONVERSATION
	else if(string_equals($body, 'endchat') && is_pair($phone_id))
	{
		send_message_to_partner($phone_id, "Your partner has ended the conversation. To initiate a new conversation, say \"start chat\".");
		send_message($phone_id, "Your conversation has been ended. To initiate a new conversation, say \"start chat\".");
		remove_pair($phone_id);
	}

	else if(string_equals($body, 'endgame') && is_pair($phone_id))
	{
		//IF SO CHECK IF OTHER PLAYER READY
		$pairdata = get_pair($phone_id);
		modify_pair($phone_id, 0, '');

		if($pairdata['type'] == 1 || $pairdata['type'] == 2)
		{
	
			send_message($phone_id, "Your game has ended but you can continue chatting with your partner. Text \"end chat\" to stop chatting.");
			send_message_to_partner($phone_id, "Your partner has ended the game but you can continue chatting. Text \"end chat\" to stop chatting.");
		}
	}
	
	//TODO UPDATE THE TEXT - HELP COMMAND
	else if(string_equals($body, 'help'))
	{
		$help_text = "Available commands:\n".
					  "start chat\n" .
					  "end chat\n".
					  "play tictactoe\n".
					  "play connect4 \n".
					  "let's talk\n".
					  "help\n".
		send_message($phone_id, $help_text);
	}

	else if(string_equals($body, 'play'))
	{
		$available_games = "Available games are:\n".
							"Connect Four (\"play connect4\")\n".
							"Tic Tac Toe (\"play tictactoe\")";
		send_message($phone_id, $available_games);
	}
	
	//VOICE CHAT COMMAND
	else if(string_equals($body, 'letstalk')) 
	{
		//CHECK IF PAIRED UP
		if(is_pair($phone_id)) {
			//IF SO CHECK IF OTHER PLAYER READY
			$pairdata = get_pair($phone_id);
			
			//IF BOTH READY, SEND INSTRUCTIONS TODO MOVE THIS TO ACCEPT OFFER
			if($pairdata['type'] == 3 && $pairdata['data'] == 'init.voice') {
				$phone_id_2;
				if($phone_id == $pairdata['phone_id_1']) {
					$phone_id_2 = $pairdata['phone_id_2'];
				}
				else {
					$phone_id_2 = $pairdata['phone_id_1'];
				}

				$data = 'Conference'.$phone_id.'To'.$phone_id_2;
				modify_pair($phone_id, 3, $data);

				send_message($phone_id, "Please call $TwilioNumber to connect with your chat buddy.");
				send_message($phone_id_2, "Please call $TwilioNumber to connect with your chat buddy.");
			}
			//ELSE SET READY AND MESSAGE OTHER PLAYER
			else
			{
				modify_pair($phone_id, 3, 'init.voice');
				
				$phone_id_2;
				if($phone_id == $pairdata['phone_id_1']) {
					$phone_id_2 = $pairdata['phone_id_2'];
				}
				else {
					$phone_id_2 = $pairdata['phone_id_1'];
				}
				send_message($phone_id_2, "Your partner wishes to start a voice conference. Text \"Accept Offer\" to accept.");
			}
		}
		//ELSE NOT PAIRED UP
		else {
			send_message($phone_id, 'Please start a conversation before attempting a voice call. To initiate a new conversation, say \"Start new conversation.\"');
		}
	}
	/* TODO DECIDE WHAT .d. SHOULD BECOME - Screw this?. As long as Sameen ain't upset.
	else if(starts_with($body, '.d.'))
	{
		$retval = diceroll($body);
		send_message($phone_id, $retval);
	}	
	*/
	
	//PLAY CONNECT FOUR
	else if(string_equals($body, 'playconnectfour') || string_equals($body, 'playcf') || string_equals($body, 'playc4') || string_equals($body, 'play connect4')) 
	{
		//Check if paired up
		if(is_pair($phone_id)) {
			//IF SO CHECK IF OTHER PLAYER READY
			$pairdata = get_pair($phone_id);
			
			//IF BOTH PLAYERS READY
			if($pairdata['type'] == 2 && $pairdata['data'] == 'init.connect4') {
				connect4_game($phone_id, NULL);
				exit("Exited at line ".__LINE__);
			}
			//ELSE SET READY AND MESSAGE OTHER PLAYER
			else
			{
				modify_pair($phone_id, 2, 'init.connect4');
				$phone_id_2;
				if($phone_id == $pairdata['phone_id_1']) {
					$phone_id_2 = $pairdata['phone_id_2'];
				}
				else {
					$phone_id_2 = $pairdata['phone_id_1'];
				}
				send_message($phone_id_2, 'Your partner wishes to start a connect4 game. Text \'accept offer\' to accept.');
			}
		}
		//ELSE IF NOT PAIRED UP, CHECK IF SOMEONE IS WAITING
		else if(num_waiting(2) > 0) {
			//SOMEONE IS WAITING, INIT GAME
			$selected_waiter = select_and_remove_waiting(2);

			//Add Pair
			add_pair($phone_id, $selected_waiter["phone_id"], 2, '');

			connect4_game($phone_id, NULL);
			exit("Exited at line ".__LINE__);
		}
		//IF NO ONE WAITING, ADD TO WAIT LIST
		else {
			add_waiting($phone_id, mysql_real_escape_string(serialize(connect4_initialize())), 2);
		}
	}

	//PLAY TIC TAC TOE
	else if(string_equals($body, 'playtictactoe') || string_equals($body, 'playttt')) 
	{
		//Check if paired up
		if(is_pair($phone_id)) {
			//IF SO CHECK IF OTHER PLAYER READY
			$pairdata = get_pair($phone_id);
			if($pairdata['type'] == 1 && $pairdata['data'] == 'init.tictactoe') {
				tic_tac_toe_game($phone_id, NULL);
				exit("Exited at line ".__LINE__);
			}
			//ELSE SET READY AND MESSAGE OTHER PLAYER
			else
			{
				modify_pair($phone_id, 1, 'init.tictactoe');
				$otherplayer;
				if($phone_id == $pairdata['phone_id_1']) {
					$otherplayer = $pairdata['phone_id_2'];
				}
				else {
					$otherplayer = $pairdata['phone_id_1'];
				}
				send_message($otherplayer, 'Your partner wishes to start a tic tac toe game. Text \'accept offer\' to accept.');
			}
		}
		//ELSE IF NOT PAIRED UP, CHECK IF SOMEONE IS WAITING
		else if(num_waiting(1) > 0) {
			//SOMEONE IS WAITING, INIT GAME
			
			$selected_waiter = select_and_remove_waiting(1);

			//Add Pair
			add_pair($phone_id, $selected_waiter["phone_id"], 1, '');

			tic_tac_toe_game($phone_id, NULL);

			exit("Exited at line ". __LINE__);
		}
		//IF NO ONE WAITING, ADD TO WAIT LIST
		else {
			add_waiting($phone_id, '', 1);
		}
	}

	else if(string_equals($body, 'acceptoffer'))
	{
		if(is_pair($phone_id)) {
			$pairdata = get_pair($phone_id);

			//Initialize the game. Pair has just been made.
			switch($pairdata['data'])
			{
				case 'init.tictactoe':
					tic_tac_toe_game($phone_id, NULL);
					break;
				case 'init.connect4':
					connect4_game($phone_id, NULL);
					break;
				//TODO Should we move this elsewhere to keep it consistent with everything else?
				case 'init.voice':
					$phone_id_2;
					if($phone_id == $pairdata['phone_id_1']) {
						$phone_id_2 = $pairdata['phone_id_2'];
					}
					else {
						$phone_id_2 = $pairdata['phone_id_1'];
					}

					$data = 'Conference'.$phone_id.'To'.$phone_id_2;
					modify_pair($phone_id, 3, $data);

					send_message($phone_id, "Please call $TwilioNumber to connect with your chat buddy.");
					send_message($phone_id_2, "Please call $TwilioNumber to connect with your chat buddy.");
					break;
				default:
					$db_data = unserialize($pairdata['data']);
					if(is_array($db_data)) //User has already been paired up and is playing a game.
					{
						send_message($phone_id, "You have already accepted the offer. The game has begun.");
					}
					else //It's probably a regular message
					{
						send_message_to_partner($phone_id, $body);	
					}

			}
			exit('Exited at line '.__LINE__); //Don't want no cascading into other code.
		}
		else //User isn't paired up yet.
		{
			send_message($phone_id, 'You have not have a partner. Please enter a play command or say \"Start Conversation\" to be paired up.');
			exit;
		}
		
	}
	
	//REGULAR CHAT MESSAGE
	else
	{
		//IF PAIR, FORWARD MESSAGE
		if(is_pair($phone_id)) {
			$pairdata = get_pair($phone_id);
			switch($pairdata['type'])
			{
			case 1:
				tic_tac_toe_game($phone_id, $body);
				exit('Exited at line '.__LINE__); //Don't want no cascading into other code.
			case 2:
				connect4_game($phone_id, $body);
				exit('Exited at line '.__LINE__); //Don't want no cascading into other code.
			default:
				send_message_to_partner($phone_id, $body);	
			}
		}
		//ELSE TELL THEM TO START A CONVERSATION
		else {
			send_message($phone_id, 'Please start a conversation before chatting. To initiate a new conversation, say "start chat".');
		}
	}
