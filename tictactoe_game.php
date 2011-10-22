<?php
require('tictactoe_logic.php');

function tic_tac_toe_game($player_id, $move) {
	echo 'tictactoe_game'.$player_id.$move;
	
	$pairdata = get_pair($player_id);

	//This pair was just created. Initialize board and stuff.
	if(!$move)
	{
		$empty_board = tic_tac_toe_initialize();
		$first_mover = rand(1, 2);

		//Database data will contain the board as well as who moved first.
		$db_data = array(
			'first' => $first_mover,
			'board' => $empty_board,
		);

		modify_pair($player_id, 1, serialize($db_data));

		send_message($pairdata['phone_id_'.$first_mover], tic_tac_toe_print_board($empty_board). 'Pick a square 1-9 (You are X): ');
		return;
	}
		
	$db_data = unserialize($pairdata['data']);

	$first_mover = $db_data['first'];
	$gametable = $db_data['board'];

	//See Whose turn it is
	$moving_player;
	$player = tic_tac_toe_get_next_player($gametable);
	if($player == $first_mover)
		$moving_player = $pairdata["phone_id_1"];
	else
		$moving_player = $pairdata["phone_id_2"];

	echo "Player: $player first_mover $first_mover Moving_player $moving_player\n<br />";

	echo '<pre>';
	print_r($db_data);
	echo '</pre>';

	//If it is not the moving player's turn.
	if($player_id != $moving_player)
	{
		send_message($player_id, 'Wait for your turn...');
		return;
	}

	if($player == 1)
		$playerXO = 'X';
	else
		$playerXO = 'O';

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


	//Make the move and get resulting board.
	$resultingBoard = tic_tac_toe_move($gametable, $actual_move, $playerXO);
	if(!$resultingBoard) //Invalid Move.
	{
		send_message($player_id, 'Invalid move. Please try again.');
		return;
	}

	$printedtable = tic_tac_toe_print_board($resultingBoard);

	echo 'RESULTING BOARD:'.$printedtable. "\n<br />";

	$win = tic_tac_toe_check_win($resultingBoard);
	
	//If not win
	if(!$win) 
	{
		//build the array to store into the database.
		$db_data = array(
			'first' => $first_mover,
			'board' => $resultingBoard,
		);

		modify_pair($player_id, 1, serialize($db_data));
		$opponentXO = $playerXO == 'X' ? 'O' : 'X';
		send_message_to_partner($player_id, $printedtable.'Your move. Pick a square 1-9. (You are '.$opponentXO.")\n$smack_talk");
	}
	else
	{
		if($win == '-')
		{
			send_message($player_id, $printedtable.'The game was a tie. Text "play tictactoe" to play again.'."\n$smack_talk");
			send_message_to_partner($player_id, $printedtable.'The game was a tie. Text "play tictactoe" to play again.'."\n$smack_talk");
		}
		else
		{
			send_message($player_id, $printedtable.'You Won the Game. Text "play tictactoe" to play again.'."\n$smack_talk");
			send_message_to_partner($player_id, $printedtable.'You Lost the Game. Text "play tictactoe" to play again.'."\n$smack_talk");
		}
		modify_pair($player_id, 0, '');
	}
}

?>
