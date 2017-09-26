<?php
class DebugProgramWriter
{
	private $program;
	
	public function __construct($program){
		$this->program=$program;
	
	}
	

	function write(){ 
		print_r($this->program);
	}

}
class PartialProgramWriter
{
	//This writer only support single day events
	private $program;
	private $allowed_name;

	public function __construct($program,$allowed_name){
		$this->program=$program;
		$this->allowed_name=$allowed_name;
	}
		
	
	function write(){ 
		foreach($this->program as $day => $program_day){
			$this->process_day($program_day->slots, "<h2>". $program_day->day_details ."</h2>");// we print the date
		}
	}


	function process_day($slots,$result){
			if($this->allowed_name ==="Foro Industrial <div><i>Room: Salón de actos</i></div>"){
				$maxSlots=8;
			}else{
				$maxSlots=50;
			}

			$numberofslots=0;
			
		        foreach($slots as $slot_object)
			{	
				$numberofslots=$numberofslots+1;
					
				$result.= "<div class=\"slot\">";
				$result.= "<div class=\"time_details\">" . $slot_object->time . '</div> ' ; //imprime hora + espacio
				$result.= "<div class=\"sessions\">";
				foreach ($slot_object->talks_groups as $talk_group) {
					
					
						if($talk_group instanceof Pause){
							$result.= "<div class=\"session\">";
							$result.= "<div class=\"session_name\">".$talk_group->title. '</div>';
						        $result.= '</div>';
						}else if($talk_group instanceof TalkGroup && ( trim($talk_group->title) == trim($this->allowed_name))) {
							
							$result.= "<div class=\"session\"></div>";    

								foreach($talk_group->talks as $talk){
									$result.= "<div class=\"talk\">".
									"<div class=\"talk_name\">". $talk->title.". </div>".
									"<div class=\"talk_authors\">". $talk->authors."</div>".
									"<div class=\"talk_data\">";
									if($talk->timming!=""){
										$result.= "<div>Timing: ".$talk->timming." min</div>";
									}
									$result.= "</div>". "</div>";
								}
						}
					
					
				}
				$result.= "</div></div>";
				
				//the counter is hardcoded. bad design :S		
				if((strpos($result, 'talk') !== false) &&( $numberofslots<$maxSlots)){
					echo $result;
					$result='';
				}

			}
	}

}

class GlanceProgramWriter{

	private $program;
	
	public function __construct($program){
		$this->program=$program;
	
	}
	

	function write(){ 

		$day_index=0;
		echo "<div class=\"glance_program\">";
		foreach($this->program as $day => $program_day){
		echo "<div class=\"glance_day\" >";

			echo "<div><b>". $program_day->day_details ."</b></div>";// we print the date
			
			if($program_day->is_Pre){
				$this->process_pre_day($program_day->slots);
			}else{
				$this->process_main_day($program_day->slots);		
			}

		echo "</div>";
		}
		echo "</div>";
	}


	function process_main_day($slots){

			foreach($slots as $slot_object)
			{	
				$unique_elements= array();
	
				echo "<div class=\"slot\">";
				echo "<div class=\"sessions\">";
				foreach ($slot_object->talks_groups as $talk_group) {
					if($talk_group instanceof Pause){
						echo "<div class=\"session\">";
						echo "<div class=\"session_name\">".$talk_group->title. '</div>';
						echo '</div>';
					}else if($talk_group instanceof TalkGroup){
						foreach($talk_group->talks as $talk){
							$unique_elements[]=$talk->event;
						}
	
					}
				
				}
				$unique_elements=array_unique($unique_elements);
				 

				foreach($unique_elements as $elem){
					echo"<div class=\"talk\">".$elem."</div>";
				}
				echo "</div>";
				echo "</div>";
			}
		


	}
	function process_pre_day($slots){

			foreach($slots as $slot_object)
			{	
				$unique_elements= array();
							
				echo "<div class=\"slot\">";
				echo "<div class=\"sessions\">";
				foreach ($slot_object->talks_groups as $talk_group) {
					if($talk_group instanceof Pause){
						echo "<div class=\"session\">";
						echo "<div class=\"session_name\">".$talk_group->title. '</div>';
						echo '</div>';
					}else if($talk_group instanceof TalkGroup){
						foreach($talk_group->talks as $talk){
							$unique_elements[]=$talk->event;
						}
					}
				}
				$unique_elements=array_unique($unique_elements);

				 

				foreach($unique_elements as $elem){
					echo"<div class=\"talk\">".$elem."</div>";
				}
				
							
				
				echo "</div>";
				echo "</div>";
			}
		


	}
}

