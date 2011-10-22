<?php

require_once("connect4_logic.php");

function connect4_game($player_id, $move) {
	echo 'connect4_game'.$player_id.$move;

	$pairdata = get_pair($player_id);
	
	//This pair was just created. Initialize board and stuff.
	if(!$move)
	{
		$empty_board = connect4_initialize();
		$first_mover = rand(1, 2);

		//Database data will contain the board as well as who moved first.
		$db_data = array(
			'first' => $first_mover,
			'board' => $empty_board,
		);

		//Modify this pair to contain the empty board, and set type=2 (connect4 game)
		modify_pair($player_id, 2, serialize($db_data));

		send_message($pairdata['phone_id_'.$first_mover], connect4_print_board($empty_board). 'Pick a column (You are '.C4_PLAYER_1.'): ');
		return;
	}
	
	$db_data = unserialize($pairdata['data']);

	$first_mover = $db_data['first'];
	$gametable = $db_data['board'];

	
	//See Whose turn it is
	$moving_player;
	$player = connect4_get_next_player($gametable);
	if($player == $first_mover)
		$moving_player = $pairdata["phone_id_1"];
	else
		$moving_player = $pairdata["phone_id_2"];

	//If it is not the moving player's turn.
	if($player_id != $moving_player)
	{
		send_message($player_id, 'Wait for your turn...');
		return;
	}
	// Assign whether x or o is moving : or hugs and kisses.
	if($player == 1)
		$playerXO = C4_PLAYER_1;
	else
		$playerXO = C4_PLAYER_2;
		

	
	// Pull out the actual move from the message string.
	$space = strpos($move, ' ');

	if($space) //if space. Extract smack talk.
	{
		$actual_move = substr($move, 0, $space);
		$smack_talk = substr($move, strpos($move, ' ')+1);
	}
	else
	{
		$actual_move = $move;
		$smack_talk = '';
	}

	var_dump($db_data);

	echo '<pre>';
	print_r($gametable);
	echo "Move: $actual_move\n";
	echo '</pre>';

	$board_error = connect4_check_invalid($gametable, $actual_move);
	if($board_error) // move is invalid.
	{
		echo "Board Error Occurred: $board_error\n<br />";
		send_message($player_id, connect4_print_board($gametable).$board_error);
		return;
	}

	//Make the connect4 move and get resulting board.
	$resultingBoard = connect4_move($gametable, $actual_move-1, $playerXO);
	$printedtable = connect4_print_board($resultingBoard);

	echo 'RESULTING BOARD:'.$printedtable. "\n<br />";

	$win = connect4_check_win($resultingBoard);
	
	//IF WIN
	if($win) 
	{
		send_message($player_id, $printedtable.'You Won the Game. Text "play connect4" to play again.'."\n$smack_talk");
		send_message_to_partner($player_id, $printedtable.'You Lost the Game. Text "play connect4" to play again.'."\n$smack_talk");
		
		modify_pair($player_id, 0, '');
	}
	//ELSE NO WIN
	else 
	{
		//build the array to store into the database.
		$db_data = array(
			'first' => $first_mover,
			'board' => $resultingBoard,
		);

		modify_pair($player_id, 2, serialize($db_data));

		$opponentXO = $playerXO == C4_PLAYER_1 ? C4_PLAYER_2 : C4_PLAYER_1;

		send_message_to_partner($player_id, $printedtable.'Pick a column 1-'.C4_WIDTH.' (You are '.$opponentXO.")\n$smack_talk");
	}
}

?>
