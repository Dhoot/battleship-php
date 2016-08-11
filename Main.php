<?php

require_once 'Ship.php';
require_once 'Square.php';
require_once 'Board.php';

// account for Windows machines
define('OS_TYPE', strtoupper(substr(PHP_OS, 0, 3)));

// clear terminal so opponent cannot see the user's board configuration
function clear_terminal() {
  if (OS_TYPE === 'WIN'):
    system('cls');
  else:
    system('clear');
  endif;
}

// allow user to view her own board before attacking
function peek($board) {
   $board->displayBoard();
   readline('Press Enter to hide board:');
   clear_terminal();
}

// check for game terminating conditions
function is_game_over($board, $board2) {
  // check if all of player 1's ships were sunk
  $player_one_loss = true;
  foreach($board->getShips() as $ship) {
    if ($ship->getIsSunk() !== true) {
      $player_one_loss = false;
      break;
    }
  }

  // check if all of player 2's ships were sunk
  $player_two_loss = true;
  foreach($board2->getShips() as $ship) {
    if ($ship->getIsSunk() !== true) {
      $player_two_loss = false;
      break;
    }
  }

  // determine if game is over and announce results accordingly
  if ($player_one_loss && $player_two_loss):
    echo 'It\'s a tie!';
    return;
  elseif ($player_two_loss):
    echo 'Player 1 wins!';
    return;
  elseif ($player_one_loss):
    echo 'Player 2 wins!';
    return;
  else:
    return false;
  endif;
}

// attack a square on the opponent's board
function launch_attack($player_number, $player_board, $opponent_board) {
  $valid_attack = false;
  do {
    // allow player to input attack coordinates or check own board configuration
    echo 'Player ' . $player_number . ', type \'peek\' to view your own board' . "\n";
    $choice = readline('or type which square of your opponent\'s to attack: ');

    // player will either peek at her own board or attempt an attack
    if ($choice === 'peek' || $choice === 'Peek'):
      peek($player_board);
      continue;
    else:
      $valid_attack = $opponent_board->receiveAttack($choice);
    endif;

    // ensure player input a legal attack
    if ($valid_attack === false)
      echo 'That is not a valid attack.' . "\n";
  } while ($valid_attack === false);

  return $valid_attack;
}

// print to the console the event that transpired.
function report_attack($result) {
  clear_terminal();
  echo $result . "\n";
}

/* ----- begin battleship program ----- */
clear_terminal();

// create boards for both players
$board = new Board();
$board2 = new Board();

// player 1 must position her ships on her board
$board->displayBoard();
readline('Player 1 prepare to place your ships. Player 2 don\'t peek. Press Enter to continue:');
$board->placeShips();
clear_terminal();

// player 2 must position her ships on her board
$board2->displayBoard();
readline('Player 2 prepare to place your ships. Player 1 don\'t peek. Press Enter to continue:');
$board2->placeShips();
clear_terminal();

// the game loop begins
$game_over = false;
while($game_over === false) {
  // player 1 launches an attack and result is reported
  $result = launch_attack(1, $board2, $board);
  report_attack($result);

  // player 2 launches an attack and result is reported
  $result = launch_attack(2, $board, $board2);
  report_attack($result);

  // check if game has ended
  $game_over = is_game_over($board, $board2);
}
/* ----- End battleship program ----- */
