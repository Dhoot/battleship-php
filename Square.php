<?php

Class Square
{

  // this boolean property identifies whether a ship is occupying this square
  private $underShip;

  // this boolean property keeps track of whether this square as been attacked by the enemy player
  private $wasAttacked;

  // these integer values denote this square's location on the game board
  private $posX;
  private $posY;

  // the square constructor
  public function __construct($posX, $posY)
  {
    $this->underShip = false;
    $this->wasAttacked = false;
    $this->posX = $posX;
    $this->posY = $posY;
  }

  // display square state in the CLI
  public function displaySquare() {
    if($this->underShip && $this->wasAttacked):
      echo 'X';
    elseif($this->underShip):
      echo 'H';
    else:
      echo '3';
    endif;
  }

  // accessor to retrieve current underShip value
  public function getUnderShip() {
    return $this->underShip;
  }

  // mutator to alter underShip value
  public function setUnderShip() {
    $this->underShip = true;
  }

  // debugging method to examine the properties of this square
  public function testSquare() {
    echo "\n" . 'testing square:' . "\n";
    var_dump(get_object_vars($this));
  }

}
