<?php

require_once 'Ship.php';
require_once 'Square.php';
require_once 'Board.php';

// create the boards for both players
$board = new Board();
$board2 = clone $board;

// players now position their ships on each board


$board->displayBoard();
$board->placeShips();

// the game loop begins and players attack each other's squares
//$gameOver = false;
//while(!gameOver) {

  //$gameOver = isGameOver();
//}

//function isGameOver() {

//}
