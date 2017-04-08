<?php
add_action( 'admin_menu', 'jmbt_add_admin_menu' );
add_action( 'admin_init', 'jm_buddy_translate_options_init' );

add_filter( 'plugin_action_links_jm-buddy-translate/jm-buddy-translate.php', 'jm_buddy_translate_settings_link' );
/**
 * Settings link that appears on the plugins overview page
 * @param array $links
 * @return array
 */
function jm_buddy_translate_settings_link( $links ) {
	$links[] = '<a href="'. 
			get_admin_url( null, 'options-general.php?page=jm-buddy-translate' ) . '">' .
			esc_html__( 'Settings', 'jm-buddy-translate' ) . '</a>';
	return $links;
}


function jmbt_add_admin_menu(  ) { 
	add_options_page( 'JM Buddy Translate', 'BuddyTranslate', 'manage_options', 'jm-buddy-translate', 'jmbt_options_page' );
}


function jm_buddy_translate_options_init(  ) { 

	$section_group = 'jm_buddy_translate_options';
	$section_name = 'jm_buddy_translate_options';
	register_setting( $section_group, $section_name );

	$settings_section = 'jmbt_integration';
	$page = $section_group;
	add_settings_section(
		$settings_section, 
		__( 'Translate Integration Options', 'jm-buddy-translate' ),
		'jm_buddy_translate_options_section_callback', 
		$page
	);

	add_settings_field( 
		'menubar_translate', 
		__( 'Menu Bar', 'jm-buddy-translate' ), 
		'menubar_translate_render', 
		$section_group, 
		$settings_section,
		array(
			__( 'Integrate into the top menu bar.', 'jm-buddy-translate' )
    )
	);
	add_settings_field( 
		'buddypress_translate', 
		__( 'Buddy Press', 'jm-buddy-translate' ), 
		'buddypress_translate_render', 
		$section_group, 
		$settings_section,
		array(
			__( 'Add Translate button to BuddyPress messages and activities.', 'jm-buddy-translate' )
    )
	);

	add_settings_field( 
		'bbpress_translate', 
		__( 'bbPress', 'jm-buddy-translate' ), 
		'bbpress_translate_render', 
		$section_group, 
		$settings_section,
		array(
			__( 'Add Translate button to bbPress Forum posts.', 'jm-buddy-translate' )
    )
	);

}


function menubar_translate_render( $s ) {  render_checkbox('menubar_translate', $s); }
function buddypress_translate_render( $s ) { render_checkbox('buddypress_translate', $s); }
function bbpress_translate_render( $s ) { render_checkbox('bbpress_translate', $s); }


function render_checkbox($optionName, $s){
	$options = btranslate_getOptions();
	?>
	<input type="checkbox" name="jm_buddy_translate_options[<?php echo($optionName) ?>]" id="<?php echo($optionName) ?>" <?php 
	 	checked(isset($options[$optionName] ), true);
	?> value="1">
	<?php echo(implode(' ', $s));
}


function jm_buddy_translate_options_section_callback(  ) { 
	_e( 'Show translate button in the following places:', 'jm-buddy-translate' );
}

function btranslate_getOptions() {

		//Pull from WP options database table
		$options = get_option('jm_buddy_translate_options');

		if (!is_array($options)) {
			$options['menubar_translate'] = 1;
			$options['buddypress_translate'] = 1;
			$options['bbpress_translate'] = 1;
			$options['bpdocs_translate'] = 1;
			//update_option('jm_buddy_translate_options', $options);
		}
		return $options;
}

function jmbt_options_page(  ) { 
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';	
	?>
	<form action='options.php' method='post'>

		<h2>JM Buddy Translate</h2>
		<p><a target="_blank" href="https://github.com/Jon007/jm-buddy-translate/">JM-Buddy-Translate</a> <?php _e('is a translation helper tool from', 'jm-buddy-translate')?> <a target="_blank" href="https://jonmoblog.wordpress.com/">Jonathan Moore</a>.</p>
		
		<?php
		settings_fields( 'jm_buddy_translate_options' );
		do_settings_sections( 'jm_buddy_translate_options' );
		submit_button();
		?>

	</form>
<h2>Menu Bar usage</h2>
<p>Illustration shows sample usage along with recommended tool jsm-user-locale which provides current user a quick way to choose preferred language.</p>
<p>When you click the translate button, the currently text is sent to Google translate for translation to current user language.</p>
<img height="153px" width="500px" style="align:center" src="https://raw.githubusercontent.com/Jon007/jm-buddy-translate/master/assets/screenshot-1.png"/>
<h2>BuddyPress and bbPress usage</h2>
<p>The tool includes hooks to insert translate buttons along in the standard button areas for bbPress and BuddyPress messages and activities.</p>
<img style="align:center" height="265px" width="626px" src="https://raw.githubusercontent.com/Jon007/jm-buddy-translate/master/assets/screenshot-2.png"/>
<h2>Notes</h2>
<p>The tool is designed to work for logged-on users and will attempt to translate into the user's preferred language. To change target language the user can change their preferred Wordpress language or use a switching tool such as jsm-user-locale to do the same thing.</p>
<p>The order of preference for detecting text to translate is:
</p><ol>
<li>Selected text on the page.</li>
<li>Current item.</li>
</ol>
<p></p>
	<?php
}
?>