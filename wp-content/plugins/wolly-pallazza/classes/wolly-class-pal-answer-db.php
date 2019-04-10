<?php
	
	if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}

	
	class Wolly_Pal_Answers_Db extends Wolly_Pal_Db {

	/**
	 * The table name for this special DB table
	 *
	 * @access private
	 * @since  1.0
	 * @var    string
	 */
	public $table_name;

	/**
	 * The primary key for the special table
	 *
	 * @access private
	 * @since  1.0
	 * @var    string
	 */
	public $primary_key;

	/**
	 * The version number of the table structure
	 *
	 * @access private
	 * @since  1.0
	 * @var    string
	 */
	public $version;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {
	
		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'answers';
		$this->primary_key = 'ID';
		$this->version     = '1.0.6';

		// Check for update table schema
		add_action( 'init', array( $this, 'update_check' ) );
		// Register the table
		//add_action( 'plugins_loaded', array( $this, 'register_table' ) );

	}
	
	/**
	 * Return the option name with the DB version for the timeslot tables
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string the name of the option vith version db table
	 */
	public function get_option_name_table_ver() {

		return get_option( 'wolly_pal_' . $this->table_name . '_db_ver', 0 );

	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_columns() {
		return array(
			'ID'      	 	=> '%d',
			'user_id' 		=> '%d',
			'question_id'   => '%d',
			'answer'        => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_column_defaults() {
		return array(
			'ID'            => '0',
			'user_id' 		=> '0',
			'question_id'   => '0',
			'answer'        => '',
		);
	}

	/**
	 * update_UTILITY_check function.
	 *
	 * @access public
	 * @return void
	 */
	public function update_check() {
		
		// Do checks only in backend
		if ( is_admin() ) {

			// Check if DB is UPDATED
			if ( version_compare( $this->get_option_name_table_ver(), $this->version ) != 0 ) {

				$this->create_db_table();

			}
		}
	}

	/**
	 * Registers the table with $wpdb to make use easy.
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	public function register_table() {

		global $wpdb;

		//register the new table with the wpdb object
		if ( ! isset( $wpdb->answers ) ) {
			$wpdb->answers = $this->table_name;
			//add the shortcut so you can use $wpdb->stats
			$wpdb->tables[] = str_replace( $wpdb->prefix, '', $this->table_name );
		}
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function create_db_table() {

		global $wpdb;

		/**
		 * The database character collate.
		 */
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(64) NOT NULL,
		question_id bigint(20) unsigned NOT NULL,
		answer varchar(5),
		PRIMARY KEY  (ID),
		UNIQUE KEY user_question (user_id,question_id)
		) $charset_collate";

		dbDelta( $sql );
		

		update_option( 'wolly_pal_' . $this->table_name . '_db_ver', $this->version );
	}

	public function get_user_answers( $user_id ){
		
		global $wpdb;

		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE user_id = %s;", $user_id ), ARRAY_A );
		
		
		
	}
	
	public function save_reply( $reply = array() ){
		
				
		
		global $wpdb;
		
		$wpdb->replace( 
					$this->table_name , 
					array( 
						'user_id' 		=> $reply['user_id'],
						'question_id'	=> $reply['question_id'], 
						'answer' 		=> $reply['answer'] 
						), 
					array( 
                '%d',
				'%d', 
				'%s',
				
				) 
				);
		
				
	}
	
	public function count_user_reply(){
		global $wpdb;
		
		$count = $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM {$this->table_name}" );
		
		return $count;
		

	}
	
	public function get_all_reply(){
		
		global $wpdb;
		
		$all = $wpdb->get_results( "SELECT user_id FROM {$this->table_name} GROUP BY user_id" );
		
		$user_ids = array();
		foreach ( $all as $id ){
			
			$user_ids[] = $id->user_id;
		}
				
		return $user_ids;
				
	}
	

}