<?php
/*
 * Plugin Name: JM Buddy Translate
 * Text Domain: jm-buddy-translate
 * Domain Path: /languages
 * Plugin URI: https://github.com/Jon007jm-buddy-translate/
 * Assets URI: https://github.com/Jon007jm-buddy-translate/assets/
 * Author: Jonathan Moore
 * Author URI: https://jonmoblog.wordpress.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: Adds a translate menu item in the WordPress admin back-end admin and front-end toolbar menus which translates selected text to current translate.
* Tags: user, translate, language, translate, back-end, front-end, buddypress
 * Contributors: jonathanmoorebcsorg
 * Requires At Least: 4.7
 * Tested Up To: 4.7.3
 * Stable Tag: 1.0
 * Version Components: {major}.{minor}.{bugfix}-{stage}{level}
 *
 *	{major}		Major code changes / re-writes or significant feature changes.
 *	{minor}		New features / options were added or improved.
 *	{bugfix}	Bugfixes or minor improvements.
 *	{stage}{level}	dev < a (alpha) < b (beta) < rc (release candidate) < # (production).
 *
 * See PHP's version_compare() documentation at http://php.net/manual/en/function.version-compare.php.
 * 
 * This script is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 3 of the License, or (at your option) any later
 * version.
 * 
 * This script is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details at
 * http://www.gnu.org/licenses/.
 * 
 * Copyright 2017 Jonathan Moore (https://jonmoblog.wordpress.com/)
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Nothing to see here...' );

include_once( plugin_dir_path(__FILE__) . 'btranslate-settings.php' );

if ( ! class_exists( 'JM_Buddy_Translate' ) ) {

	class JM_Buddy_Translate {

		private static $instance;
		private static $wp_min_version = 4.7;


		//enqueue scripts and hook buttons according to the options set
		public function __construct() {
			
			$options = get_option('jm_buddy_translate_options');

			//optionally, implement different function on front and back end 
			//$is_admin = is_admin();
			//$on_front = apply_filters( 'jm_buddy_translate_front_end', true );


			add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
			add_action( 'admin_init', array( __CLASS__, 'check_wp_version' ) );

			//register scripts and elements to be available on both back and front end
			add_action('admin_head', array( __CLASS__, 'jm_buddy_translate_register_head'));
			add_action('wp_enqueue_scripts', array( __CLASS__, 'jm_buddy_translate_scripts_method'));
			add_action('wp_footer', array( __CLASS__, 'jm_buddy_translate_footer'));
			add_action('admin_menu', array( __CLASS__, 'jm_buddy_translate_footer'));

			//add translate to menu bar if option set
			if ( $options['menubar_translate'] ){
				add_action( 'wp_before_admin_bar_render', array( __CLASS__, 'add_translate_toolbar' ) );
			}
			//add translate buttons to buddypress if option set
			if ($options['buddypress_translate'] ){
				add_action( 'bp_activity_entry_meta', array( __CLASS__, 'add_activity_translate_button' ) );
				add_action( 'bp_after_message_meta', array( __CLASS__, 'add_message_translate_button' ) );				
			}
			if ($options['bbpress_translate'] ){
				add_action( 'bbp_theme_before_reply_admin_links', array( __CLASS__, 'add_bbp_translate_button' ) );				
			}
			
	}

		//hooked: bp_after_message_meta - called from eg buddypress/activity/entry.php
		public static function add_bbp_translate_button(){
			?><a href="#" class="button acomment-translate" onmousedown="javascript:btnTranslatebbp(jQuery(this));return false;" onclick="javascript:return false;"><?php printf( __( 'Translate', 'jm-buddy-translate' ) ); ?></a><?php
		}
		
		
		//hooked: bp_after_message_meta - called from eg buddypress/activity/entry.php
		public static function add_message_translate_button(){
			?><a href="#" class="button acomment-translate" onmousedown="javascript:btnTranslateMessage(jQuery(this));return false;" onclick="javascript:return false;"><?php printf( __( 'Translate', 'jm-buddy-translate' ) ); ?></a><?php
		}
		//hooked: bp_activity_entry_meta - called from eg buddypress/activity/entry.php
		//after other activity action buttons
		public static function add_activity_translate_button(){
			?><a href="#" class="button acomment-translate" onmousedown="javascript:btnTranslateActivity(jQuery(this));return false;" onclick="javascript:return false;"><?php printf( __( 'Translate', 'jm-buddy-translate' ) ); ?></a><?php
		}
		//called in admin mode: do any extra admin stuff then our standard Front End scripts
		public static function jm_buddy_translate_register_head() {
			self::jm_buddy_translate_scripts_method();
		}

		//including .min versions unless SCRIPT_DEBUG defined
		//using file timestamp as version
		public static function jm_buddy_translate_scripts_method() {

			$options = get_option('jm_buddy_translate_options');
	
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$csfile='css/jm-buddy-translate' . $suffix . '.css' ;
			wp_register_style('jm_buddy_translate-css',	plugin_dir_url(__FILE__) . $csfile , false, 
					filemtime( plugin_dir_path(__FILE__) . $csfile), 'all' );
			wp_enqueue_style( 'jm_buddy_translate-css');
					//,	plugin_dir_url(__FILE__) . $csfile , array( 'jquery3' ), 					filemtime( plugin_dir_path(__FILE__) . $csfile), 'all' );
			
			//Core JS file - now using WP standard JQuery
			//$jsfile='js/jm-buddy-translate' . $suffix . '.js' ;
			//wp_register_script( 'jquery3', plugin_dir_url(__FILE__) . 'js/jquery.min.js', array(), '3.2.0', true );
			
			$jsfile='js/jm-buddy-translate' . $suffix . '.js' ;
			wp_enqueue_script( 'jm_buddy_translate', plugin_dir_url(__FILE__) . $jsfile , array( 'jquery' ), 
					filemtime( plugin_dir_path(__FILE__) . $jsfile ), true);

		}


		//footer to add to every page for translation placeholder
		public static function jm_buddy_translate_footer() {
				echo('<div id="bTranslateContainer">' . 
								'<div id="bTranslateHeader"><span id="bTranslateTitle">' .  
									__('Google Translation', 'jm-buddy-translate' ) .
									' </span> - <a id="bTranslateLink" target="googletranslate" href="">' . 
									'<span class="bigscreen-caption">' . __( 'open in Google Translate', 'jm-buddy-translate' ) . '</span>' .
										' <span id="google-link-icon" class="ab-icon dashicons-'.self::$dashicons[326].'"></span>' .
										' </a> &nbsp; &nbsp; &nbsp; <a id="bTranslateClose" href="#" onclick="javascript:btMinimize();return false;" >' .
									'<span class="bigscreen-caption">' . __('close', 'jm-buddy-translate' ) . '</span>' .
										' <span id="close-link-icon" class="ab-icon dashicons-'.self::$dashicons[153].'"></span>' .
										' </a> </div>' . 
						    '<div id="bTranslateResult"></div></div>');
		}
		public static function &get_instance() {
			if ( ! isset( self::$instance ) )
				self::$instance = new self;
			return self::$instance;
		}

		public static function load_textdomain() {
			load_plugin_textdomain( 'jm-buddy-translate', false, 'jm-buddy-translate/languages/' );
		}

		public static function check_wp_version() {
			global $wp_version;
			if ( version_compare( $wp_version, self::$wp_min_version, '<' ) ) {
				$plugin = plugin_basename( __FILE__ );
				if ( is_plugin_active( $plugin ) ) {
					self::load_textdomain();
					if ( ! function_exists( 'deactivate_plugins' ) ) {
						require_once trailingslashit( ABSPATH ).'wp-admin/includes/plugin.php';
					}
					$plugin_data = get_plugin_data( __FILE__, false );	// $markup = false
					deactivate_plugins( $plugin, true );	// $silent = true
					wp_die( 
						'<p>'.sprintf( __( '%1$s requires %2$s version %3$s or higher and has been deactivated.',
							'jm-buddy-translate' ), $plugin_data['Name'], 'WordPress', self::$wp_min_version ).'</p>'.
						'<p>'.sprintf( __( 'Please upgrade %1$s before trying to reactivate the %2$s plugin.',
							'jm-buddy-translate' ), 'WordPress', $plugin_data['Name'] ).'</p>'
					);
				}
			}
		}

		public static function add_translate_toolbar() {
			//exit if not logged in
			if ( ! $user_id = get_current_user_id() )
				return;			
			
			global $wp_admin_bar;
			$menu_translate = '<span class="bigscreen-caption">' . __( 'translate', 'jm-buddy-translate' ) . '</span>';
			
			/*
			 * Menu Icon and Title
			 */
			$dashicon = apply_filters( 'jm_buddy_translate_menu_dashicon', 326, $menu_translate );

			if ( ! empty( $dashicon ) && $dashicon !== 'none' ) {
				if ( isset( self::$dashicons[$dashicon] ) ) {		// just in case
					$menu_icon = '<span id="translate-icon" title="' . __( 'translate', 'jm-buddy-translate' ) . '" class="ab-icon dashicons-'.self::$dashicons[$dashicon].'"></span>';
				} else $menu_icon = '';
			} else $menu_icon = '';

			$menu_title = apply_filters( 'jm_buddy_translate_menu_title', '%s', $menu_translate );
			$menu_title = sprintf( $menu_title, $menu_translate );
			$wp_admin_bar->add_node( array(	// since wp 3.1
				'id' => 'jm-buddy-translate',
				'title' => $menu_icon.$menu_title,
				'parent' => false,
				'href' => '#',  //TODO: add action href here
				'group' => false,
				//'meta' => false,
				'meta'  => array(
				'onclick' => 'return false;'  //suppress click navigation, instead mousedown is hooked
        )
			) );
		}

		private static function get_target_locale() {
			global $wp_local_package;
			if ( isset( $wp_local_package ) )
	      			$locale = $wp_local_package;
			
			//WP4.7 function to get current user locale
			$locale = get_user_locale();
			
			/* fallback methods
			if ( defined( 'WPLANG' ) )
				$locale = WPLANG;
			if ( is_multisite() ) {
				if ( ( $ms_locale = get_option( 'WPLANG' ) ) === false )
					$ms_locale = get_site_option( 'WPLANG' );
				if ( $ms_locale !== false )
					$locale = $ms_translate;
			} else {
				$db_locale = get_option( 'WPLANG' );
				if ( $db_locale !== false )
					$locale = $db_locale;
			}
			*/
			if ( empty( $locale ) )
				$locale = 'en_US';      // just in case
			return $locale;
		}

		
		private static $dashicons = array(
			100 => 'admin-appearance',
			101 => 'admin-comments',
			102 => 'admin-home',
			103 => 'admin-links',
			104 => 'admin-media',
			105 => 'admin-page',
			106 => 'admin-plugins',
			107 => 'admin-tools',
			108 => 'admin-settings',
			109 => 'admin-post',
			110 => 'admin-users',
			111 => 'admin-generic',
			112 => 'admin-network',
			115 => 'welcome-view-site',
			116 => 'welcome-widgets-menus',
			117 => 'welcome-comments',
			118 => 'welcome-learn-more',
			119 => 'welcome-write-blog',
			120 => 'wordpress',
			122 => 'format-quote',
			123 => 'format-aside',
			125 => 'format-chat',
			126 => 'format-video',
			127 => 'format-audio',
			128 => 'format-image',
			130 => 'format-status',
			132 => 'plus',
			133 => 'welcome-add-page',
			134 => 'align-center',
			135 => 'align-left',
			136 => 'align-right',
			138 => 'align-none',
			139 => 'arrow-right',
			140 => 'arrow-down',
			141 => 'arrow-left',
			142 => 'arrow-up',
			145 => 'calendar',
			147 => 'yes',
			148 => 'admin-collapse',
			153 => 'dismiss',
			154 => 'star-empty',
			155 => 'star-filled',
			156 => 'sort',
			157 => 'pressthis',
			158 => 'no',
			159 => 'marker',
			160 => 'lock',
			161 => 'format-gallery',
			163 => 'list-view',
			164 => 'exerpt-view',
			165 => 'image-crop',
			166 => 'image-rotate-left',
			167 => 'image-rotate-right',
			168 => 'image-flip-vertical',
			169 => 'image-flip-horizontal',
			171 => 'undo',
			172 => 'redo',
			173 => 'post-status',
			174 => 'cart',
			175 => 'feedback',
			176 => 'cloud',
			177 => 'visibility',
			178 => 'vault',
			179 => 'search',
			180 => 'screenoptions',
			181 => 'slides',
			182 => 'trash',
			183 => 'analytics',
			184 => 'chart-pie',
			185 => 'chart-bar',
			200 => 'editor-bold',
			201 => 'editor-italic',
			203 => 'editor-ul',
			204 => 'editor-ol',
			205 => 'editor-quote',
			206 => 'editor-alignleft',
			207 => 'editor-aligncenter',
			208 => 'editor-alignright',
			209 => 'editor-insertmore',
			210 => 'editor-spellcheck',
			211 => 'editor-distractionfree',
			212 => 'editor-kitchensink',
			213 => 'editor-underline',
			214 => 'editor-justify',
			215 => 'editor-textcolor',
			216 => 'editor-paste-word',
			217 => 'editor-paste-text',
			218 => 'editor-removeformatting',
			219 => 'editor-video',
			220 => 'editor-customchar',
			221 => 'editor-outdent',
			222 => 'editor-indent',
			223 => 'editor-help',
			224 => 'editor-strikethrough',
			225 => 'editor-unlink',
			226 => 'dashboard',
			227 => 'flag',
			229 => 'leftright',
			230 => 'location',
			231 => 'location-alt',
			232 => 'images-alt',
			233 => 'images-alt2',
			234 => 'video-alt',
			235 => 'video-alt2',
			236 => 'video-alt3',
			237 => 'share',
			238 => 'chart-line',
			239 => 'chart-area',
			240 => 'share-alt',
			242 => 'share-alt2',
			301 => 'twitter',
			303 => 'rss',
			304 => 'facebook',
			305 => 'facebook-alt',
			306 => 'camera',
			307 => 'groups',
			308 => 'hammer',
			309 => 'art',
			310 => 'migrate',
			311 => 'performance',
			312 => 'products',
			313 => 'awards',
			314 => 'forms',
			316 => 'download',
			317 => 'upload',
			318 => 'category',
			319 => 'admin-site',
			320 => 'editor-rtl',
			321 => 'backup',
			322 => 'portfolio',
			323 => 'tag',
			324 => 'wordpress-alt',
			325 => 'networking',
			326 => 'translation',
			328 => 'smiley',
			330 => 'book',
			331 => 'book-alt',
			332 => 'shield',
			333 => 'menu',
			334 => 'shield-alt',
			335 => 'no-alt',
			336 => 'id',
			337 => 'id-alt',
			338 => 'businessman',
			339 => 'lightbulb',
			340 => 'arrow-left-alt',
			341 => 'arrow-left-alt2',
			342 => 'arrow-up-alt',
			343 => 'arrow-up-alt2',
			344 => 'arrow-right-alt',
			345 => 'arrow-right-alt2',
			346 => 'arrow-down-alt',
			347 => 'arrow-down-alt2',
			348 => 'info',
			459 => 'star-half',
			460 => 'minus',
			462 => 'googleplus',
			463 => 'update',
			464 => 'edit',
			465 => 'email',
			466 => 'email-alt',
			468 => 'sos',
			469 => 'clock',
			470 => 'smartphone',
			471 => 'tablet',
			472 => 'desktop',
			473 => 'testimonial',
		);		
	}//class
}//if class exists


JM_Buddy_Translate::get_instance();

?>
