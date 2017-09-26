<?php
require('XLSXReader.php');

class XLSXProgramReader
{
       
  protected $pre_day_xlsx;
  protected $pre_day_talks;
  protected $main_day_xlsx;
  protected $main_day_talks;
  protected $main_day_chairs;

  public function __construct($pre_file_path,$main_file_path)
  {
    $this->pre_day_xlsx=new XLSXReader($pre_file_path);
    $this->pre_day_talks =$this->pre_day_xlsx->getSheet("Talks")->getData();
    $this->main_day_xlsx=new XLSXReader($main_file_path);
    $this->main_day_talks =$this->main_day_xlsx->getSheet("Talks")->getData();
    $this->main_day_chairs =$this->main_day_xlsx->getSheet("Sessions")->getData();
   
  } 
  

  public function parseProgram($pre_days,$main_days)
  {
	$program = array();  
  	foreach($pre_days as $day){
 		$program[$day]=	$this->processDay( $this->pre_day_xlsx->getSheet($day)->getData(),true);	
	}

	foreach($main_days as $day){
		$program[$day]=	$this->processDay( $this->main_day_xlsx->getSheet($day)->getData(),false);
	}
	return $program;	
  }

  function processDay($day_sheet,$isPre) {
	  $day_slots = array();
	  $day_details=$day_sheet[0][1];
    $i = 0;
 	foreach($day_sheet as $row) {
      if($i!=0){ // We avoid the first line. then, we go thought each line and print 1) the hours [0] 2) the title. Also, if title contains "(" list the sessions. Also, check [2]
        $time=$row[0]; //imprime hora + espacio
        $talks_group = array();
        if (strpos($row[1], '(') !== FALSE){//contiene charlas
 		$group=new TalkGroup($this->getTitle($row[1]),$this->process_talks($this->getCode($row[1]),$time,$isPre),$this->search_chair($this->getCode($row[1])),$this->search_room($this->getCode($row[1])));
		$group->url=$this->getURL($row[1]);
		$talks_group[]=$group;	    
	}else{//si no contiene charlas es porque el titulo la incluye
          $talks_group = array();
//          $talks_group[] = new TalkGroup($this->getTitle($row[1]),new Pause($row[1],$time));
          $talks_group[] = new Pause($time,$row[1]);

        }
        if (strpos($row[2], '(') !== FALSE){//contiene charlas
	  $group=new TalkGroup($this->getTitle($row[2]),$this->process_talks($this->getCode($row[2]),$time,$isPre),$this->search_chair($this->getCode($row[2])),$this->search_room($this->getCode($row[2])));		
	  $group->url=$this->getURL($row[2]);
	  $talks_group[]=$group; 
        }
        if (strpos($row[3], '(') !== FALSE){//contiene charlas ... hay que ver como guardar el nombre del slot si existe
	  $group= new TalkGroup($this->getTitle($row[3]),$this->process_talks($this->getCode($row[3]),$time,$isPre),$this->search_chair($this->getCode($row[2])),$this->search_room($this->getCode($row[3])));
	  $group->url=$this->getURL($row[3]);
	  $talks_group[]=$group;
	}
        $slot = new Slot($time,$talks_group);

        //ver aqui lo de a
        $day_slots[]=$slot;
      }
      $i++;
    }
	return new Day($day_details,$isPre,$day_slots);

  }

  function search_chair($data){
  	 foreach($this->main_day_chairs as $chair) {
		 if($chair[0]==$data){
			return $chair[1].' '.$chair[2]; 
		 }
	 }
	return '';
  }

  function search_room($data){
  	 foreach($this->main_day_chairs as $chair) {
		 if($chair[0]==$data){
			return $chair[3]; 
		 }
	 }
	return '';
  }


  function process_talks($data,$time,$is_Pre){
    $talks_collection= array();
    
    if($is_Pre){
   	 foreach($this->pre_day_talks as $talk) {
     	 	if($talk[1]==$data){
          		$event= $talk[0];
          		$code= $talk[1];
          		$title = $talk[2];
          		$authors= $talk[3];
							$timming=$talk[4];

							$abstract= $talk[5];
						  $room =$talk[8];
          		$talk = new Talk($time,$title,$event,$code, $authors, $abstract);
							$talk->room=$room;
							$talk->timming= $timming;

							$talks_collection[]=$talk;
      		}
	 }
    }
    else if(!$isPre){
    	foreach($this->main_day_talks as $talk) {
     		if($talk[4]==$data){
          		$event= $talk[3];
          		$code= $talk[4];
          		$title = $talk[6];
          		$authors= $talk[7];
          		$abstract= $talk[12];
							$timming=$talk[8];
							$talk = new Talk($time,$title,$event,$code, $authors, $abstract);
							$talk->timming= $timming;
							$talks_collection[]=$talk;
      	}
	 		}
    }
    return $talks_collection;
  }

  private function getCode($text){
    return substr( substr( $text, strrpos( $text, '(')  + 1), 0, strpos( substr( $text, strrpos( $text, '(')  + 1), ')'));
  }

  private function getURL($text){
    return substr( $text, strrpos( $text, ')')  + 1);
  }

  private function getTitle($text){
    return substr( $text, 0,strrpos( $text, '(') );
  }
}
