<?php
	
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly
		}
	
	
	class Wolly_Pal_Options
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
	    }
	
	    /**
	     * Add options page
	     */
	    public function add_plugin_page()
	    {
	        // This page will be under "Settings"
	        add_options_page(
	            'Impostazioni Pallazza', 
	            'Pallazza GOT', 
	            'manage_options', 
	            'pallazza-admin', 
	            array( $this, 'create_admin_page' )
	        );
	    }
	
	    /**
	     * Options page callback
	     */
	    public function create_admin_page()
	    {
	        // Set class property
	        $this->options = get_option( 'wolly_pal_general_options' );
	        ?>
	        <div class="wrap">
	            <h2>Impostazioni GOT</h2>           
	            <form method="post" action="options.php">
	            <?php
	                // This prints out all hidden setting fields
	                settings_fields( 'pallazza_option_group' );   
	                do_settings_sections( 'pallazza-admin' );
	                submit_button(); 
	            ?>
	            </form>
	        </div>
	        <?php
	    }
	
	    /**
	     * Register and add settings
	     */
	    public function page_init()
	    {        
	        register_setting(
	            'pallazza_option_group', // Option group
	            'wolly_pal_general_options', // Option name
	            array( $this, 'sanitize' ) // Sanitize
	        );
	
	        add_settings_section(
	            'setting_section_id', // ID
	            'My Custom Settings', // Title
	            array( $this, 'print_section_info' ), // Callback
	            'pallazza-admin' // Page
	        );  
	
	        add_settings_field(
	            'id_number', // ID
	            'ID Number', // Title 
	            array( $this, 'id_number_callback' ), // Callback
	            'pallazza-admin', // Page
	            'setting_section_id' // Section           
	        );      
	
	        add_settings_field(
	            'title', 
	            'Title', 
	            array( $this, 'title_callback' ), 
	            'pallazza-admin', 
	            'setting_section_id'
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
	        if( isset( $input['id_number'] ) )
	            $new_input['id_number'] = absint( $input['id_number'] );
	
	        if( isset( $input['title'] ) )
	            $new_input['title'] = sanitize_text_field( $input['title'] );
	
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
	    public function id_number_callback()
	    {
	        printf(
	            '<input type="text" id="id_number" name="wolly_pal_general_options[id_number]" value="%s" />',
	            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
	        );
	    }
	
	    /** 
	     * Get the settings option array and print one of its values
	     */
	    public function title_callback()
	    {
	        printf(
	            '<input type="text" id="title" name="wolly_pal_general_options[title]" value="%s" />',
	            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
	        );
	    }
	}
	
	if( is_admin() )
	    $Wolly_Pal_Options = new Wolly_Pal_Options();