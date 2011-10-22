<?

define("C4_WIDTH",7);
define("C4_HEIGHT",6);
define("C4_SPACE",'_');
define("C4_WIN_VAL", 4);

define('C4_PLAYER_1', 'X');
define('C4_PLAYER_2', 'O');

function connect4_initialize()
{
	$board = array();
	for($i = 0; $i < C4_HEIGHT ; $i++)
	{
		$board[$i] = array();
		for($j = 0; $j < C4_WIDTH ; $j++)
			$board[$i][$j] = C4_SPACE;
	}
	return $board;
}

//$board = connect4_initialize();
//echo connect4_print_board($board);

function connect4_print_board(&$board)
{
	//Throw the numbers of the columns on top of the board.
	$rv = '';
	for($i = 0; $i < C4_WIDTH; $i++)
		$rv.=($i+1).' ';

	$rv .= "\n";
	
	for($i = 0; $i < C4_HEIGHT ; $i++)
	{
		for($j = 0; $j < C4_WIDTH ; $j++)
			$rv .= $board[$i][$j].' ';
		$rv .= "\n";
	}
	return $rv;
}

function column_sequence_win(&$board)
{
	$sq_count = 1;
	for($i = 0 ; $i < C4_WIDTH ; $i++)
	{
		for($j = 0 ; $j < C4_HEIGHT - 1 ; $j++)
		{
			$player = $board[$j][$i];
			$player2 = $board[$j+1][$i];
			if($player == C4_SPACE)
				continue;
			if($player == $player2)
				$sq_count++;
			else
				$sq_count = 1;

			if($sq_count == C4_WIN_VAL)
				return $player2;
		}
		$sq_count = 1;
	}
	return false;
}

function row_sequence_win(&$board)
{
	$sq_count = 1;
	for($i = 0 ; $i < C4_HEIGHT ; $i++)
	{
		for($j = 0 ; $j < C4_WIDTH - 1 ; $j++)
		{
			$player = $board[$i][$j];
			$player2 = $board[$i][$j+1];
			if($player == C4_SPACE)
				continue;
			if($player == $player2)
			{
				$sq_count++;
//				echo "Checking: ".$board[$i][$j]."Row = $j and Column $i sq_count = $sq_count\n";
			}
			else
				$sq_count = 1; if($sq_count == C4_WIN_VAL) return $player2;
		}
		$sq_count = 1;
	}
	return false;
}

function diagonal_sequence_win(&$board)
{
	for($i = 0 ; $i < C4_WIDTH ; $i++)
	{
		for($j = 0 ; $j < C4_HEIGHT; $j++)
		{
			if($board[$j][$i] == C4_SPACE)
				continue;
			if($i + C4_WIN_VAL <= C4_WIDTH)
			{
				if($j - C4_WIN_VAL + 1 >= 0) //up right diagonal from i, j
				{
					$x = $i;
					$y = $j;
					$sq_count = 1;
					for($k = 1 ; $k < C4_WIN_VAL ; $k++)
					{
//						echo "loop running: x = $x y = $y i = $i j = $j\n";
						$player = $board[$y][$x];
						if($player == C4_SPACE)
							continue;
						$player2 = $board[$y-1][$x+1];
						if($player == $player2)
							$sq_count++;
						else
							$sq_count = 1;

						if($sq_count == C4_WIN_VAL)
							return $player2;

						$x++;
						$y--;
					}
				}
				if($j + C4_WIN_VAL <= C4_HEIGHT) //down right diagonal from i, j
				{
					$x = $i;
					$y = $j;
					$sq_count = 1;
					for($k = 1 ; $k < C4_WIN_VAL ; $k++)
					{
//						echo "2: loop running: x = $x y = $y i = $i j = $j\n";
						$player = $board[$y][$x];
						if($player == C4_SPACE)
							continue;
						$player2 = $board[$y+1][$x+1];
						if($player == $player2)
							$sq_count++;
						else
							$sq_count = 1;

						if($sq_count == C4_WIN_VAL)
							return $player2;

						$x++;
						$y++;
					}
				}
			}
		}
	}
}

// Return error string if move is invalid and true if error free
function connect4_check_invalid(&$board, $column)
{	
	if($column > C4_WIDTH || $column < 0)
		return "Please enter a valid column.\n";

	if($board[0][$column] != C4_SPACE)
		return "Column Full\n";
	
}

function connect4_move(&$board, $column, $player)
{
	for($i = C4_HEIGHT - 1 ; $i >= 0 ; $i--)
	{
		if($board[$i][$column] == C4_SPACE)
		{
			$board[$i][$column] = $player;
			return $board;
		}
	}
	return $board;
}

/** This function returns 1 if C4_PLAYER_1 is the next person that should move, and 2 if C4_PLAYER2 is the next person that should move. */
function connect4_get_next_player(&$board)
{
	$count1 = 0;
	$count2 = 0;

	for($i=0; $i < C4_HEIGHT; $i++)
	{
		for($j=0; $j < C4_WIDTH; $j++)
		{
			if($board[$i][$j] == C4_PLAYER_1)
				$count1++;
			if($board[$i][$j] == C4_PLAYER_2)
				$count2++;
		}
	}

/** If more Xs on board than Os, it's O's turn. Else X's turn */

	return ($count1 > $count2) ? 2 : 1;


}

function connect4_board_full(&$board)
{
	for($i=0; $i < C4_HEIGHT; $i++)
	{
		for($j=0; $j < C4_WIDTH; $j++)
		{
			if($board[$i][$j] == C4_SPACE)
				return false;
		}
	}
	return true;
}

/** This function returns false if no one's won the game, and returns 1 or 2 if C4_PLAYER_1 / C4_PLAYER_2 have won the game. Returns '-' if tied. */
function connect4_check_win(&$board)
{
	$player_win = column_sequence_win($board);
	if($player_win) return $player_win;

	$player_win = row_sequence_win($board);
	if($player_win) return $player_win;
	
	$player_win = diagonal_sequence_win($board);
	if($player_win) return $player_win;

	if(connect4_board_full($board))
		return '-';

	return false;
}
/*
$count = 0;
$player = 'x';
$player2 = 'o';
$player3 = "";
while(true)
{
	if($count++ % 2 == 0)
		$player3 = $player;
	else
		$player3 = $player2;
	$move = trim(fgets(STDIN));
	//echo "Move = $move";

	connect4_check_invalid($board, $move-1);
	connect4_move($board,$move - 1, $player3);
	connect4_print_board($board);
	echo connect4_print_board($board);
	$player_win = connect4_check_win($board);
	if($player_win)
	{
		echo "Player $player_win won\n";
		break;
	}
}*/
