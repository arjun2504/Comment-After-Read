<?php

class CommentAfterReadAdmin {

	public function __construct() {
		add_action('admin_menu', array( $this, 'wpcar_add_menu' ) );
	}

	public function wpcar_add_menu() {
		add_options_page(
			'Comment After Read Settings', 
			'Comment After Read', 
			'manage_options', 
			'comment-after-read-settings', 
			array($this, 'settings_page') 
			);

		add_action('admin_init', array($this, 'display_options') );
	}

	public function settings_page() {
		?>
		<div class="wrap">
			<h1>Comment After Read</h1>
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