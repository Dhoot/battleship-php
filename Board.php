
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
    if ($endpoint === 'A'):
      $promptString = 'Select starting point for your ' . $ship_name . ': ';
    elseif ($endpoint === 'B'):
      $promptString = 'Select ending point for your ' . $ship_name . ': ';
    else:
      return false;
    endif;

    // user will input choices
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
    if ($this->isLegalSquare($posX, $posY) && !$this->gameBoard[$posX][$posY]->getUnderShip())
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

  // verify range to qualify legal coordinates
  private function isLegalSquare($posX, $posY) {
    if (!($posX >= 0 && $posX < 10))
      return false;

    if (!($posY >= 0 && $posY < 10))
      return false;

    // this is indeed a legal square on the board
    return true;
  }

  // check that endpoints form a segment that is legally represents a ship
  private function validateEndpointSegment($pointA, $pointB, $ship) {
    if ($pointA === $pointB)
      return false;

    // ensure endpoints are on the same row or column
    if ($pointA[0] !== $pointB[0] && $pointA[1] !== $pointB[1])
      return false;

    // endpoints must form a straight line segment equal in length to the ship
    if ($pointA[0] === $pointB[0]):
      $orientation = 'row';
      $max = max($pointA[1], $pointB[1]);
      $min = min($pointA[1], $pointB[1]);
    else:
      $orientation = 'column';
      $max = max($pointA[0], $pointB[0]);
      $min = min($pointA[0], $pointB[0]);
    endif;

    // length verification
    if ($max - $min + 1 != $ship->getSize()) {
      echo 'The ship won\'t fit here. ';
      return false;
    }

    // get squares between endpoints
    $squares = array();
    $row = $pointA[0];
    $column = $pointA[1];

    // get array of target squares
    while ($min <= $max) {

      // the ship is being placed along 1 row and multiple columns
      if ($orientation === 'row') {
        if ($this->gameBoard[$row][$min]->getUnderShip() === true)
          return false;
        else
          $squares[] = $this->gameBoard[$row][$min];

      // the ship is being placed along 1 column and multiple rows
      } else {
        if ($this->gameBoard[$min][$column]->getUnderShip() === true)
          return false;
        else
          $squares[] = $this->gameBoard[$min][$column];
      }

      // prepare for next square in the row or column
      $min = $min + 1;
    }

    // finally, if no squares are already occupied, place the ship
    foreach ($squares as $square):
      $square->setUnderShip();
      $square->setShipName($ship->getName());
    endforeach;

    // show the player the board configuration after each piece is placed
    $this->displayBoard();
  }

  // receives and processes an attack on this board
  public function receiveAttack($choice) {
    $choice = $this->processChoiceString($choice);
    if ($choice === false)
      return false;

    // convert coordinates to int values
    $posX = array_search($choice[0], $this->xAxisLabel);
    $posY = intval($choice[1]);
    $posY = $posY - 1;

    // report attacks that have missed all opponent ships
    $square = $this->gameBoard[$posX][$posY];
    if ($this->isLegalSquare($posX, $posY) && !$square->getUnderShip()):
      $square->setWasAttacked();
      return $this->xAxisLabel[$posX] . ($posY + 1) . ': ' . 'Miss.';

    // process the ship that was hit and report the incident
    elseif ($this->isLegalSquare($posX, $posY) && $square->getUnderShip()):
      $square->setWasAttacked();

      // process the hit
      $name = $square->getShipName();
      $ship = $this->getShipByName($name);
      $ship->receiveHit();

      // report the hit
      $report_string = $this->xAxisLabel[$posX] . ($posY + 1) . ': ' . 'Hit.';
      if ($ship->getRemainingHits() === 0)
        $report_string .= "\n" . 'You sank the ' . $ship->getName() . '.';
      return $report_string;

    // return false if user input invalid coordinates
    else:
      return false;
    endif;
  }

  // accessor function for the array of squares (i.e. the board)
  public function getBoard() {
    return $this->gameBoard;
  }

  // accessor method to return ships array
  public function getShips() {
    return $this->ships;
  }

  // access method for single ship
  public function getShipByName($name) {
    foreach($this->ships as $ship):
      if ($ship->getName() === $name)
        return $ship;
    endforeach;

    return false;
  }

  // display board state in the CLI
  public function displayBoard() {
    echo '     ---------------------' . "\n";
    echo '    | 1 2 3 4 5 6 7 8 9 10|' . "\n";
    echo '     ---------------------' . "\n";

    // print the x-axis labels and the state of each square
    for ($i = 0; $i < 10; $i++) {
      echo '| ' . $this->xAxisLabel[$i] . ' | ';

      for ($j = 0; $j < 10; $j++)
        echo $this->gameBoard[$i][$j]->displaySquare() . ' ';

      echo '|' . "\n";
    }

    echo '     ---------------------' . "\n";
  }

}
