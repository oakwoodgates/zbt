<?php
/**
 * Plugin Name: zbt
 * Plugin URI:  http://github.com/oakwoodgates/zbt
 * Description: For Brigit
 * Version:     0.0.4
 * Author:      WPGuru4u
 * Author URI:  http://wpguru4u.com
 * Donate link: http://github.com/oakwoodgates/zbt
 * License:     GPLv2
 * Text Domain: zbt
 * Domain Path: /languages
 *
 * @link http://github.com/oakwoodgates/zbt
 *
 * @package zbt
 * @version 0.0.2
 */

/**
 * Copyright (c) 2016 WPGuru4u (email : wpguru4u@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */


/**
 * Autoloads files with classes when needed
 *
 * @since  0.0.1
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function zbt_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'ZBT_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'ZBT_' ) )
	) );

	ZBT::include_file( $filename );
}
spl_autoload_register( 'zbt_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  0.0.1
 */
final class ZBT {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.0.1
	 */
	const VERSION = '0.0.2';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  0.0.1
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin
	 *
	 * @var ZBT
	 * @since  0.0.1
	 */
	protected static $single_instance = null;

	/**
	 * Instance of ZBT_Gravityforms
	 *
	 * @since 0.0.1
	 * @var ZBT_Gravityforms
	 */
	protected $gravityforms;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  0.0.1
	 * @return ZBT A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  0.0.1
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->gravityforms = new ZBT_Gravityforms( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'zbt', false, dirname( $this->basename ) . '/languages/' );
			$this->plugin_classes();
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.0.1
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			add_action( 'admin_init', array( $this, 'deactivate_me' ) );

			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function deactivate_me() {
		deactivate_plugins( $this->basename );
	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  0.0.1
	 * @return boolean True if requirements are met.
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'zbt is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'zbt' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.1
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'gravityforms':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 *
	 * @since  0.0.1
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'includes/class-'. $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  0.0.1
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  0.0.1
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the ZBT object and return it.
 * Wrapper for ZBT::get_instance()
 *
 * @since  0.0.1
 * @return ZBT  Singleton instance of plugin class.
 */
function zbt() {
	return ZBT::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( zbt(), 'hooks' ) );

register_activation_hook( __FILE__, array( zbt(), '_activate' ) );
register_deactivation_hook( __FILE__, array( zbt(), '_deactivate' ) );
