<?php

class Program
{
  public $day_slots_map;



}

class Slot // This class represents the simplest event
{
  public $time;
  public $title;
}

class Session extends Slot
{
   public String[] $talks;
   public String $chair;

   public function __construct($time, $title, $talks, $chair)
   {
       parent::__construct($time, $title);
       $this->talks = $talks;
       $this->chair = $chair;
   }

   public function __toString()
   {
       $res="";
       $this->;
       return $res;
   }
}

class Talk extends Slot
{

  public function __construct($time, $title, $, $)
  {
      parent::__construct($time, $title);

  }
}
