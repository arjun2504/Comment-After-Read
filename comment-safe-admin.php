<?php

class CommentSafeAdmin {

	public function __construct() {
		add_action('admin_menu', array( $this, 'wpcar_add_menu' ) );
	}

	public function wpcar_add_menu() {
		add_options_page(
			'CommentSafe Settings', 
			'CommentSafe', 
			'manage_options', 
			'comment-safe-settings', 
			array($this, 'settings_page') 
			);

		add_action('admin_init', array($this, 'display_options') );
		add_action('add_meta_boxes', array($this, 'add_post_specific_box'), 2 );
		add_action('save_post', array($this, 'save_post_specific_settings'));
	}

	public function add_post_specific_box() {
		
		add_meta_box(
            '_wpcar_box',
            __( 'CommentSafe', '_wpcar_box' ),
            array($this, 'post_specific_box')
        );
	}

	public function post_specific_box( $post ) {
		wp_nonce_field( 'wpcar_nonce', 'wpcar_nonce' );

		$timer_status = get_post_meta( $post->ID, '_wpcar_autotime_limit', true);
		$timer_limit = get_post_meta( $post->ID, '_wpcar_maxtime_limit', true);

		?>
		<br/>
		<input type="checkbox" onclick="wpcar_disableTimer()" name="_wpcar_autotime_limit" id="_wpcar_autotime_limit" <?php checked(1, get_post_meta($post->ID, '_wpcar_autotime_limit'), true); ?> value="1" />
		<label for="_wpcar_autotime_limit"><b>Disable Timer Limit</b></label>
		<br/><br/>
		<label for="_wpcar_maxtime_limit"><b>Max. Limit (seconds)</b></label>
		<br/>
		<input type="number" style="width: 100%" name="_wpcar_maxtime_limit" id="_wpcar_maxtime_limit" value="<?php echo get_post_meta($post->ID, '_wpcar_maxtime_limit', true); ?>" />
		
		<?php
	}

	public function save_post_specific_settings( $post_id ) {

		if( ! isset($_POST['wpcar_nonce']) ) return;

		if( ! wp_verify_nonce($_POST['wpcar_nonce'], 'wpcar_nonce') ) return;

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

		if( isset($_POST['post_type']) ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		}

		$disable_timer = $_POST['_wpcar_autotime_limit'];
		$max_limit = sanitize_text_field( $_POST['_wpcar_maxtime_limit'] );

		update_post_meta( $post_id, '_wpcar_autotime_limit', $disable_timer);
		update_post_meta( $post_id, '_wpcar_maxtime_limit', $max_limit);

	}

	public function settings_page() {
		?>
		<div class="wrap">
			<h1>CommentSafe</h1>
			<form method="post" action="options.php">
			<?php
				settings_fields("wpcar_section");
	            do_settings_sections("wpcar-options");      
	            submit_button(); 
			?>
			</form>
		</div>
		<?php
	}

	public function get_max_timer_field() {
		?>
		<input type="number" name="_wpcar_maxtime_limit" id="_wpcar_maxtime_limit" value="<?php echo get_option('_wpcar_maxtime_limit'); ?>" required />
		<?php
	}

	public function get_autotimer_field() {
		?>
		<input type="checkbox" onclick="wpcar_disableTimer()" name="_wpcar_autotime_limit" id="_wpcar_autotime_limit" <?php checked(1, get_option('_wpcar_autotime_limit'), true); ?> value="1" />
		<?php
	}

	public function display_options() {
		add_settings_section("wpcar_section", "Settings", null, "wpcar-options");

		add_settings_field("_wpcar_autotime_limit", "Disable Timer Limit", array($this, "get_autotimer_field"), "wpcar-options", "wpcar_section");
		add_settings_field("_wpcar_maxtime_limit", "Max. Limit (seconds)", array($this, "get_max_timer_field"), "wpcar-options", "wpcar_section");

		register_setting("wpcar_section", "_wpcar_autotime_limit");
		register_setting("wpcar_section", "_wpcar_maxtime_limit");
	}

	public function enable_auto_time_limit() {
		update_option('_wpcar_autotime_limit', '1');
	}

	public function get_auto_time_limit_status() {
		return get_option('_wpcar_autotime_limit');
	}

	public function disable_auto_time_limit() {
		update_option('_wpcar_autotime_limit', '0');
	}

	public function set_max_time_limit($time) {
		update_option('_wpcar_maxtime_limit', $time);
	}

	public function get_max_time_limit() {
		return get_option('_wpcar_maxtime_limit');
	}
	
}