
<?php

Class Board
{
  // the board object contains all the squares that ships may occupy
  private $gameBoard;

  // the array of ships to be placed on the board
  private $ships;

  // the labels identifying horizontal rows on the game board (the vertical columns will be numbered)
  private $xAxisLabel = array("A","B","C","D","E","F","G","H","I","J");

  // the board constructor
  function __construct()
  {
    $this->gameBoard = array();
    $this->initSquares();
    $this->initShips();
  }

  // instantiate all squares on the game board
  private function initSquares() {
    for ($i = 0; $i < 10; $i++)
      for ($j = 0; $j < 10; $j++)
        $this->gameBoard[$i][$j] = new Square($i,$j);
  }

  // instantiate all the ships for the board
  private function initShips() {
    $this->ships[] = new Ship("Carrier", 5);
    $this->ships[] = new Ship("Battleship", 4);
    $this->ships[] = new Ship("Submarine", 3);
    $this->ships[] = new Ship("Destroyer", 3);
    $this->ships[] = new Ship("Patrol Boat", 2);
  }

  // set ship position via user input in CLI
  public function placeShips() {

    foreach ($this->ships as $ship) {

      // ensure the ship is being placed legally
      $isLegalRange = false;
      while ($isLegalRange === false) {

        // inform the user of which ship she is placing
        echo "\n" . 'Place your ' . $ship->getName() . ' (size = ' . $ship->getSize() . ')' . "\n";

        // set endpoint A and ensure it is a legal board position
        $pointA = false;
        while($pointA === false)
          $pointA = $this->setEndpoint($ship->getName(), 'A');

        // set endpoint B and ensure it is a legal board position
        $pointB = false;
        while ($pointB === false)
          $pointB = $this->setEndpoint($ship->getName(), 'B');

        // ensure endpoints span a range that legally describes the current ship
        $isLegalRange = $this->validateEndpointSegment($pointA, $pointB, $ship);
        if ($isLegalRange === false)
          echo 'You may not place this ship on the board this way. Please try again.' . "\n";
      }
    }
  }

  // select first endpoint for ship positioning
  private function setEndpoint($ship_name, $endpoint) {

    // user will input endpoint choice
    if ($endpoint === 'A') {
      $promptString = 'Select starting point for your ' . $ship_name . ': ';
    } elseif ($endpoint === 'B') {
      $promptString = 'Select ending point for your ' . $ship_name . ': ';
    } else {
      return false;
    }
    $choice = readline($promptString);

    // weed out poor input strings and prepare string for conversion to int
    $choice = $this->processChoiceString($choice);

    if ($choice === false)
      return false;

    // convert coordinates to int values
    $posX = array_search($choice[0], $this->xAxisLabel);
    $posY = intval($choice[1]);
    $posY = $posY - 1;

    // validate coordinates
    if ($this->isLegalSquare($posX, $posY))
      return $posX . $posY;

    // deliver an error message as required
    echo 'Illegal Square choice' . "\n";
    return false;
  }

  // prepare choice string for conversion to coordinates
  private function processChoiceString($choice) {

    // remove all whitespace
    $choice = preg_replace('/\s+/', '', $choice);

    // break user input into seperate coordinate strings
    $length = strlen($choice);
    switch ($length) {
        case 2:
            $choice = str_split($choice);
            break;
        case 3: $choice = str_split($choice);
            $choice[1] = $choice[1] . $choice[2];
            break;
        default:
            echo 'Please select a square of the form \'G7\', \'A1\', etc' . "\n";
            return false;
    }

    // allow user to use lowercase characters when specifying coordinates
    $choice[0] = strtoupper($choice[0]);

    return $choice;
  }

  // verify two conditions that qualify legal coordinates
  private function isLegalSquare($posX, $posY) {

    // ensure coordinates are within the legal range
    if (!(is_int($posX) && $posX >= 0 && $posX < 10))
      return false;

    if (!(is_int($posY) && $posY >= 0 && $posY < 10))
      return false;

    // ensure coordinates will not describe a position that is already occupied
    return !$this->gameBoard[$posX][$posY]->getUnderShip();
  }

  // check that endpoints form a segment that is legally represents a ship
  private function validateEndpointSegment($pointA, $pointB, $ship) {

    // ensure the endpoints are not the same point
    if ($pointA === $pointB)
      return false;

    // ensure endpoints are on the same row or column
    if ($pointA[0] !== $pointB[0] && $pointA[1] !== $pointB[1])
      return false;

    // ensure endpoints form a segment that is the same size as the ship
    if ($pointA[0] === $pointB[0]) {
      // endpoints lie on same row; proceed to calculate length of segment
      $orientation = 'row';
      $max = max($pointA[1], $pointB[1]);
      $min = min($pointA[1], $pointB[1]);
    } else {
      // endpoints lie on same column; proceed to calculate length of segment
      $orientation = 'column';
      $max = max($pointA[0], $pointB[0]);
      $min = min($pointA[0], $pointB[0]);
    }

    // length calculation
    if ($max - $min + 1 != $ship->getSize()) {
      echo 'The ship won\'t fit here. ';
      return false;
    }

    // get squares between endpoints
    $squares = array();
    $row = $pointA[0];
    $column = $pointA[1];

    // get squares
    while ($min <= $max) {

      if ($orientation === 'row') {

        if ($this->gameBoard[$row][$min]->getUnderShip() === true)
          return false;
        else
          $squares[] = $this->gameBoard[$row][$min];

      } else { // orientation === 'column'

        if ($this->gameBoard[$min][$column]->getUnderShip() === true) {
          return false;
        } else {
          $squares[] = $this->gameBoard[$min][$column];
        }
      }

      $min = $min + 1;
    }

    // finally, if no squares are occupied, place the ship
    foreach ($squares as $square) {
      $square->setUnderShip();
    }

    // show the player the board configuration after each piece is placed
    $this->displayBoard();
  }

  // accessor function for the array of squares (i.e. the board)
  public function getBoard() {
    return $this->gameBoard;
  }

  // display board state in the CLI
  public function displayBoard() {
    echo '     ---------------------' . "\n";
    echo '    | 1 2 3 4 5 6 7 8 9 10|' . "\n";
    echo '     ---------------------' . "\n";

    // print the x-axis labels and the state of each square
    for ($i = 0; $i < 10; $i++) {
      echo '| ' . $this->xAxisLabel[$i] . ' | ';
      for ($j = 0; $j < 10; $j++) {
        echo $this->gameBoard[$i][$j]->displaySquare() . ' ';
      }
      echo '|' . "\n";
    }

    echo '     ---------------------' . "\n";
  }

}
