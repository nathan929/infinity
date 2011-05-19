<?php
/**
 * PIE API: base registry
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://marshallsorenson.com/
 * @copyright Copyright (C) 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package PIE
 * @subpackage base
 * @since 1.0
 */

Pie_Easy_Loader::load( 'collections' );

/**
 * Make keeping track of concrete components
 *
 * @package PIE
 * @subpackage base
 */
abstract class Pie_Easy_Registry extends Pie_Easy_Policeable
{
	/**
	 * Name of parameter which passes back blog id
	 */
	const PARAM_BLOG_ID = 'pie_easy_options_blog_id';

	/**
	 * Name of parameter which passes back blog theme
	 */
	const PARAM_BLOG_THEME = 'pie_easy_options_blog_theme';

	/**
	 * Stack of config files that have been loaded
	 *
	 * @var Pie_Easy_Stack
	 */
	private $files_loaded;

	/**
	 * Name of the theme currently being loaded
	 *
	 * @var string
	 */
	protected $loading_theme;

	/**
	 * Blog id when screen was initialized
	 *
	 * @var integer
	 */
	protected $screen_blog_id;

	/**
	 * Blog theme when screen was initialized
	 *
	 * @var string
	 */
	protected $screen_blog_theme;

	/**
	 * Registered components map
	 *
	 * @var Pie_Easy_Map
	 */
	private $components;

	/**
	 * Singleton constructor
	 * @ignore
	 */
	public function __construct()
	{
		// init local collections
		$this->components = new Pie_Easy_Map();
		$this->files_loaded = new Pie_Easy_Stack();
	}

	/**
	 * Init ajax requirements
	 */
	public function init_ajax()
	{
		// init ajax for each concrete component
	}

	/**
	 * Init screen dependencies for all applicable components to be rendered
	 */
	public function init_screen()
	{
		global $blog_id;

		$this->screen_blog_id = (integer) $blog_id;
		$this->screen_blog_theme = get_stylesheet();

		add_action( 'pie_easy_enqueue_styles', array($this, 'init_styles') );
		add_action( 'pie_easy_enqueue_scripts', array($this, 'init_scripts') );
	}

	/**
	 * Enqueue required styles
	 */
	public function init_styles() {}

	/**
	 * Enqueue required scripts
	 */
	public function init_scripts() {}

	/**
	 * Template method to allow localization of scripts
	 */
	protected function localize_script() {}

	/**
	 * Register a component
	 *
	 * @param Pie_Easy_Component $component
	 * @return boolean
	 */
	final protected function register( Pie_Easy_Component $component )
	{
		// has the component already been registered?
		if ( $this->components->contains( $component->name ) ) {

			// get component stack
			$comp_stack = $this->components->get_stack( $component->name );

			// check if component already registered for this theme
			if ( $comp_stack->contains( $component->theme ) ) {
				throw new Exception( sprintf(
					'The "%s" component has already been registered for the "%s" theme',
					$component->name, $component->theme ) );
			}

		} else {
			$comp_stack = new Pie_Easy_Stack();
			$this->components->add( $component->name, $comp_stack );
		};

		// set policy
		$component->policy( $this->policy() );

		// register it
		$comp_stack->push( $component );

		return true;
	}

	/**
	 * Returns true if a component has been registered
	 *
	 * @param string $name
	 * @return boolean
	 */
	final public function has( $name )
	{
		return $this->components->contains( $name );
	}

	/**
	 * Return a registered component object by name
	 *
	 * @param string $name
	 * @return Pie_Easy_Component
	 */
	final public function get( $name )
	{
		// check registry
		if ( $this->has( $name ) ) {
			// from top of stack
			return $this->get_stack($name)->peek();
		}

		// didn't find it
		throw new Exception( sprintf( 'Unable to get component "%s": not registered', $name ) );
	}

	/**
	 * Return all registered components as an array
	 *
	 * @return array
	 */
	final public function get_all( $reverse = false )
	{
		// components to return
		$components = array();

		// loop through and compare names
		foreach ( $this->components as $component_stack ) {
			// add comp on top of stack to array
			$components[] = $component_stack->peek();
		}

		// return them
		return $components;
	}

	/**
	 * Return stack for the given component name
	 *
	 * @param string $name
	 * @return Pie_Easy_Stack
	 */
	final protected function get_stack( $name )
	{
		return $this->components->item_at( $name );
	}

	/**
	 * Load directives from an ini file
	 *
	 * @uses parse_ini_file()
	 * @param string $filename Absolute path to the component ini file to parse
	 * @param string $theme The theme to assign the parsed directives to
	 * @return boolean
	 */
	final public function load_config_file( $filename, $theme )
	{
		// skip loaded files
		if ( $this->files_loaded->contains( $filename ) ) {
			return;
		} else {
			$this->files_loaded->push( $filename );
		}

		// set the current theme being loaded
		$this->loading_theme = $theme;

		// try to parse the file
		return $this->load_config_array( parse_ini_file( $filename, true ) );
	}

	/**
	 * Load components into registry from an array (of parsed ini sections)
	 *
	 * @param array $ini_array
	 * @return boolean
	 */
	private function load_config_array( $ini_array )
	{
		// an array means successful parse
		if ( is_array( $ini_array ) ) {
			// loop through each directive
			foreach ( $ini_array as $s_name => $s_config ) {
				$this->load_config_single( $s_name, $s_config );
			}
			// all done
			return true;
		}

		return false;
	}

	/**
	 * Load a single component into the registry (one parsed ini section)
	 *
	 * @param string $name
	 * @param array $config
	 * @return boolean
	 */
	abstract protected function load_config_single( $name, $config );

}

?>