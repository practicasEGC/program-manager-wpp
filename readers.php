<?php
require('XLSXReader.php');

class XLSXProgramReader
{
  protected $pre_day_xlsx;
  protected $pre_day_talks;

  public function __construct($pre_file_path,$main_file_path)
  {
    $this->pre_day_xlsx=new XLSXReader($pre_file_path);
    $this->pre_day_talks =$this->pre_day_xlsx->getSheet("Talks")->getData();
  }

  public function parseProgram()
  {
    //By now only reading Monday
    return $this->processPreDay( $this->pre_day_xlsx->getSheet('Tuesday')->getData());
  }

  function processPreDay($day_sheet) {
    $day_slot = array();
    $i = 0;
  	//$date=$data[0][1];
  	foreach($day_sheet as $row) {
      if($i!=0){ // We avoid the first line. then, we go thought each line and print 1) the hours [0] 2) the title. Also, if title contains "(" list the sessions. Also, check [2]
        $time=$row[0]; //imprime hora + espacio
        $talks_group = array();
        if (strpos($row[1], '(') !== FALSE){//contiene charlas
          $talks_group[]= new TalkGroup($this->getTitle($row[1]),$this->process_pre_talks($this->getCode($row[1]),$time));
        }else{//si no contiene charlas es porque el titulo la incluye
          $talks_group = array();
//          $talks_group[] = new TalkGroup($this->getTitle($row[1]),new Pause($row[1],$time));
          $talks_group[] = new Pause($row[1],$time);

        }
        if (strpos($row[2], '(') !== FALSE){//contiene charlas
          $talks_group[]= new TalkGroup($this->getTitle($row[2]),$this->process_pre_talks($this->getCode($row[2]),$time));
        }
        if (strpos($row[3], '(') !== FALSE){//contiene charlas ... hay que ver como guardar el nombre del slot si existe
          $talks_group[]= new TalkGroup($this->getTitle($row[3]),$this->process_pre_talks($this->getCode($row[3]),$time));
        }
        $slot = new Slot($time,$talks_group);

        //ver aqui lo de a
        $day_slots[]=$slot;
      }
      $i++;
    }
    return $day_slots;

  }

  function process_pre_talks($data,$time){
    $talks_collection= array();
    foreach($this->pre_day_talks as $talk) {
      if($talk[1]==$data){
          $event= $talk[0];
          $code= $talk[1];
          $title = $talk[2];
          $authors= $talk[3];
          $abstract= $talk[4];
          $talk = new Talk($time,$title,$event,$code, $authors, $abstract);
          $talks_collection[]=$talk;
      }
    }
    return $talks_collection;
  }

  private function getCode($text){
    return substr( substr( $text, strrpos( $text, '(')  + 1), 0, strpos( substr( $text, strrpos( $text, '(')  + 1), ')'));
  }

  private function getTitle($text){
    return substr( $text, 0,strrpos( $text, '(') );
  }
}
