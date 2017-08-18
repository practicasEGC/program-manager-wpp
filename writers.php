<?php

function write_program($program){
  foreach($program as $slot_object)
  {
    echo "<div class=\"slot\">";
    echo "<div class=\"time_details\">" . $slot_object->time;
    echo '</div> ' ; //imprime hora + espacio
    echo "<div class=\"sessions\">";
    foreach ($slot_object->talk_groups as $talk_group) {
      # code...
      if($talk_group instanceof Pause){
        echo "<div class=\"session\">";
        echo "<div class=\"session_name\">".$talk_group->title. '</div>';
        echo '</div>';
      }
    }
  }

  print_r($program);
}
