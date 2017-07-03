<?php
/**
* Plugin Name: CommentSafe
* Description: Stop spam comments & bot comments. It improves your bounce rate and ultimately helps in better SEO. Subsequent versions will be more on SEO. Developers: Avinash Mishra & Arjun R R. You can donate us for appreciation.
* Author: Inviul & CryptLife
* Version: 1.1
* License: GPL2
*/
include("comment-safe-admin.php");

class CommmentSafe {

	private $is_single = false;
	private $defaultSettings = [];
	private $word_count = 0;
	public $options;
	private $post_id;

	public function __construct() {
		$this->defaultSettings['wpm'] = 275;

		$this->options = new CommentSafeAdmin();

		register_activation_hook( __FILE__, array($this, 'set_defaults') );
		
		add_filter('the_content', array($this, 'is_single_post') );

		wp_register_script( 'wpcar', plugins_url('/wpcar.js', __FILE__), array( 'jquery' ), 0.1, false);
		wp_enqueue_script('wpcar');

		add_filter( 'comment_form_defaults', array($this,'get_timers') );

		add_action( 'comment_form_logged_in_after', array($this, 'init_timer_client_side') );
		add_action( 'comment_form_after_fields', array($this, 'init_timer_client_side') );
		add_filter( 'preprocess_comment', array($this, 'verify_timer_after_comment') );
	}


	public function init_timer_client_side() {
		echo "<input type='hidden' name='_wpcar_init_timer' value='".base64_encode($this->get_reading_time()."|".time())."'>";
	}

	public function verify_timer_after_comment( $commentdata ) {
		$timer = explode("|", base64_decode($_POST['_wpcar_init_timer']));
		$time_to_comment = $timer[0];
		$page_loaded_at = $timer[1];
		if((time() - $page_loaded_at) < $time_to_comment) {
			wp_die( __( 'Error: Please read the article before you comment.' ) );
		}
		return $commentdata;
	}

	public function get_timers( $args ) {

		if( $this->is_single ) {
			$old_name = $args['label_submit'];
			$args['label_submit'] = "Please wait for ".$this->get_reading_time()." seconds to comment";
			echo "<span id='wpcar_old_name_restore' data-name='".$old_name."'></span>";
		}

		return $args;
	}

	public function set_defaults() {
		
		if( !get_option('_wpcar_autotime_limit') ) {
			
			update_option('_wpcar_autotime_limit', '1');	//setting auto time -- based on content length
			update_option('_wpcar_maxtime_limit', 60);

		}

	}

	private function get_reading_time() {
		$auto_reading_time = ( floor( $this->word_count / $this->get_wpm() ) * 60 );
		
		$option = $this->options;
		
		$post_meta_disable_timer = get_post_meta( $this->post_id, '_wpcar_autotime_limit', true);
		$post_meta_max_limit = get_post_meta( $this->post_id, '_wpcar_maxtime_limit', true);

		if(empty($post_meta_max_limit) && empty($post_meta_disable_timer)) {
			$reading_time = ( $option->get_auto_time_limit_status() != '1' && $auto_reading_time > $option->get_max_time_limit() ) ? $option->get_max_time_limit() : $auto_reading_time;
		}
		else {
			//overriding global settings
			$reading_time = ( $post_meta_disable_timer != '1' && $auto_reading_time > $post_meta_max_limit ) ? $post_meta_max_limit : $auto_reading_time;
		}

		return $reading_time;
	}

	public function is_single_post( $content ) {

		global $post;

		$this->post_id = $post->ID;

		$this->is_single = is_single() ? true : false;
		$this->word_count = str_word_count( strip_tags( $content ) );

		return $content;
	}

	private function get_wpm() {
		return $this->defaultSettings['wpm'];
	}

}

new CommmentSafe();