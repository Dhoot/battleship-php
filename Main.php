<?php

require_once 'Ship.php';
require_once 'Square.php';
require_once 'Board.php';

// allow user to view her own board before attacking
function peek($board) {
   $board->displayBoard();
   readline('Press Enter to hide board:');
   Board::clear_terminal();
}

// check for game terminating conditions
function is_game_over($board, $board2) {
  // check if all of player 1's ships were sunk
  $player_one_loss = true;
  foreach($board->getShips() as $ship) {
    if ($ship->getIsSunk() !== true):
      $player_one_loss = false;
      break;
    endif;
  }

  // check if all of player 2's ships were sunk
  $player_two_loss = true;
  foreach($board2->getShips() as $ship) {
    if ($ship->getIsSunk() !== true):
      $player_two_loss = false;
      break;
    endif;
  }

  // determine if game is over, if so, announce it
  if ($player_one_loss || $player_two_loss):
    Board::clear_terminal();
    echo 'Game Over!' . "\n\n";
    echo '        Player 1 Board' . "\n";
    $board->displayBoard();
    echo "\n";
    echo '        Player 2 Board' . "\n";
    $board2->displayBoard();
    echo 'Result: ';
  endif;

  // announce game outcome
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

/* ----- begin battleship program ----- */
Board::clear_terminal();

// create boards for both players
$board = new Board();
$board2 = new Board();

// player 1 must position her ships on her board
$board->displayBoard();
readline('Player 1 prepare to place your ships. Player 2 don\'t peek. Press Enter to continue:');
$board->placeShips();
Board::clear_terminal();

// player 2 must position her ships on her board
$board2->displayBoard();
readline('Player 2 prepare to place your ships. Player 1 don\'t peek. Press Enter to continue:');
$board2->placeShips();
Board::clear_terminal();

// the game loop
$game_over = false;
$p1Report = '';
$p2Report = '';
while($game_over === false) {
  // Result of previous attack is reported, and player 1 launches another attack
  Board::clear_terminal();
  if ($p1Report)
    echo $p1Report . "\n";
  $board2->attackVisualizer();
  $p1Report = launch_attack(1, $board, $board2);

  // Result of previous attack is reported, and player 2 launches another attack
  Board::clear_terminal();
  if ($p2Report)
    echo $p2Report . "\n";
  $board->attackVisualizer();
  $p2Report = launch_attack(2, $board2, $board);

  // check if game has ended
  $game_over = is_game_over($board, $board2);
}
/* ----- end battleship program ----- */
