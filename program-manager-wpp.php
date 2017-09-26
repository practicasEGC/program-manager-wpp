<?php
/*
Plugin Name: Program manager plugin
Author: José  Ángel Galindo Duarte
Version: 2.0
*/

require('metamodel.php');
require('readers.php');
require('writers.php');
require('settings.php');

add_shortcode( 'write_full_program', 'write_full' );
add_shortcode( 'write_room_program', 'write_rooms' );
add_shortcode( 'write_dc_program', 'write_dc' );
add_shortcode( 'write_reve_program', 'write_reve' );
add_shortcode( 'write_foro_program', 'write_foro' );
add_shortcode( 'write_dspl_program', 'write_dspl' );
add_shortcode( 'write_array_program', 'write_debug' );
add_shortcode('write_glance_program','write_glance');


add_action( 'wp_enqueue_scripts', 'my_plugin_register_scripts' );

wp_enqueue_script( 'media-upload'); 

//This creates a page for settings
if( is_admin() )
    $my_settings_page = new MySettingsPage();

function my_plugin_register_scripts(){
     wp_register_script('my-script',plugins_url( '/my-script.js', __FILE__ ), false, '1.0', 'all' );
     wp_register_style( 'my-style', plugins_url( '/my-style.css', __FILE__ ), false, '1.0', 'all' );
}

function write_glance(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new GlanceProgramWriter($program); 
	$writer->write();
}


function write_full(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new FullProgramWriter($program); 
	$writer->write();
}

function write_rooms(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new RoomProgramWriter($program); 
	$writer->write();
}



function write_debug(){
	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
		
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new DebugProgramWriter($program); 
	$writer->write();

}

function write_reve(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new PartialProgramWriter($program,"REVE"); 
	$writer->write();
}


function write_foro(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new PartialProgramWriter($program,"Foro Industrial <div><i>Room: Salón de actos</i></div>"); 
	$writer->write();
}


function write_dspl(){
	
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );
	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);
	$writer=new PartialProgramWriter($program,"DSPL"); 
	$writer->write();
}


function write_dc(){
	date_default_timezone_set('UTC');
	wp_enqueue_style( 'my-style' );
	wp_enqueue_script( 'my-script' );

	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('siteurl'),".",$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('siteurl'),'.',$plugin_options['main_days_file']);		
	
//	print_r($pre_days);print_r($main_days);print($pre_xlsx);print($main_xlsx);die();
	$reader = new XLSXProgramReader($pre_xlsx,$main_xlsx);
	$program=$reader->parseProgram($pre_days,$main_days);	
	$writer=new PartialProgramWriter($program,"Doctoral Symposium <div><i>Room: AULA 1</i></div>"); 
	$writer->write();
}

function extractVariables(){
	$plugin_options=get_option('program_manager_option');
	$pre_days=explode(",",$plugin_options['pre_days']);	
	$main_days=explode(",",$plugin_options['main_days']);
	$pre_xlsx=str_replace(get_option('site_url'),'.',$plugin_options['pre_days_file']);	
	$main_xlsx=str_replace(get_option('site_url'),'.',$plugin_options['pre_days_file']);		
}

