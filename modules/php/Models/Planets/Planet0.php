<?php
namespace PU\Models\Planets;

class Planet0 extends \PU\Models\Planet
{
  public function __construct($player)
  {
    $this->name = clienttranslate('KSB-4156');
    $this->desc = clienttranslate('Score medals for every complete row and column.');
    parent::__construct($player);
  }

  protected $id = '0';
  protected $columnMedals = [ 1,1,1,2,2,3,3,2,2,1,1,1 ];
  protected $rowMedals = [ 1,1,1,2,2,3,3,2,2,1,1,1 ];
  protected $terrains = [ 
    [ NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING ],
    [ NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING ],
    [ NOTHING, NOTHING, LAND, ICE, ICE, ICE, LAND, LIFEPOD, ICE, LAND, NOTHING, NOTHING ],
    [ NOTHING, LAND, LAND, LAND, LAND, ICE, LAND, LAND, ICE, ICE, LAND, NOTHING ],
    [ NOTHING, LIFEPOD, LAND, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LIFEPOD, NOTHING ],
    [ ICE, ICE, LAND, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LAND, LAND ],
    [ ICE, ICE, LAND, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LAND, LAND ],
    [ NOTHING, LAND, LIFEPOD, LAND, ICE, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING ],
    [ NOTHING, LAND, LAND, ICE, ICE, LAND, LAND, LAND, LIFEPOD, LAND, LAND, NOTHING ],
    [ NOTHING, NOTHING, LAND, ICE, LAND, LIFEPOD, LAND, LAND, LAND, LAND, NOTHING, NOTHING ],
    [ NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING ],
    [ NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING ]    
  ];
}
