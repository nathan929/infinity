<?php
/**
 * BP Tasty theme API templating functions
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://bp-tricks.com/
 * @copyright Copyright (C) 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package api
 * @subpackage templating
 * @since 1.0
 */

define( 'BP_TASTY_EXTRAS_SKIN_DIR', BP_TASTY_EXTRAS_DIR . '/skins' );
define( 'BP_TASTY_ACTIVE_SKIN_DIR', BP_TASTY_EXTRAS_SKIN_DIR . '/' . BP_TASTY_SKIN_NAME );

define( 'BP_TASTY_SKIN_DEFAULT', 'minimal' );
define( 'BP_TASTY_SKIN_NAME', bp_tasty_skins_active_name() );

/**
 * Get the name of the active skin
 *
 * @return string
 */
function bp_tasty_skins_active_name()
{
	// make sure registry has been initialized
	bp_tasty_options_registry_init();
	
	// try to get it from the options
	$skin_name = bp_tasty_get_option( 'active_skin' );

	// did we get a name?
	if ( empty( $skin_name ) ) {
		// nope, use default
		return BP_TASTY_SKIN_DEFAULT;
	} else {
		return $skin_name;
	}
}

/**
 * Return a list of the available skins in the skins dir
 *
 * @return array
 */
function bp_tasty_skins_available()
{
	// does the skins directory exist?
	if ( is_dir( BP_TASTY_EXTRAS_SKIN_DIR ) ) {
		// open the skins dir
		$dh = opendir( BP_TASTY_EXTRAS_SKIN_DIR );
		// check that handle is valid
		if ( $dh ) {
			// list of dirs
			$dirs = array();
			// loop through and add dirs only to list
			while (($file = readdir($dh)) !== false) {
				// skip "dot" files
				if ( preg_match('/^\./', $file) ) {
					continue;
				}
				// build up full file path
				$file_path = BP_TASTY_EXTRAS_SKIN_DIR . DIRECTORY_SEPARATOR . $file;
				// is it a directory?
				if ( is_dir($file_path) ) {
					$dirs[$file] = $file;
				}
			}
			// destroy handle
			closedir($dh);
			// put skins in alphabetical order
			asort($dirs);
			// done
			return $dirs;
		} else {
			throw new Exception( 'Unable to open the skins dir: ' . BP_TASTY_EXTRAS_SKIN_DIR );
		}
	} else {
		throw new Exception( 'The skins dir does not exist: ' . BP_TASTY_EXTRAS_SKIN_DIR );
	}
}

/**
 * Filter located template from bp_core_load_template
 *
 * @see bp_core_load_template()
 * @param string $located_template
 * @param array $template_names
 * @return string
 */
function bp_tasty_filter_template( $located_template, $template_names )
{
	// template already located, skip
	if ( empty( $located_template ) ) {
		return bp_tasty_locate_template( $template_names );
	} else {
		return $located_template;
	}
}
add_filter( 'bp_located_template', 'bp_tasty_filter_template', 10, 2 );

/**
 * Check if template exists in custom style (skin) path
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */
function bp_tasty_locate_template( $template_names, $load = false )
{
	// did we get an array?
	if ( is_array( $template_names ) ) {

		// loop through all templates
		foreach ( $template_names as $template_name ) {

			// prepend all template names with the active skin dir
			$located_template = BP_TASTY_ACTIVE_SKIN_DIR . '/' . $template_name;

			// does it exist?
			if ( file_exists( $located_template ) ) {
				// load it?
				if ($load) {
					load_template( $located_template );
				}
				// return the located template path
				return $located_template;
			}
		}
	}

	// didn't find a template
	return '';
}

/**
 * Load a template
 *
 * @param string $template_name
 * @return string
 */
function bp_tasty_load_template( $template_name )
{
	if ( !is_array( $template_name ) ) {
		$template_name = array( $template_name );
	}

	return bp_tasty_locate_template( $template_name, true );
}

?>
