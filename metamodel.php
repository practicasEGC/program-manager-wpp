<?php

class Day
{
	public $day_details;
	public $is_Pre;
	public $slots;

	public function __construct($day_details,$is_Pre,$slots){
		$this->day_details=$day_details;
		$this->is_Pre=$is_Pre;
		$this->slots=$slots;
	}
}

class Slot
{
  public $time;
  public $talks_groups;

  public function __construct($time,$talks_groups)
  {
    $this->time=$time;
    $this->talks_groups=$talks_groups;
  }

}
class TalkGroup
{
  public $title;
  public $talks;
  public $url;

  public function __construct($title,$talks)
  {
    $this->title=$title;
    $this->talks=$talks;
  }
}

class Talk
{
  public $time;
  public $title;
  public $event;
  public $code;
  public $authors;
  public $abstract;
  public $timming;

  public function __construct($time,$title,$event,$code, $authors, $abstract)
  {
      $this->time=$time;
      $this->title=$title;
      $this->event=$event;
      $this->code=$code;
      $this->authors=$authors;
      $this->abstract=$abstract;
  }
}

class Pause
{
  public $time;
  public $title;

  public function __construct($time,$title)
  {
      $this->time=$time;
      $this->title=$title;
  }
}
