<?php

Class Square
{
  // this boolean property identifies whether a ship is occupying this square
  private $underShip;

  // this string maintains which ship this square resides under
  private $shipName;

  // this boolean property keeps track of whether this square as been attacked by the enemy player
  private $wasAttacked;

  // the square constructor
  public function __construct($posX, $posY)
  {
    $this->underShip = false;
    $this->wasAttacked = false;
  }

  // display square state in the CLI
  public function displaySquare() {
    if($this->underShip && $this->wasAttacked):
      return 'X';
    elseif($this->underShip):
      return 'H';
    elseif($this->wasAttacked):
      return '-';
    else:
      return '3';
    endif;
  }

  // accessor to retrieve current underShip value
  public function getUnderShip() {
    return $this->underShip;
  }

  // accessor to retrieve current shipName
  public function getShipName() {
    return $this->shipName;
  }

  // mutator to alter underShip value
  public function setUnderShip() {
    $this->underShip = true;
  }

  // mutator to alter wasAttacked value
  public function setWasAttacked() {
    $this->wasAttacked = true;
  }

  // mutator to alter shipName
  public function setShipName($name) {
    $this->shipName = $name;
  }
}
