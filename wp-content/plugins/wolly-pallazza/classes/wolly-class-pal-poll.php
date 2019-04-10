<?php
	
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
	}


class Wolly_Pal_Poll{
	
	/**
	 * user_id
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $user_id = '';
	
	/**
	 * ordered_characters
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $ordered_characters = '';
	
	/**
	 * now_status
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $now_status ='';
	
	/**
	 * user_reply
	 * 
	 * (default value: '')
	 * 
	 * @var string
	 * @access public
	 */
	var $user_reply = '';
	
	public function __construct(){
		
		/**
		 * this-
		 * 
		 * @var mixed
		 * @access public
		 */
		$this->user_id 				= get_current_user_id(  );
		
		/**
		 * this-
		 * 
		 * @var mixed
		 * @access public
		 */
		$this->ordered_characters 	= $this->get_characters();
		$this->now_status 			= $this->get_now_status();
		$this->user_reply 			= $this->users_answers();
		
		add_shortcode( 'pal_poll', array( $this,  'pall_poll' ) );
		add_shortcode( 'pal-single-reply', array( $this,  'show_single_reply' ) );		
		
		
	}
	
	public function pall_poll(){
		$html = '';
		
		if ( is_user_logged_in(  ) ){
			 
			if ( TRUE == WPIT_WOLPAL_POLL_OPEN ){
			if ( isset ( $_POST['save_got'] ) ){
				
					
				$this->save_got();
				
				$this->user_reply = $this->users_answers();
			}
			
			
		
		$html .= '<h3>Raggiungi le altre ' . wolly_pal()->answers->count_user_reply() . ' persone che hanno giocato</h3>';	
		$html .= $this->got_form();
		
		}
		
		if ( FALSE == WPIT_WOLPAL_POLL_OPEN ){
			
			$user_id = get_current_user_id();
			
			$html .= '<h3>Le votazioni sono chiuse, ecco le tue risposte definitive.</h3>';
			$html .= $this->show_single_reply( $user_id );
		}
		
		} else {
			
			$html = 'Per votare devi essere registrato e loggato';
		}
		return $html;
	}
	
	public function show_single_reply( $user_id = FALSE ){
		
		$html = '';
		$total_points = 0;
		if ( is_user_logged_in() ){
			
			$user = '';
			
			if ( isset( $_GET['user'] ) && is_numeric( $_GET['user'] ) ){
				
				$user = $_GET['user'];
				
				}
			if ( is_numeric( $user_id ) ){
				 $user = $user_id;
				 }
				 
			if ( is_numeric( $user ) ){
				
				$this->user_id = $user;
				$this->user_reply = $this->users_answers();
				
				$characters = $this->ordered_characters;
				$users_answers = $this->user_reply;
					
				
				$html .= '<table>';
			
			$html .= '<tr>';
				
				$html .= '<th>Domanda</th>';
				$html .= '<th>Risposta</th>';
				$html .= '<th>Stato attuale</th>';
				$html .= '<th>Punti</th>';
			$html .= '</tr>';
			foreach ( $characters as $character ){
				 
				$status = ( isset( $this->now_status[$character->ID] ) ) ? $this->convert_status( $this->now_status[$character->ID] ): '';
				$selected = ( isset( $users_answers[$character->ID] ) ) ?  $users_answers[$character->ID]: '';
				
				$html .= '<tr>';
				
					$html .= '<td>' . $character->post_title . '</td>';
					$html .= '<td>' . $this->convert_status( $selected ) . '</td>';
					$html .= '<td>' . $status . '</td>';
					$html .= '<td>' . $this->get_point( $character->ID ) . '</td>';
				$html .= '</tr>';
				$total_points = $total_points + $this->get_point( $character->ID );
			}
			
			foreach ( $this-> extra_questions_array() as $key => $ea ){
				
				$status_q_a =  $this->status_q_a( $key );
				
				
				$selected = ( isset( $users_answers[$key] ) ) ? $users_answers[$key]: '';
				
								
				if ( 100000 == $key ){
					
					if ( 1 == $selected ){
						
						$user_reply = 'Sì';
						
					} elseif ( 0 == $selected ){
						
						$user_reply = 'No';
						
						} else {
							
							$user_reply = 'Nessuna risposta';
						}
					
					if ( 1 == $status_q_a ){
						
						$status_q_a = 'Sì';
						
						} elseif ( '0' == $status_q_a ){	
						
							$status_q_a = 'No';
						
						} else {
							
							$status_q_a = 'N/A';
						}
				}
				
				if ( 100001 == $key || 100002 == $key ){
					
					if ( is_numeric( $selected )  ){
					
						$user_reply = get_the_title( $selected );
						
					} elseif ( 'none' == $selected ){
					
						$user_reply = 'none';
					} else {
						$user_reply = '';
					
					}
					
					if ( is_numeric( $status_q_a )  ){
					
						$status_q_a = get_the_title( $status_q_a );
						
					} elseif ( 'none' == $status_q_a ){
					
						$status_q_a = 'Nessuno di questi';
						
					} elseif ( 'N/A' == $status_q_a ){
						
						$status_q_a = 'N/A';
					} else {
						$status_q_a = '';
					
					}

					
				
				}
				
				
				
				$html .= '<tr>';
				
					$html .= '<td>' . $ea['question'] . '</td>';
					$html .= '<td>' . $user_reply . '</td>';
					$html .= '<td>' . $status_q_a . '</td>';
					$html .= '<td>' . $this->get_point( $key ) . '</td>';
				$html .= '</tr>';
				$total_points = $total_points + $this->get_point( $key );
			}
			$html .= '<tr>';
				
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td>Punti</td>';
				$html .= '<td>' . $total_points . '</td>';
			$html .= '</tr>';
			$html .= '</table>';

				
				
				} else {
				
					$html = 'Ci hai provato!';
			}
			
			
			
			} else {
				
				$html = 'Devi essere registrato e loggato per accedere a questa pagina';
			}
		
		
		return $html;
	}
	