class RoomProgramWriter{

	private $program;
	private $pre_rooms = array("SUM","AULA 1","Salón de actos");
	private $main_rooms = array("Almería","Almenara+Alanda","Málaga","Paraninfo");
	
	public function __construct($program){
		$this->program=$program;
	
	}
	

	function write(){ 

		$this->print_tab_bar($this->program);//this prints a tabular bar with cickable days

		$day_index=0;
		foreach($this->program as $day => $program_day){
			if($day_index==0){
				echo "<div id=\"". $day ."\" class=\"tab\">";
			}else{
				echo "<div id=\"". $day ."\" class=\"tab\" style=\"display:none\">";
			}	
			$day_index++;

			echo "<h2>". $program_day->day_details ."</h2>";// we print the date
			
			if($program_day->is_Pre){
				$this->process_pre_day($program_day->slots);
			}else{
				$this->process_main_day($program_day->slots);		
			}

		echo "</div>";
		}
	}

	function print_tab_bar(){
		echo "<div class=\"w3-bar w3-black\">";
		$index = 0;
		foreach($this->program as $day=>$program_day) {
			if($index==0){
				echo  "<button class=\"w3-bar-item w3-button\" style=\"background:#000\" onclick=\"openTab('".$day."')\">".$day."</button>";
			}else{
				echo  "<button class=\"w3-bar-item w3-button\" onclick=\"openTab('".$day."')\">".$day."</button>";
			}
			$index++;
		}
		echo "</div>";
	}

	function process_main_day($slots){

		foreach($this->main_rooms as $room){
			echo "<h2>". $room ."</h2>";// we print the date
			

			foreach($slots as $slot_object)	{	
				$unique_elements= array();
				
				echo "<div class=\"slot\">";
				echo "<div class=\"time_details\">" . $slot_object->time . '</div> ' ; //imprime hora + espacio
				echo "<div class=\"sessions\">";
				
				foreach ($slot_object->talks_groups as $talk_group) {
					if($talk_group instanceof Pause){
							echo "<div class=\"session\">";
							echo "<div class=\"session_name\">".$talk_group->title. '</div>';
									   
							echo '</div>';
						
					}else if($talk_group instanceof TalkGroup){
						echo "<div class=\"session\">"."</div>";
						
						if($talk_group->room == $room){ 
		 					foreach($talk_group->talks as $talk){
								$not_allowed = array("");
								if(!in_array($talk->event,$not_allowed )){
									$talk->time=$slot_object->time ;
									$unique_elements[]=$talk;
									}
								}
							}
							
						}
						
						
					}
					if(!empty($unique_elements)){
						$unique_elements=array_unique($unique_elements);
						foreach($unique_elements as $talk){
							echo $talk;
						}
					}
	
					echo "</div>";
					echo "</div>";
				}
				
			
			}
		}
	function process_pre_day($slots){
			foreach($this->pre_rooms as $room){
				echo "<h2>". $room ."</h2>";// we print the date
				
				foreach($slots as $slot_object){	

					$unique_elements= array();
					
					echo "<div class=\"slot\">";
					echo "<div class=\"time_details\">" . $slot_object->time . '</div> ' ; //imprime hora + espacio
					echo "<div class=\"sessions\">";
					
					foreach ($slot_object->talks_groups as $talk_group) {
						if($talk_group instanceof Pause){
							echo "<div class=\"session\">";
							echo "<div class=\"session_name\">".$talk_group->title. '</div>';
							echo '</div>';
						}
						if($talk_group instanceof TalkGroup){
							echo "<div class=\"session\">"."</div>";
							if( !empty($talk_group->talks)){//Es uno de los eventos a mostrar
								foreach($talk_group->talks as $talk){
									if($talk->room == $room){
										$unique_elements[]=$talk;
									}
								}
							}
							
			
						}
						
					}
					if(!empty($unique_elements)){
						$unique_elements=array_unique($unique_elements);
						foreach($unique_elements as $talk){
							echo $talk;
						}
					}
					
					echo "</div>";
					echo "</div>";
					
				}
				


		}

	}
}


