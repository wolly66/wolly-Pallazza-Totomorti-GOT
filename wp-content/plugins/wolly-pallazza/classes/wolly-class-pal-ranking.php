<?php
	if ( ! defined( 'ABSPATH' ) ) {
	    exit; // Exit if accessed directly
		}
	
class Wolly_Pal_Ranking{
	
	var $ranking_options;
	var $ranking_option_name;
	var $extra_answers;
	var $current_status;
	
	public function __construct(){
		
		$this->ranking_option_name = 'pal-ranking';
		$this->ranking_options = get_option( $this->ranking_option_name );
		
		add_shortcode( 'pal-ranking', array( $this, 'show_ranking' ) );
		
	}
	
	
	public function show_ranking(){
		$html = '';
		
		if ( current_user_can( 'manage_options' ) ){
			if ( isset( $_POST['do_ranking'] ) ){
				
				$users_answers = $this->create_ranking();
				$now = time();
				
				update_option( 'pal-ranking-date-time', $now );
				
			}
			
			if ( isset( $_POST['send_email'] ) ){
				
				$email = $this->send_email();
				
				
			}

			$ranking_date_time = get_option( 'pal-ranking-date-time' );
			
			
			$html .= '<form name="create_ranking" method="post" action="">';
			$html .= '<input type="submit" name="do_ranking" value="Crea la Classifica">';
			$html .= '</form>';
			
			$html .= '<form name="email" method="post" action="">';
			$html .= '<input type="submit" name="send_email" value="Invia email">';
			$html .= '</form>';
		}
		$ranking_date_time = get_option( 'pal-ranking-date-time' );
		date_default_timezone_set('Europe/Rome');
		$html .= '<h2>Classifica aggiornata a: ' . date( 'd-m-Y H:i', $ranking_date_time ) . '</h2>';
		$html .= '<table>';
			
			$html .= '<tr>';
				
				$html .= '<th>Posizione</th>';
				$html .= '<th>Nickname</th>';
				$html .= '<th>Punti</th>';
				
			$html .= '</tr>';
		$rank = 0;
		$old_point = '';
		$diff = 0;
		
		foreach ( $this->ranking_options as $user_id => $points ){
			
			if ( $old_point != $points ){
				
				$rank ++;
				$rank = $rank + $diff;
				$diff = 0;
				
				
			} else {
				
				$diff ++;
				
			}
			
			$userdata = get_userdata( $user_id );
			$nickname = $userdata->user_login;
			
			if ( current_user_can( 'manage_options' ) ){
				
				$nickname = '<a href="https://pallazza.it/risposte-utente/?user=' . $user_id . '" >' . $userdata->user_login . '</a>';
			}
			$html .= '<tr>';
				$html .= '<td>' . $rank . '</td>';
				$html .= '<td>' . $nickname . '</td>';
				$html .= '<td>' . $points . '</td>';
			$html .= '</tr>';
			
			$old_point = $points;
			
			
			
		}
		
		$html .= '</table>';
		return $html;
	}
	
	public function create_ranking(){
		
		/**
		 * users_answers
		 * 
		 * (default value: wolly_pal()->answers->get_all_reply())
		 * 
		 * @var mixed
		 * @access public
		 */
		$users_answers 	= wolly_pal()->answers->get_all_reply();
		
		/**
		 * current_status
		 * 
		 * (default value: wolly_pal()->got_poll->get_now_status())
		 * 
		 * @var mixed
		 * @access public
		 */
		$this->current_status = wolly_pal()->got_poll->get_now_status();
		
		/**
		 * extra_answers
		 * 
		 * (default value: wolly_pal()->got_poll->extra_questions_array())
		 * 
		 * @var array
		 * @access public
		 */
		$this->extra_answers 	= wolly_pal()->got_poll->extra_questions_array();
		
		
		
		
		$raw_ranking = array();
		
		foreach ( $users_answers as $user_id ){
			$user_points = 0;
			$all_answers = $this->users_answers( $user_id );
			
			
			
			if ( ! empty( $all_answers ) ){
				
				foreach ( $all_answers as $char_id => $reply ){
					
					$point = $this->get_point( $char_id, $reply );
										
					$user_points = $user_points + $point;
					
				}
			}
			
			$raw_ranking[$user_id] = $user_points;
			
		}
		 arsort($raw_ranking);
		
		
		update_option( $this->ranking_option_name, $raw_ranking, true );
		
		$this->ranking_options = get_option( $this->ranking_option_name );
				
		
	}
	
	private function users_answers( $user_id ){
		
		$users_answers = wolly_pal()->answers->get_user_answers( $user_id );
		
		$ordered_answers = array();
		
		if ( ! empty( $users_answers ) ){
			
			foreach ( $users_answers as $ua ){
				
				$ordered_answers[$ua['question_id']] = $ua['answer'];
			}
		}
		
		return $ordered_answers;
	}
	
	private function get_point( $char_id, $reply ){
		
		$extra_questions = $this->extra_answers;
		
		$point = 0;
		
		if ( isset( $this->current_status[$char_id] ) ){
			
			if ( 100000 == $char_id || 100001 == $char_id || 100002 == $char_id ){
				
				if ( $extra_questions[$char_id]['status'] == $reply ){
				
					$point = $extra_questions[$char_id]['point'];
					
				}
			} else {
				
				if ( $this->current_status[$char_id] == $reply ){
				
					$point = 1;
				}
				
			}
		
			
			
			
			
			
		}
		
		return $point;
		
		
		
		
	}
	
	private function send_email(){
		
		
		$all_users = get_users();
		$headers = array();
		$headers[] = 'Content-Type: text/html'; 
		$headers[] = 'charset=UTF-8';

		foreach ( $all_users as $user ){
			
			$headers[] = 'Bcc: ' . esc_html( $user->user_email );
		}
		 
		
		$to = 'wolly66@gmail.com';
		$subject = '3 GIORNI ALLA CHIUSURA - Ricordati che devi votare il totomorti di GOT';
		$body = 'Ciao, qui è Wolly. <br /><br />Volevo ricordarti che il 14 aprile si avvicina e verranno chiuse le votazioni per il totomorti di GOT. <br /><br />Ricevi questa mail perché o hai già votato, o ti sei iscritto e non hai ancora votato, o hai votato solo parzialmente.<br /><br /> Se vuoi modificare il tuo voto, oppure vuoi completarlo o semplicemente devi ancora votare, vai a https://pallazza.it e fai il tuo dovere! <br /><br />Se non vuoi più giocare e/o vuoi cancellarti dal sito, nel footer, in ogni pagina, trovi il form per cancellarti.';
	
 
		wp_mail( $to, $subject, $body, $headers );

		
				
	}

}