	private function get_characters(){
		
		$args = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'characters',
			'meta_key' 			=> 'pal_name',
			'orderby' 			=> 'pal_name',
			'order' 			=> 'ASC',
			'posts_per_page'   	=> -1,
			'suppress_filters'	 => true,
        );

		$characters = get_posts($args);
		
		return $characters;
	}
	
	 //[26] => Array
     //   (
     //       [meta_id] => 281
     //       [post_id] => 78
     //       [meta_key] => pal_status
     //       [meta_value] => A
     //   )

	
	private function got_form(){
		
		$characters = $this->ordered_characters;
		$html = '';
		
		if ( ! empty( $characters ) ){
			
			$total_points = 0;
			$html .= '<form name="got-form" method="post" action="">';
			$html .= '<table>';
			
			$html .= '<tr>';
				
				$html .= '<th>Domanda</th>';
				$html .= '<th>Risposta</th>';
				$html .= '<th>Stato attuale</th>';
				$html .= '<th>Punti</th>';
			$html .= '</tr>';
			foreach ( $characters as $character ){
				 
				$status = ( isset( $this->now_status[$character->ID] ) ) ? $this->convert_status( $this->now_status[$character->ID] ): '';
				
				$html .= '<tr>';
				
					$html .= '<td>' . $character->post_title . '</td>';
					$html .= '<td>' . $this->status_dropdown( $character->ID ) . '</td>';
					$html .= '<td>' . $status . '</td>';
					$html .= '<td>' . $this->get_point( $character->ID ) . '</td>';
				$html .= '</tr>';
				
				$total_points = $total_points + $this->get_point( $character->ID );
			}
			
			foreach ( $this-> extra_questions_array() as $key => $ea ){
				$status_q_a =  $this->status_q_a( $key );
				
				if ( is_numeric( $status_q_a ) ){
					
					$status_q_a = get_the_title( $status_q_a );
				}
				$html .= '<tr>';
				
					$html .= '<td>' . $ea['question'] . '</td>';
					$html .= '<td>' . $this->extra_dropdown( $key, $ea['type'] ) . '</td>';
					$html .= '<td>' . $status_q_a . '</td>';
					$html .= '<td>' . $this->get_point( $key ) . '</td>';
				$html .= '</tr>';
				$total_points = $total_points + $this->get_point( $key );
			}
			$html .= '<tr>';
				
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td>Punti</td>';
				$html .= '<td>' . $total_points . '</td>';
			$html .= '</tr>';
			$html .= '</table>';
			$html .=  wp_nonce_field( 'got_poll_action','got_poll_nonce_field' );
			$html .= '<input type="hidden" name="user_id" value="' . $this->user_id . '"/>';
			$html .= '<input type="submit" value="Salva" name="save_got"/>';
			$html .= '</form>';
		}
		
		
		return $html;
	}
	
	private function status_dropdown( $id_answer ){
		
		$users_answers = $this->users_answers();
	
		$selected = ( isset( $users_answers[$id_answer] ) ) ? $users_answers[$id_answer]: '';
		$dropdown = '';
		$dropdown .= '<select name="status[' . $id_answer . ']">';
		$dropdown .= '<option value="-1">Selezione lo stato</option>';
		$dropdown .= '<option value="A"' . selected( $selected, 'A', false) . '>Vivo</option>';
		$dropdown .= '<option value="D"' . selected( $selected, 'D', false) . '>Morto</option>';
		$dropdown .= '<option value="W"' . selected( $selected, 'W', false) . '>Morto e Whitewalker</option>';
		$dropdown .= '</select>';
		
		return $dropdown;
	}
	
	private function extra_dropdown( $id_answer, $type ){
		
		$users_answers = $this->users_answers();
	
		$selected = ( isset( $users_answers[$id_answer] ) ) ? $users_answers[$id_answer]: '';
		$dropdown = '';
		$dropdown .= '<select name="extra[' . $id_answer . ']">';
		
		if ( 'bool' == $type ){
			$dropdown .= '<option value="no">Scegli si o no</option>';
		
					
			$dropdown .= '<option value="1"' . selected( $selected, 1, false) . '>Sì</option>';
			$dropdown .= '<option value="0"' . selected( $selected, 0, false) . '>No</option>';
			
		
		
		$dropdown .= '</select>';
			
		} else {
			
		$dropdown .= '<option value="no">Selezione il personaggio</option>';
		$dropdown .= '<option value="none" ' . selected( $selected,'none', false) . '>Nessuno di questi</option>';
		
		foreach ( $this->ordered_characters as $oc ){
			
			$dropdown .= '<option value="' .  $oc->ID . '"' . selected( $selected, $oc->ID, false) . '>' . $oc->post_title . '</option>';
			
		}
		
		$dropdown .= '</select>';
		
		}
		
		return $dropdown;
	}
	
	private function save_got(){
				
		if ( empty( $_POST ) || !wp_verify_nonce( $_POST['got_poll_nonce_field'], 'got_poll_action' ) ){

			print 'Sorry, your nonce did not verify.';
			exit;

		}
		
		$valid_char_answer = array( 'A', 'D', 'W' );
		
		$char_answers = $_POST['status'];
		
		

		foreach ( $char_answers as $key => $ca ){
			
			$reply = array();	
			if ( in_array( $ca, $valid_char_answer) ){
				
				
			 
				$reply['user_id'] 		= $_POST['user_id'];
				$reply['question_id'] 	= $key;
				$reply['answer'] 		= $ca;
				
				$save_answers = wolly_pal()->answers->save_reply( $reply );
		 	}

		}
		
		$extra_answers = $_POST['extra'];
		
		$valid_extra_answers = array( '100000', '100001', '100002' );
		
		foreach ( $extra_answers as $key => $ea ){
			
			if( in_array( $key, $valid_extra_answers ) ){
				
				if ( is_numeric( $ea ) || 'none' == $ea ){
					
					$reply['user_id'] 		= $_POST['user_id'];
					$reply['question_id'] 	= $key;
					$reply['answer'] 		= $ea;
				
					$save_answers = wolly_pal()->answers->save_reply( $reply );

					
					
				}
			}
		}
		
		$author_obj = get_user_by( 'ID', $_POST['user_id'] );
		
		$username = $author_obj->user_login;
		//$to = 'wolly66@gmail.com';
		//$subject = 'Nuovo totomorti di GOT';
		//$body = 'Ciao Wolly, ' . $username . ' ha votato il totomorti di GOT';
		//$headers = array('Content-Type: text/html; charset=UTF-8');
 		//
		//wp_mail( $to, $subject, $body, $headers );
		
		$mail_voter = $author_obj->user_email;
		$to = $mail_voter;
		$subject = 'Hai votato il totomorti';
		$body = 'Ciao ' . $username . ' hai votato il totomorti di GOT.<br /><br /> Puoi modificare i tuoi voti fino al 13 aprile sul sito https://pallazza.it <br /><br />Wolly';
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $to, $subject, $body, $headers );
		
		
		wolly_pal()->ranking->create_ranking();
		
		$now = time();
				
		update_option( 'pal-ranking-date-time', $now );
		
		$username = $author_obj->user_login;
		$to = 'wolly66@gmail.com';
		$subject = 'Nuovo voto e la classifica Totomorti di GOT è stata ricreata';
		$body = 'Ciao Wolly, ' . $username . ' ha votato il totomorti di GOT e la classifica è stata ricreata';
		$headers = array('Content-Type: text/html; charset=UTF-8');
 
		wp_mail( $to, $subject, $body, $headers );
	}
	
	private function users_answers(){
		
		$users_answers = wolly_pal()->answers->get_user_answers( $this->user_id );
		
		$ordered_answers = array();
		
		if ( ! empty( $users_answers ) ){
			
			foreach ( $users_answers as $ua ){
				
				$ordered_answers[$ua['question_id']] = $ua['answer'];
			}
		}
		
		return $ordered_answers;
	}
	
	static function extra_questions_array(){
		
		$extra_questions = array(
			
			'100000'	=> array(
				
				'question' 	=> 'Daenerys è incinta?',
				'type'		=> 'bool',
				'status'	=> 'N/A',
				'point'		=> 1,
			),
			'100001'	=> array(
				'question' 	=> 'Chi ucciderà il Re della notte?',
				'type'		=> 'char',
				'status'	=> 'N/A',
				'point'		=> 2,
				
			),
			'100002'	=> array(
				'question' 	=> 'Chi siederà sul trono di spade, alla fine?',
				'type'		=> 'char',
				'status'	=> (int)56,
				'point'		=> 4,
				
			),
			
			
		);	
		
		return 	$extra_questions;
	}
	
	static function get_now_status(){
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'postmeta';
		
		$raw = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE meta_key = %s;", 'pal_status' ), ARRAY_A );
		
		$current_status = array();
		
		foreach ( $raw as $r ){
			
			$current_status[$r['post_id']] = $r['meta_value'];
		}
		
		return $current_status;
		
	}
	
	static function convert_status( $status ){
		
		switch ( $status ) {
				case 'A':
				    $status = 'Vivo';
				    break;
				case 'D':
				    $status = 'Morto';
				    break;
				case 'W':
				    $status = 'Whitewalker';
				    break;
				
				default:
				     $status = '';
		}
		
		return $status;
		
	}
	private function status_q_a( $char_id ) {
		
		$extra_questions = $this->extra_questions_array();
		
		return $extra_questions[$char_id]['status'];
		
	}
	private function get_point( $char_id ){
		
	
		
		$extra_questions = $this->extra_questions_array();
				
		$point = 0;
		
		if ( isset( $this->now_status[$char_id] ) && isset( $this->user_reply[$char_id] ) ){
			
			if ( 100000 == $char_id || 100001 == $char_id || 100002 == $char_id ){
			
		
				if ( $extra_questions[$char_id]['status'] == $this->user_reply[$char_id] ){
			
			
				$point = $extra_questions[$char_id]['point'];
				}
			} elseif ( $this->now_status[$char_id] == $this->user_reply[$char_id] ){
				
				$point = 1;
			}
		}
			
			
		
		
		return $point;
		
		
		
		
	}
}