class FullProgramWriter{

	private $program;
	
	public function __construct($program){
		$this->program=$program;
	
	}
	

	function write(){ 

		$this->print_tab_bar($this->program);//this prints a tabular bar with cickable days

		$day_index=0;
		foreach($this->program as $day => $program_day){
			if($day_index==0){
				echo "<div id=\"". $day ."\" class=\"tab\">";
			}else{
				echo "<div id=\"". $day ."\" class=\"tab\" style=\"display:none\">";
			}	
			$day_index++;

			echo "<h2>". $program_day->day_details ."</h2>";// we print the date
			
			if($program_day->is_Pre){
				$this->process_pre_day($program_day->slots);
			}else{
				$this->process_main_day($program_day->slots);		
			}

		echo "</div>";
		}
	}

	function print_tab_bar(){
		echo "<div class=\"w3-bar w3-black\">";
		$index = 0;
		foreach($this->program as $day=>$program_day) {
			if($index==0){
				echo  "<button class=\"w3-bar-item w3-button\" style=\"background:#000\" onclick=\"openTab('".$day."')\">".$day."</button>";
			}else{
				echo  "<button class=\"w3-bar-item w3-button\" onclick=\"openTab('".$day."')\">".$day."</button>";
			}
			$index++;
		}
		echo "</div>";
	}

	function process_main_day($slots){

			foreach($slots as $slot_object)
			{	

				echo "<div class=\"slot\">";
				echo "<div class=\"time_details\">" . $slot_object->time . '</div> ' ; //imprime hora + espacio
				echo "<div class=\"sessions\">";
				foreach ($slot_object->talks_groups as $talk_group) {
					if($talk_group instanceof Pause){
						echo "<div class=\"session\">";
						echo "<div class=\"session_name\">".$talk_group->title. '</div>';
					       		
						echo '</div>';
					}else if($talk_group instanceof TalkGroup){
						echo "<div class=\"session\">".
							"<div class=\"session_name\">".$talk_group->title."</div>".
							"<div class=\"session_details\">Chair: ".$talk_group->chair. ' - Room: '.$talk_group->room .'</div>'.
						"</div>";    
		 
					foreach($talk_group->talks as $talk){
							$not_allowed = array("");
								if(!in_array($talk->event,$not_allowed )){
									echo "<div class=\"talk\">".
									"<div class=\"talk_name\">". $talk->title."<span> <i>(". $talk->event.") </i> </span></div>".
									"<div class=\"talk_authors\">". $talk->authors."</div>".
									"<div class=\"talk_data\">";
										if($talk->timming!=""){
											echo "<div>Timing: ".$talk->timming." min</div>";
										}
									echo "</div>". "</div>";

								
								}
				
							}
								}
				}
				echo "</div>";
				echo "</div>";
			}
		


	}
	function process_pre_day($slots){

			foreach($slots as $slot_object)
			{	

				echo "<div class=\"slot\">";
				echo "<div class=\"time_details\">" . $slot_object->time . '</div> ' ; //imprime hora + espacio
				echo "<div class=\"sessions\">";
				foreach ($slot_object->talks_groups as $talk_group) {
					if($talk_group instanceof Pause){
						echo "<div class=\"session\">";
						echo "<div class=\"session_name\">".$talk_group->title. '</div>';
						echo '</div>';
					}else if($talk_group instanceof TalkGroup){
			      
						if($talk_group->title == "" && !empty($talk_group->talks)){//Es uno de los eventos a mostrar
							foreach($talk_group->talks as $talk){
									echo "<div class=\"talk_pre\">".'<b>'. $talk->event."</b>".': '. $talk->title;
									if($talk->authors!=""){
										echo " - "."<div class=\"talk_authors\">". $talk->authors."</div>" ;
									}
								echo 
  "Room: ".$talk->room . '</div>'; 	
							}
						}else if($talk_group->title == "" && empty($talk_group->talks)){//es un hueco
							echo "<div class=\"talk_pre\"></div>";
						}else {//No queremos mostrar más que el hueco o el titulo
							echo "<div class=\"talk_pre\">"."<b><a href=\"".$talk_group->url."\" target=\"new\">".$talk_group->title."</a></b>"."</div>";    
						}
					}
				}
				echo "</div>";
				echo "</div>";
			}
		


	}
}
