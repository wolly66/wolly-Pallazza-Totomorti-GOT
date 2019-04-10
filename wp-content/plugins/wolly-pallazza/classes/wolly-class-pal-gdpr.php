<?php
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly
		}

class Wolly_Pal_Gdpr {
	
	public function __construct(){
		
		add_filter(
			'wp_privacy_personal_data_exporters',
			array( $this, 'register_pal_exporter' ),
			10
		);
		
		add_filter(
			'wp_privacy_personal_data_erasers',
			array( $this, 'register_pal_eraser' ),
			10
		);
	}
	public function register_pal_exporter( $exporters ) {
		
		$exporters['wolly-pallazza'] = array(
			'exporter_friendly_name' => __( 'Esportazione voti totomorti' ),
			'callback' => array( $this, 'pal_exporter' ),
			);
		return $exporters;
		
	}
	public function pal_exporter( $email_address, $page = 1 ){
		
		$number = 100; // Limit us to avoid timing out
		$page = (int) $page;
 
		$export_items = array();
		
		$user = get_user_by( 'email', $email_address );
		
		$user_id = $user->ID;
		
		$user_replies = wolly_pal()->answers->get_user_answers( $user_id );
		
		$extra_questions = wolly_pal()->got_poll->extra_questions_array();
		
		if ( ! empty( $user_replies ) ){
			
			foreach ( $user_replies as $ua ){
				
				$question = '';
				$reply = '';
				
				if ( 100000 == $ua['question_id'] || 100001 == $ua['question_id'] || 100002 == $ua['question_id'] ){
					
					$question = $extra_questions[$ua['question_id']]['question'];
					
					if ( 'bool' == $extra_questions[$ua['question_id']]['type'] ){
						
						$reply = ( 0 == $ua['answer'] ) ? 'No': 'Sì';
						
					} else {
						if ( 100002 == $ua['question_id'] ){
							if ( 'none' == $ua['answer'] ){
								
								$reply = 'Nessuno di quelli attuali';
							} else {
								
								$reply = get_the_title(  $ua['answer'] );
							}
							
						} else {
							
							$reply = get_the_title(  $ua['answer'] );
						
						}
						
					}
				} else {
					
					$question = get_the_title( $ua['question_id'] );
					
					$reply = wolly_pal()->got_poll->convert_status( $ua['answer'] );
					
				}
				
				$group_id = 'Risposte totomorti';
				$group_label = __( 'Risposte' );
				$data = array(
					array(
					'name' => __( 'Domanda' ),
					'value' => $question,
					),
					array(
					'name' => __( 'Risposta' ),
					'value' => $reply,
					),

					
					);
					
				$export_items[] = array(
					'group_id' => $group_id,
					'group_label' => $group_label,
					'item_id' => $user_replies->ID,
					'data' => $data,
					);
			}
		}
		
		// Tell core if we have more comments to work on still
		$done = count( $user_replies ) < $number;
		return array(
			'data' => $export_items,
			'done' => $done,
			);
		
	}
	
	public function register_pal_eraser( $erasers ){
		
		$erasers['wolly-pallazza'] = array(
			'eraser_friendly_name' => __( 'Cancellazione voti totomorti' ),
			'callback' => array( $this, 'pal_erasers' ),
			);
		return $erasers;
		
		
	}
	
	public function pal_erasers( $email_address, $page = 1 ){
		
		$number = 100; // Limit us to avoid timing out
		$page = (int) $page;
 
		$items_removed = false;
		
		$user = get_user_by( 'email', $email_address );
		
		$user_id = $user->ID;
		
		$user_replies = wolly_pal()->answers->get_user_answers( $user_id );
		
		if ( ! empty( $user_replies ) ){
			
			foreach ( $user_replies as $ua ){
				
				wolly_pal()->answers->delete( $ua['ID'] );
				$items_removed = true;
				
			}
		}
		
		$done = count( $user_replies ) < $number; 
		
		wolly_pal()->ranking->create_ranking();
		
		wp_delete_user( $user_id );
		
		return array( 'items_removed' => $items_removed,
			'items_retained' => false, // always false in this example
			'messages' => array(), // no messages in this example
			'done' => $done,
			);
		
		
		
	}
	
	
}