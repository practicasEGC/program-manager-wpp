<?php

include "conexion_BD.php";
include "quickstart.php";

// ============================================================
//Metodo que mete el programa completo en la BD (leyendo las dos hojas de cálculo)
// ============================================================
function sheet_to_DB($spreadsheetIdpre , $spreadsheetIdmain){
    $id_programa = create_program();
    process_pre_day($id_programa,$spreadsheetIdpre);
    process_main_day($id_programa,$spreadsheetIdmain);
}


// ============================================================
// ===================Creación del programa====================
// ============================================================
function create_program(){
    $conn = connectDB();
    // Creo el programa
    $sql = "INSERT INTO program(id,prog_version,selected_version,state)
	VALUES ('',1,1,'final')";
    $conn->query($sql);
    
    $id_programa = $conn->insert_id;
    disconnectDB($conn);
    return $id_programa;
}

// ============================================================
// ========================PRE-PROGRAMA========================
// ============================================================
function process_pre_day($id_programa,$spreadsheetIdpre){
    
    $client = getClient();
    $service = new Google_Service_Sheets($client);
    
    $range_monday = 'Monday!A:D';
    $response_mon = $service->spreadsheets_values->get($spreadsheetIdpre, $range_monday);
    $range_tuesday = 'Tuesday!A:D';
    $response_tues = $service->spreadsheets_values->get($spreadsheetIdpre, $range_tuesday);
    $range_pre_talks = 'Talks!A2:G';
    $response_pre_talks = $service->spreadsheets_values->get($spreadsheetIdpre, $range_pre_talks);
    
    $values_mon = $response_mon->getValues();
    $values_tues = $response_tues->getValues();
    
    $values_pre_talks = $response_pre_talks->getValues();
    
    $i = 0;
    $id_day=0;
    foreach ($values_mon as $row) {
        $id_day= process_pre_slots($row,$i,$id_programa,$values_pre_talks,$id_day);
        $i = 1;
    }
    $i = 0;
    $id_day=0;
    foreach ($values_tues as $row) {
        $id_day= process_pre_slots($row,$i,$id_programa,$values_pre_talks,$id_day);
        $i = 1;
    }
}


function process_pre_slots($row,$i,$id_programa,$values_pre_talks,$id_day){
    if ($i == 0) { // Solo la primera vez crea el day, con los detalles del dia. (wp_day)
        $conn = connectDB();
        $sql = "INSERT INTO _day(id,day_details,isPre,pro_id)
	       VALUES ('','$row[1]',1,$id_programa)";
        $conn->query($sql);
        $id_day = $conn->insert_id;
        disconnectDB($conn);
    } else {
        if (strpos($row[1], '(') === FALSE) { // no contiene charlas
            // Necesito guardar slots
            $conn = connectDB();
            $sql = "INSERT INTO slot (id,total_time,day_id) VALUES ('','$row[0]',$id_day)";
            $conn->query($sql);
            $id_slot = $conn->insert_id;
            disconnectDB($conn);
            // Guardar descansos
            $conn = connectDB();
            $sql = "INSERT INTO talk_group(id,title,chair,url,room,code,slot_id) VALUES ('','$row[1]','','','','',$id_slot)";
            $conn->query($sql);
            disconnectDB($conn);
        } else {
            //Guardo slot
            $conn = connectDB();
            $sql = "INSERT INTO slot(id,total_time,day_id) VALUES ('','$row[0]',$id_day)";
            $conn->query($sql);
            $id_slot = $conn->insert_id;
            disconnectDB($conn);
            //Guardo talkGroup
            if (strpos($row[1], '(') !== FALSE) { // contiene charlas
                process_pre_talks($row[1], $id_slot,$values_pre_talks);
            }
            if (strpos($row[2], '(') !== FALSE) { // contiene charlas
                process_pre_talks($row[2], $id_slot,$values_pre_talks);
            }
            if (strpos($row[3], '(') !== FALSE) { // contiene charlas
                process_pre_talks($row[3], $id_slot,$values_pre_talks);
            }
        }
    }
    return $id_day;
}

function process_pre_talks($data, $slot,$values_pre_talks){
    $code = substr($data, 1, - 1); //Guardo el codigo en una variable quitandole los parentesis
    $conn = connectDB();
    $sql = "INSERT INTO talk_group(id,title,chair,url,room,code,slot_id)
	                       VALUES ('','$data','','','','$code',$slot)";
    $conn->query($sql);
    $id_talkGroup = $conn->insert_id;
    disconnectDB($conn);
    foreach ($values_pre_talks as $talk) {
        if ($talk[1] === $code ) { // Si coincide el code del talk group con el de la fila de la sheet 'Talks'
            $title = "";
            if (empty($talk[2])){ //Si el titulo esta vacio -> Event: URL
                $title = $talk[0].": ".$talk[6];
            }else{ //Si hay titulo -> Event: Titulo
                $title = $talk[0].": ".$talk[2];
            }
            $conn = connectDB();
            $sql = "INSERT INTO talk(id,title,authors,timming,tg_id) VALUES ('','$title','$talk[3]','',$id_talkGroup)";
            $conn->query($sql);
            disconnectDB($conn);
        }
    }
}


// ============================================================
// ========================MAIN-PROGRAMA=======================
// ============================================================

