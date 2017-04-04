<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Illegal Entry');
}

//============================== BuddyTranslate options ========================//
class jm_buddy_translate_plugin_options {

	//Defaults
	public static function btranslate_getOptions() {

		//Pull from WP options database table
		$options = get_option('jm_buddy_translate_options');

		if (!is_array($options)) {
			$options['menubar_translate'] = true;
			$options['buddypress_translate'] = true;
			$options['bbpress_translate'] = true;
			$options['bpdocs_translate'] = true;
			update_option('jm_buddy_translate_options', $options);
		}
		return $options;
	}


	public static function update() {

		if(isset($_POST['jm_buddy_translate_save'])) {

			$options = jm_buddy_translate_plugin_options::btranslate_getOptions();

			if (isset($_POST['bpdocs_translate'])) {
				$options['bpdocs_translate'] = (bool)true;
			} else {
				$options['bpdocs_translate'] = (bool)false;
			}

			if (isset($_POST['menubar_translate'])) {
				$options['menubar_translate'] = (bool)true;
			} else {
				$options['menubar_translate'] = (bool)false;
			}

			if (isset($_POST['buddypress_translate'])) {
				$options['buddypress_translate'] = (bool)true;
			} else {
				$options['buddypress_translate'] = (bool)false;
			}

			if (isset($_POST['bbpress_translate'])) {
				$options['bbpress_translate'] = (bool)true;
			} else {
				$options['bbpress_translate'] = (bool)false;
			}

			update_option('jm_buddy_translate_options', $options);

		} else {
			jm_buddy_translate_plugin_options::btranslate_getOptions();
		}

		add_submenu_page( 'options-general.php', 'BuddyTranslate options', 'BuddyTranslate', 'edit_theme_options', basename(__FILE__), array('jm_buddy_translate_plugin_options', 'display'));
	}


	public static function display() {

		$options = jm_buddy_translate_plugin_options::btranslate_getOptions();
		?>

		<div id="jm_buddy_translate_admin" class="wrap">

			<h2>JM-Buddy-Translate Options</h2>

			<p>JM-Buddy-Translate is a translation helper tool from Jonathan Moore.  <a href="https://jonmoblog.wordpress.com/">JM-Buddy-Translate</a></p>

			<form method="post" action="#" enctype="multipart/form-data">

				<div class="ps_border" ></div>
				
				<p><?php _e("Show translate button in the following places: ") ?> </p>
				
				<p><label><input name="menubar_translate" type="checkbox" value="checkbox" <?php 
				if($options['menubar_translate']) echo "checked='checked'"; ?> />  Menu Bar</label></p>

				<p><label><input name="buddypress_translate" type="checkbox" value="checkbox" <?php 
				if($options['buddypress_translate']) echo "checked='checked'"; ?> /> BuddyPress</label></p>
				
				<p style="display:none;"><label><input name="bpdocs_translate" type="checkbox" value="checkbox" <?php 
				if($options['bpdocs_translate']) echo "checked='checked'"; ?> /> BuddyPress Docs</label></p>

				<p><label><input name="bbpress_translate" type="checkbox" value="checkbox" <?php 
				if($options['bbpress_translate']) echo "checked='checked'"; ?> /> bbPress</label></p>


				<p><input class="button-primary" type="submit" name="jm_buddy_translate_save" value="Save Changes" /></p>

			</form>

      <h2>Menu Bar usage</h2>
			<p>Illustration shows sample usage along with recommended tool jsm-user-locale which provides current user a quick way to choose preferred language.</p>
			<p>When you click the translate button, the currently text is sent to Google translate for translation to current user language.</p>
			<img height="153px" width="500px" style="align:center" src="<?php echo(plugin_dir_url(__FILE__))?>assets/screenshot-1.png"/>
			
      <h2>BuddyPress and bbPress usage</h2>
			<p>The tool includes hooks to insert translate buttons along in the standard button areas for bbPress and BuddyPress messages and activities.</p>
			<img style="align:center" height="265px" width="626px"  src="<?php echo(plugin_dir_url(__FILE__))?>assets/screenshot-2.png"/>

			
      <h2>Notes</h2>
			<p>The tool is designed to work for logged-on users and will attempt to translate into the user's preferred language.  To change target language the user can change their preferred Wordpress language or use a switching tool such as jsm-user-locale to do the same thing.</p>
			<p>The order of preference for detecting text to translate is:
			<ol>
				<li>Selected text on the page.</li>
				<li>Current item.</li>
			</ol>				
				
				
			</p>
		</div>

		<?php
	}
} //options class

// register functions
add_action('admin_menu', array('jm_buddy_translate_plugin_options', 'update'));
/**
 * Settings link that appears on the plugins overview page
 * @param array $links
 * @return array
 */
function bTranslate_settings_link( $links ) {
	$links[] = '<a href="'. get_admin_url( null, 'options-general.php?page=btranslate-settings.php' ) . '">' . esc_html__( 'Settings', 'jm-buddy-translate' ) . '</a>';
	return $links;
}
add_filter( 'plugin_action_links_jm-buddy-translate/jm-buddy-translate.php', 'bTranslate_settings_link' );


