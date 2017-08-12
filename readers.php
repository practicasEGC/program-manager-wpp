<?php

class XLSXProgramReader
{
  private $pre_day_file;
  private $main_day_file;

  public function __construct($pre_day_file, $main_day_file)
  {
      $this->$pre_day_file = $pre_day_file;
      $this->$main_day_file = $main_day_file;
  }

  public function parseProgram()
  {

  }
}
