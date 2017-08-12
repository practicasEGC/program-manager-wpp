<?php

function write_program($program){
  foreach($program as $day => $slots) {
    echo "Key=" . $day . ", Value=" . $slots;
    echo "<br>";
  }
}
