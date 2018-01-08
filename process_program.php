<?php

include "conexion_BD.php";



function sheet_to_DB($spreadsheetIdpre , $sheet_id_main){
    $id_programa = create_program();
    process_pre_day($id_programa,$spreadsheetIdpre);
}




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


function process_pre_day($id_programa,$spreadsheetIdpre){
    include "quickstart.php";
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
    $code = substr($data, 1, - 1);
    $conn = connectDB();
    $sql = "INSERT INTO talk_group(id,title,chair,url,room,code,slot_id)
	                       VALUES ('','$data','','','','$code',$slot)";
    $conn->query($sql);
    $id_talkGroup = $conn->insert_id;
    disconnectDB($conn);
    // Necesito crear un TalkGroup que los englobe o cambiar el modelo de dominio
    foreach ($values_pre_talks as $talk) {
        if ($talk[1] === $code ) { // && $talk[5] == "true"?¿?¿
            $title = "";
            if (empty($talk[2])){
                $title = $talk[0].": ".$talk[6];
            }else{
                $title = $talk[0].": ".$talk[2];
            }
            $conn = connectDB();
            $sql = "INSERT INTO talk(id,title,authors,timming,tg_id) VALUES ('','$title','$talk[3]','',$id_talkGroup)";
            $conn->query($sql);
            disconnectDB($conn);
        }
    }
}


