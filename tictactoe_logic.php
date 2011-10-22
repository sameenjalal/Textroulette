<?

/*
$board = tic_tac_toe_initialize();
while(true)
{
	print_board($board);
	echo 'Make a move: ';
	$move = trim(fgets(STDIN));
	if(!is_numeric($move) || $move > 9 || $move < 1)
		continue;

	$temp_board = tic_tac_toe_move($board, $move);
	if(!$temp_board) 
	{
		echo "Invalid move\n";
		continue;
	}
	$board = $temp_board;
	$winner = tic_tac_toe_win($board);
	if($winner)
	{
		if($winner == '-')
			echo "Tied";
		else
			echo "$winner won";
		echo "\n";
		print_board($board);
		break;
	}
}
 */

function tic_tac_toe_initialize()
{
	$board = array();
	for($i = 0; $i <3; $i++)
	{
		$board[$i] = array();
		for($j = 0; $j < 3; $j++)
			$board[$i][$j] = '-';
	}
	return $board;
}

function tic_tac_toe_print_board($board)
{
	$rv = '';
	for($i=0; $i<3; $i++)
	{
		for($j=0; $j<3; $j++)
		{
			$rv .= $board[$i][$j].' ';
		}
		$rv .= "\n";
	}

	return $rv;
}

function tic_tac_toe_move($board, $number)
{
	$sum_x=0;
	$sum_y=0;
	for($i = 0; $i <3; $i++)
	{
		for($j = 0; $j < 3; $j++)
		{
			if($board[$i][$j] == 'X')
				$sum_x++;
			else if($board[$i][$j] == 'O')
				$sum_y++;
		}
	}
//	echo "Number: $number Sum_y: $sum_y Sum_x $sum_x \n";
	switch($number)
	{
		case 1:
			$i=0;
			$j=0;
			break;
		case 2:
			$i=0;
			$j=1;
			break;
		case 3:
			$i=0;
			$j=2;
			break;
		case 4:
			$i=1;
			$j=0;
			break;
		case 5:
			$i=1;
			$j=1;
			break;
		case 6:
			$i=1;
			$j=2;
			break;
		case 7:
			$i=2;
			$j=0;
			break;
		case 8:
			$i=2;
			$j=1;
			break;
		case 9:
			$i=2;
			$j=2;
			break;
		default:
			return false;
	}
	if($board[$i][$j] == '-')
	{
		if($sum_x <= $sum_y)
			$board[$i][$j] = 'X';
		else
			$board[$i][$j] = 'O';
		return $board;

	}
	else 
		return false;
}
	
function tic_tac_toe_check_win($board)
{
//	print_r($board); exit;
	$row = array($board[0][0], $board[0][1], $board[0][2]);
//	print_r($row);
	if($row[0] != '-' && array_equal($row))
	{
		return $board[0][0];
	}
	$row = array($board[1][0], $board[1][1], $board[1][2]);
	if($row[1][0] != '-' && array_equal($row))
	{
		return $board[1][0];
	}
	$row = array($board[2][0], $board[2][1], $board[2][2]);
	if($row[2][0] != '-' && array_equal($row))
	{
		return $board[2][0];
	}

	$col = array($board[0][0], $board[1][0], $board[2][0]);
	if($col[0] != '-' && array_equal($col))
	{
		return $board[0][0];
	}
	$col = array($board[0][1], $board[1][1], $board[2][1]);
	if($col[0] != '-' && array_equal($col))
	{
		return $board[0][1];
	}
	$col = array($board[0][2], $board[1][2], $board[2][2]);
	if($col[0] != '-' && array_equal($col))
	{
		return $board[0][2];
	}

	$diag = array($board[0][0], $board[1][1], $board[2][2]);
	if($diag[0] != '-' && array_equal($diag))
	{
		return $board[0][0];
	}

	$diag = array($board[2][0], $board[1][1], $board[0][2]);
	if($diag[0] != '-' && array_equal($diag))
	{
		return $board[2][0];
	}

	$sum_things=0;
	for($i = 0; $i <3; $i++)
	{
		for($j = 0; $j < 3; $j++)
		{
			if($board[$i][$j] != '-')
				$sum_things++;
		}
	}
	if($sum_things == 9)
		return '-';

	return false;
}

function array_equal($arr)
{
	return count(array_unique(array_values($arr))) == 1;
}

//Returns 1 or 2, indicating which player should go next.
function tic_tac_toe_get_next_player($board)
{

	$sum_x=0;
	$sum_y=0;
	for($i = 0; $i <3; $i++)
	{
		for($j = 0; $j < 3; $j++)
		{
			if($board[$i][$j] == 'X')
				$sum_x++;
			else if($board[$i][$j] == 'O')
				$sum_y++;
		}
	}
	return $sum_x > $sum_y ? 2 : 1;
}

?>