function process_main_day($id_programa,$spreadsheetIdmain){
    
    $client = getClient();
    $service = new Google_Service_Sheets($client);
    
    $range_wednesday = 'Wednesday!A:D';
    $response_wed = $service->spreadsheets_values->get($spreadsheetIdmain, $range_wednesday);
    $range_thursday = 'Thursday!A:D';
    $response_thurs = $service->spreadsheets_values->get($spreadsheetIdmain, $range_thursday);
    $range_friday = 'Friday!A:D';
    $response_fri = $service->spreadsheets_values->get($spreadsheetIdmain, $range_friday);
    $range_main_talks = 'Talks!E2:I';
    $response_main_talks = $service->spreadsheets_values->get($spreadsheetIdmain, $range_main_talks);
    $range_main_rooms = 'rooms!A2:B';
    $response_main_rooms = $service->spreadsheets_values->get($spreadsheetIdmain, $range_main_rooms);
    $range_main_chairs = 'session chairs!A2:D';
    $response_main_chairs = $service->spreadsheets_values->get($spreadsheetIdmain, $range_main_chairs);
    
    $values_wed = $response_wed->getValues();
    $values_thurs = $response_thurs->getValues();
    $values_fri = $response_fri->getValues();
    $values_main_talks = $response_main_talks->getValues();
    $values_main_rooms = $response_main_rooms->getValues();
    $values_main_chairs = $response_main_chairs->getValues();
    
    $i = 0;
    $id_day=0;
    foreach ($values_wed as $row) {
        $id_day=process_main_slots($row,$i,$id_programa,$values_main_talks,$id_day,$values_main_rooms,$values_main_chairs);
        $i = 1;
    }
    $i = 0;
    $id_day=0;
    foreach ($values_thurs as $row) {
        $id_day=process_main_slots($row,$i,$id_programa,$values_main_talks,$id_day,$values_main_rooms,$values_main_chairs);
        $i = 1;
    }
    $i = 0;
    $id_day=0;
    foreach ($values_fri as $row) {
        $id_day=process_main_slots($row,$i,$id_programa,$values_main_talks,$id_day,$values_main_rooms,$values_main_chairs);
        $i = 1;
    }
    
}

function process_main_slots($row,$i,$id_programa,$values_main_talks,$id_day,$values_main_rooms,$values_main_chairs){
    if ($i == 0) { // Solo la primera vez crea el day, con los detalles del dia. (wp_day)
        $conn = connectDB();
        $sql = "INSERT INTO _day(id,day_details,isPre,pro_id)
	       VALUES ('','$row[1]',0,$id_programa)";
        $conn->query($sql);
        $id_day = $conn->insert_id;
        disconnectDB($conn);
    } else {
        if (strpos($row[1], '(') === FALSE) { // no contiene charlas
            // Necesito guardar slots
            $conn = connectDB();
            $sql = "INSERT INTO slot(id,total_time,day_id)
	       VALUES ('','$row[0]',$id_day)";
            $conn->query($sql);
            $id_slot = $conn->insert_id;
            disconnectDB($conn);
            // Guardar descansos
            $conn = connectDB();
            $sql = "INSERT INTO talk_group(id,title,chair,url,room,code,slot_id)
	       VALUES ('','$row[1]','','','','',$id_slot)";
            $conn->query($sql);
            disconnectDB($conn);
        } else {
            //Guardo slot
            $conn = connectDB();
            $sql = "INSERT INTO slot(id,total_time,day_id)
	                           VALUES ('','$row[0]',$id_day)";
            $conn->query($sql);
            $id_slot = $conn->insert_id;
            disconnectDB($conn);
            //Guardo talkGroup
            if (strpos($row[1], '(') !== FALSE) { // contiene charlas
                process_main_talks($row[1], $id_slot,$values_main_talks,$values_main_rooms,$values_main_chairs);
            }
            if (strpos($row[2], '(') !== FALSE) { // contiene charlas
                process_main_talks($row[2], $id_slot,$values_main_talks,$values_main_rooms,$values_main_chairs);
            }
        }
    }
    return $id_day;
}


function process_main_talks($data, $slot,$values_main_talks,$values_main_rooms,$values_main_chairs){
    $posA = strpos($data, '(');
    $code = substr($data, $posA+1, -1);
    $title_group = substr($data, 0, $posA);
    $room = get_room($code,$values_main_rooms);
    $chair = get_chair($code,$values_main_chairs);
    $conn = connectDB();
    $sql = "INSERT INTO talk_group(id,title,chair,url,room,code,slot_id)
	                       VALUES ('','$title_group','$chair','','$room','$code',$slot)";
    $conn->query($sql);
    $id_talkGroup = $conn->insert_id;
    disconnectDB($conn);
    foreach ($values_main_talks as $talk) {
        if ($talk[0] === $code ) { // Si coincide el code del talk group con el de la fila de la sheet 'Talks'
            $conn = connectDB();
            $sql = "INSERT INTO talk(id,title,authors,timming,tg_id)
	       VALUES ('','$talk[2]','$talk[3]','$talk[4]',$id_talkGroup)";
            $conn->query($sql);
            disconnectDB($conn);
        }
    }
}

function get_room($code,$values_main_rooms){
    $room="";
    foreach ($values_main_rooms as $row){
        if ($row[0] === $code){
            $room = $row[1];
        }
    }
    return $room;
}

function get_chair($code,$values_main_chairs){
    $chair="";
    foreach ($values_main_chairs as $row){
        if ($row[3] === $code){
            $nombre = $row[0];
            $apellidos = $row[1];
            $chair = $nombre." ".$apellidos;
        }
    }
    return $chair;
}

