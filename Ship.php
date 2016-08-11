<?php

Class Ship
{
  // ( 5 = Carrier, 4 = Battleship, 3 = Submarine, 3 = Destroyer, 2 = Patrol boat )
  // size is the number of squares a ship occupies on the game board (e.g. Patrol Boat is of size 2)
  private $size;

  // name is title of the unit (i.e "Patrol Boat" or "Battleship")
  private $name;

  // remainingHits is the number of squares a ship occupies that have not yet been attacked
  private $remainingHits;

  // when all squares a ship occupies have been attacked, the ship sinks
  private $isSunk;

  // ship constuctor
  public function __construct($name, $size)
  {
    $this->name = $name;
    $this->size = $size;
    $this->remainingHits = $size;
    $this->isSunk = false;
  }

  // accessor for ship name
  public function getName() {
    return $this->name;
  }

  // accessor for ship size
  public function getSize() {
    return $this->size;
  }

  // accessor for remainingHits
  public function getRemainingHits() {
    return $this->remainingHits;
  }

  // accessor for isSunk
  public function getIsSunk() {
    return $this->isSunk;
  }

  // decrements value of remainingHits
  public function receiveHit() {
    $this->remainingHits--;
  }

  // mutator to alter value of isSunk
  public function sink() {
    $this->isSunk = true;
  }
}
