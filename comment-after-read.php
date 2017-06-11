<?php
/**
* Plugin Name: Comment After Read
* Description: Lets readers to comment on your posts only after a certain amount of time. Plugin from <a href="https://www.cryptlife.com">CryptLife</a> and <a href="http://invuil.com">Invuil</a>.
* Author: Arjun, Avinash
* Version: 1.0
* License: GPL2
*/
include("comment-after-read-admin.php");

class CommmentAfterRead {

	private $is_single = false;
	private $defaultSettings = [];
	private $word_count = 0;
	public $options;

	public function __construct() {
		$this->defaultSettings['wpm'] = 275;

		$this->options = new CommentAfterReadAdmin();

		register_activation_hook( __FILE__, array($this, 'set_defaults') );
		
		add_filter('the_content', array($this, 'is_single_post') );

		wp_register_script( 'wpcar', plugins_url('/wpcar.js', __FILE__), array( 'jquery' ), 0.1, false);
		wp_enqueue_script('wpcar');

		add_filter( 'comment_form_defaults', array($this,'get_timers') );
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
		
		$reading_time = ( $option->get_auto_time_limit_status() != '1' && $auto_reading_time > $option->get_max_time_limit() ) ? $option->get_max_time_limit() : $auto_reading_time;

		return $reading_time;
	}

	public function is_single_post( $content ) {
		$this->is_single = is_single() ? true : false;
		$this->word_count = str_word_count( strip_tags( $content ) );

		return $content;
	}

	private function get_wpm() {
		return $this->defaultSettings['wpm'];
	}

}

new CommmentAfterRead();