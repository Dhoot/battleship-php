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

  // each ship is required to keep track of which squares it occupies
  private $squares = array();

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

  // debugging method to examine the properties of this ship
  public function testShip() {
    echo "\n" . 'testing ship:' . "\n";
    var_dump(get_object_vars($this));
  }

}
