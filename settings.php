<?php
class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        if( is_admin() ) {
        	function my_admin_load_styles_and_scripts() {
        		$mode = get_user_option( 'media_library_mode', get_current_user_id() ) ? get_user_option( 'media_library_mode', get_current_user_id() ) : 'grid';
        		$modes = array( 'grid', 'list' );
        			if ( isset( $_GET['mode'] ) && in_array( $_GET['mode'], $modes ) ) {
            				$mode = $_GET['mode'];
            				update_user_option( get_current_user_id(), 'media_library_mode', $mode );
        			}
        		if( ! empty ( $_SERVER['PHP_SELF'] ) && 'upload.php' === basename( $_SERVER['PHP_SELF'] ) && 'grid' !== $mode ) {
            			wp_dequeue_script( 'media' );
        		}
        	wp_enqueue_media();
    		}
    	add_action( 'admin_enqueue_scripts', 'my_admin_load_styles_and_scripts' );
	}
  
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Program Manager Settings', 
            'manage_options', 
            'program_manager_admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'program_manager_option' );
        ?>
        <div class="wrap">
            <h1>Program Manager Settings</h1>
            <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'program_manager_option_group' );
                do_settings_sections( 'program_manager_admin' );
                submit_button();
            ?>
	    </form>
	    <div>
		<p>We do provide the following shotcodes:</p>
		<p>write_full_program: To write the full program</p>
		<p>write_dc_program: To write the doctoral symposyum program</p>
		<p>write_reve_program: To write the REVE program</p>
		<p>write_dspl_program: To write the DSPL program</p>
		<p>write_array_program: To write the program metamodel</p>
		<p>write_glance_program: To write the program at a glance</p>
        <p>write_room_program: To write the listo of rooms per day<p>
	    </div>
	</div>

<script>
    jQuery(document).ready(function($){
    var main_custom_uploader;
        var pre_custom_uploader;
 


    $('#pre_upload_file_button').click(function(e) {
         
        e.preventDefault();
  
        //If the uploader object has already been created, reopen the dialog
        if (pre_custom_uploader) {
            pre_custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        pre_custom_uploader =  wp.media.frames.file_frame = wp.media({
            title: 'Choose File',
            button: {
                text: 'Choose File'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        pre_custom_uploader.on('select', function() {
            console.log(pre_custom_uploader.state().get('selection').toJSON());
            attachment = pre_custom_uploader.state().get('selection').first().toJSON();
            $('#pre_days_file').val(attachment.url);
        });

        //Open the uploader dialog
        pre_custom_uploader.open();

    });


    $('#main_upload_file_button').click(function(e) {
         
        e.preventDefault();
            //If the uploader object has already been created, reopen the dialog
        if (main_custom_uploader) {
            main_custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        main_custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose File',
            button: {
                text: 'Choose File'
            },
            multiple: true
        });

        //When a file is selected, grab the URL and set it as the text field's value
        main_custom_uploader.on('select', function() {
            console.log(main_custom_uploader.state().get('selection').toJSON());
            attachment = main_custom_uploader.state().get('selection').first().toJSON();
            $('#main_days_file').val(attachment.url);
        });

        //Open the uploader dialog
        main_custom_uploader.open();

    });


});
    </script>


	<?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'program_manager_option_group', // Option group
            'program_manager_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'program_manager_section_id', // ID
            'Program generator options', // Title
            array( $this, 'print_section_info' ), // Callback
            'program_manager_admin' // Page
        );  

        add_settings_field(
            'pre_days', // ID
            'PRE conference days (separated by ,)', // Title 
            array( $this, 'pre_days_callback' ), // Callback
            'program_manager_admin', // Page
            'program_manager_section_id' // Section           
        );      

        add_settings_field(
            'main_days', 
            'MAIN conference days (separated by ,)', 
            array( $this, 'main_days_callback' ), 
            'program_manager_admin', 
            'program_manager_section_id'
        );      
 
	add_settings_field(
            'pre_days_file', // ID
            'PRE conference xlsx file', // Title 
            array( $this, 'pre_days_file_callback' ), // Callback
            'program_manager_admin', // Page
            'program_manager_section_id' // Section           
        );      

        add_settings_field(
            'main_days_file', 
            'MAIN conference xlsx file', 
            array( $this, 'main_days_file_callback' ), 
            'program_manager_admin', 
            'program_manager_section_id'
        );      
 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['pre_days'] ) )
            $new_input['pre_days'] = sanitize_text_field( $input['pre_days'] );

        if( isset( $input['pre_days_file'] ) )
            $new_input['pre_days_file'] = sanitize_text_field( $input['pre_days_file'] );
	
	if( isset( $input['main_days'] ) )
            $new_input['main_days'] = sanitize_text_field( $input['main_days'] );

       if( isset( $input['main_days_file'] ) )
            $new_input['main_days_file'] = sanitize_text_field( $input['main_days_file'] );


        return $new_input;
    }


    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function pre_days_callback()
    {
        printf(
            '<input type="text" id="pre_days" name="program_manager_option[pre_days]" value="%s" />',
            isset( $this->options['pre_days'] ) ? esc_attr( $this->options['pre_days']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function main_days_callback()
    {
        printf(
            '<input type="text" id="main_days" name="program_manager_option[main_days]" value="%s" />',
            isset( $this->options['main_days'] ) ? esc_attr( $this->options['main_days']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function pre_days_file_callback()
    {
        printf(
   		'<input id="pre_days_file" type="text" size="36" name="program_manager_option[pre_days_file]" value="%s" /> 
    		<input id="pre_upload_file_button" class="button" type="button" value="Upload/Select PRE File" />',
	    isset( $this->options['pre_days_file'] ) ? esc_attr( $this->options['pre_days_file']) : ''
        );
    }
   
    /** 
     * Get the settings option array and print one of its values
     */
    public function main_days_file_callback()
    {
        printf(
            	'<input id="main_days_file" type="text" size="36" name="program_manager_option[main_days_file]" value="%s" /> 
    		<input id="main_upload_file_button" class="button" type="button" value="Upload/Select MAIN File" />',
         
            isset( $this->options['main_days_file'] ) ? esc_attr( $this->options['main_days_file']) : ''
        );
    }
 
 
}

