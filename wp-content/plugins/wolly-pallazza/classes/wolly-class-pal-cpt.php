<?php
	
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}


class Wolly_Pal_Cpt {
	
	public function __construct(){
		
		add_action( 'init', array( $this, 'characters_cpt' ), 0 );
				
		
		add_action( 'save_post', 	  array( $this, 'characters_data_save_meta_box' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'characters_data_register_meta_boxes' ) );
		
	}
	
	
	
	// Register Custom Post Type AUTHORS
	public function characters_cpt() {
	
		$labels = array(
			'name'                  => _x( 'Characters', 'Post Type General Name', 'wolly-consulenze-editoriali' ),
			'singular_name'         => _x( 'Character', 'Post Type Singular Name', 'wolly-consulenze-editoriali' ),
			'menu_name'             => __( 'Characters', 'wolly-consulenze-editoriali' ),
			'name_admin_bar'        => __( 'Characters', 'wolly-consulenze-editoriali' ),
			'archives'              => __( 'Item Archives', 'wolly-consulenze-editoriali' ),
			'attributes'            => __( 'Item Attributes', 'wolly-consulenze-editoriali' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wolly-consulenze-editoriali' ),
			'all_items'             => __( 'All Items', 'wolly-consulenze-editoriali' ),
			'add_new_item'          => __( 'Add New Item', 'wolly-consulenze-editoriali' ),
			'add_new'               => __( 'Add New', 'wolly-consulenze-editoriali' ),
			'new_item'              => __( 'New Item', 'wolly-consulenze-editoriali' ),
			'edit_item'             => __( 'Edit Item', 'wolly-consulenze-editoriali' ),
			'update_item'           => __( 'Update Item', 'wolly-consulenze-editoriali' ),
			'view_item'             => __( 'View Item', 'wolly-consulenze-editoriali' ),
			'view_items'            => __( 'View Items', 'wolly-consulenze-editoriali' ),
			'search_items'          => __( 'Search Item', 'wolly-consulenze-editoriali' ),
			'not_found'             => __( 'Not found', 'wolly-consulenze-editoriali' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wolly-consulenze-editoriali' ),
			'featured_image'        => __( 'Featured Image', 'wolly-consulenze-editoriali' ),
			'set_featured_image'    => __( 'Set featured image', 'wolly-consulenze-editoriali' ),
			'remove_featured_image' => __( 'Remove featured image', 'wolly-consulenze-editoriali' ),
			'use_featured_image'    => __( 'Use as featured image', 'wolly-consulenze-editoriali' ),
			'insert_into_item'      => __( 'Insert into item', 'wolly-consulenze-editoriali' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wolly-consulenze-editoriali' ),
			'items_list'            => __( 'Items list', 'wolly-consulenze-editoriali' ),
			'items_list_navigation' => __( 'Items list navigation', 'wolly-consulenze-editoriali' ),
			'filter_items_list'     => __( 'Filter items list', 'wolly-consulenze-editoriali' ),
		);
		$args = array(
			'label'                 => __( 'Character', 'wolly-consulenze-editoriali' ),
			'description'           => __( 'Characters post type', 'wolly-consulenze-editoriali' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-admin-users',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => false,
		);
		register_post_type( 'characters', $args );
	
	}
	
		
	
	
	 
/**
 * Register meta box(es).
 */
function characters_data_register_meta_boxes() {
    add_meta_box( 'meta-box-characters-id', __( 'Answer', 'wolly-consulenze-editoriali' ), array( $this, 'characters_data_display_callback' ), 'characters' );
}


 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function characters_data_display_callback( $post ) {
	
	
    // Display code/markup goes here. Don't forget to include nonces!
    	
		$name	= ( ! empty( get_post_meta( $post->ID, 'pal_name', true ) ) ) ? get_post_meta( $post->ID, 'pal_name', true ): '';
		$points = ( ! empty( get_post_meta( $post->ID, 'pal_points', true ) ) ) ? get_post_meta( $post->ID, 'pal_points', true ): '';
		$status = ( ! empty( get_post_meta( $post->ID, 'pal_status', true ) ) ) ? get_post_meta( $post->ID, 'pal_status', true ): '';
				

		wp_nonce_field( 'characters_action', 'characters_nonce' );
	    
	    
	   	    
	    ?>
	    
	   <table class="form-table">

	   	<tbody>

	   		<tr>
	   			<th scope="row">
	   				<label for="name">Cognome e nome</label>
	   			</th>
	   			<td>
	   				<input type="text" id="name" name="name" value="<?php echo $name; ?>" class="large-text">
	   			</td>
	   		</tr>
	   		
	   
	   		<tr>
	   			<th scope="row">
	   				<label for="status">Status</label>
	   			</th>
	   			<td>
		   			<select id="status" name="status">
			   			
			   			<option value="-1">Selezione lo stato attuale</option>
			   			<option value="A" <?php selected( $status, 'A', true); ?>>Vivo</option>
			   			<option value="D" <?php selected( $status, 'D', true); ?>>Morto</option>
			   			<option value="W" <?php selected( $status, 'W', true); ?>>Morto e Whitewalker</option>
	   				
	   				
		   			</select>
	   			</td>
	   		</tr>

		</tbody>
	</table>
			    
		    
		    
		<?php
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function characters_data_save_meta_box( $post_id ) {
    // Save logic goes here. Don't forget to include nonce checks!
    	global $post;
		
		if ( $post->post_type != 'characters' ){
        	return;
    	}
    
        global $wpdb;

		

		// Check if our nonce is set
		if ( ! isset( $_POST['characters_nonce'] ) ) {
		  return $post_id;
		}
		
		// Verify that the nonce is valid
		if ( ! wp_verify_nonce( $_POST['characters_nonce'], 'characters_action' ) ) {
		  return $post_id;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

									
		if ( isset( $_POST['name'] ) && ! empty( $_POST['name'] ) ){
			
			update_post_meta( $post_id, 'pal_name', sanitize_text_field( $_POST['name'] ) );
			
			} else {
			
				delete_post_meta( $post_id, 'pal_name' );
		}			
				
		
		if ( isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ){
			
			update_post_meta( $post_id, 'pal_status', sanitize_text_field( $_POST['status'] ) );
			
			} else {
			
				delete_post_meta( $post_id, 'pal_status' );
		}
		
		
		
}




	
}
