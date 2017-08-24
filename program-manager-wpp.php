<?php
/*
Plugin Name: Program manager plugin 2
*/

require('metamodel.php');
require('readers.php');
require('writers.php');
require('settings.php');

add_shortcode( 'write_full_program', 'write_full' );
add_shortcode( 'write_dc_program', 'write_dc' );
add_shortcode( 'write_reve_program', 'write_reve' );
add_shortcode( 'write_dspl_program', 'write_dspl' );
add_shortcode( 'write_array_program', 'write_debug' );


add_action( 'wp_enqueue_scripts', 'my_plugin_register_scripts' );


if( is_admin() )
    $my_settings_page = new MySettingsPage();



function my_plugin_register_scripts(){
     wp_register_script('my-script',plugins_url( '/my-script.js', __FILE__ ), false, '1.0', 'all' );
     wp_register_style( 'my-style', plugins_url( '/my-style.css', __FILE__ ), false, '1.0', 'all' );
}

function write_full(){

	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );


	$reader = new XLSXProgramReader('./wp-content/plugins/program-manager-wpp/programme-pre-v9.xlsx','./wp-content/plugins/program-manager-wpp/programme-main-v10.xlsx');
	$program=$reader->parseProgram(array('Monday','Tuesday'),array('Wednesday','Thursday','Friday'));
	$writer=new FullProgramWriter($program); 
	$writer->write();
}

function write_debug(){

	$reader = new XLSXProgramReader('./wp-content/plugins/program-manager-wpp/programme-pre-v9.xlsx','./wp-content/plugins/program-manager-wpp/programme-main-v10.xlsx');
	$program=$reader->parseProgram(array('Monday','Tuesday'),array('Wednesday','Thursday','Friday'));
	$writer=new DebugProgramWriter($program); 
	$writer->write();

}

function write_reve(){

	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );


	$reader = new XLSXProgramReader('./wp-content/plugins/program-manager-wpp/programme-pre-v9.xlsx','./wp-content/plugins/program-manager-wpp/programme-main-v10.xlsx');
	$program=$reader->parseProgram(array('Monday','Tuesday'),array('Wednesday','Thursday','Friday'));
	//$writer=new PartialProgramWriter($program,"Doctoral Symposium"); 
	$writer=new PartialProgramWriter($program,"REVE"); 
	$writer->write();
}


function write_dspl(){

	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );


	$reader = new XLSXProgramReader('./wp-content/plugins/program-manager-wpp/programme-pre-v9.xlsx','./wp-content/plugins/program-manager-wpp/programme-main-v10.xlsx');
	$program=$reader->parseProgram(array('Monday','Tuesday'),array('Wednesday','Thursday','Friday'));
	//$writer=new PartialProgramWriter($program,"Doctoral Symposium"); 
	$writer=new PartialProgramWriter($program,"DSPL"); 
	$writer->write();
}


function write_dc(){

	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );


	$reader = new XLSXProgramReader('./wp-content/plugins/program-manager-wpp/programme-pre-v9.xlsx','./wp-content/plugins/program-manager-wpp/programme-main-v10.xlsx');
	$program=$reader->parseProgram(array('Monday','Tuesday'),array('Wednesday','Thursday','Friday'));
	//$writer=new PartialProgramWriter($program,"Doctoral Symposium"); 
	$writer=new PartialProgramWriter($program,"Doctoral Symposium"); 
	$writer->write();
}

