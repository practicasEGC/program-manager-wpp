<?php



//Función para añadir una página al menú de administrador de Wordpress
function program_plugin_menu(){
	add_menu_page('Program Manager Settings', 		//Título de la página
			'Program Manager Plugin Settings',	//Título del menú
			'administrator',			//Rol que puede acceder
			'program-manager-settings',		//Id de la página de opciones
			'printPageProgramSettings',		//Función que pinta la página de configuración del plugin
			'dashicons-admin-generic');		//Icono del menú
			

}
//La acción admin_menu se ejecuta después de colocar el menú básico del administrador. 
add_action('admin_menu','program_plugin_menu');

//Funcion que pinta la página de configuración del plugin
function printPageProgramSettings(){
?>
	
	<div class="wrap">
		<h2>Configuración plugin Program Manager</h2>
		<form method="POST" action="options.php">
			<?php 
				settings_fields('id-googleSheet');
				do_settings_sections( 'id-googleSheet' ); 
			?>
			<label>Id de la hoja de cálculo de Google:&nbsp;</label>
			<p>La id está contenida en la URL de la hoja, son los caracteres entre a .../d/ y /edit...<p/>
			<input 	type="text" size="60"
					name="id_google_sheet_value" 
					id="id_google_sheet_value" 
					value="<?php echo get_option('id_google_sheet_value'); ?>" />
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

/*
* Función que registra las opciones del formulario en una lista blanca para que puedan ser guardadas
*/
function id_google_settings(){
	register_setting('id-googleSheet',
			 'id_google_sheet_value',
			 'string');
}
add_action('admin_init','id_google_settings');

?>
