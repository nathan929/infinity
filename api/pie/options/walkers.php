<?php
/**
 * PIE Framework API options walkers class file
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://marshallsorenson.com/
 * @copyright Copyright &copy; 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package pie
 * @subpackage options
 * @since 1.0
 */

/**
 * Make category options easy
 */
class Pie_Easy_Options_Walker_Category extends Walker_Category
{
	/**
	 * @see Walker_Category::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int $depth Depth of category in reference to parents.
	 * @param array $args
	 */
	function start_el( &$output, $category, $depth, $args )
	{
		// get option from args
		$option = $args['pie_easy_option'];

		// put a checkbox before the category
		$output .= sprintf(
			'<input type="checkbox" value="%s" name="%s" id="%s" class="%s"%s />',
			esc_attr( $category->term_id ),
			esc_attr( $option->name ),
			esc_attr( $option->field_id ),
			esc_attr( $option->field_class ),
			( $option->get() == $category->term_id ) ? ' checked="checked"' : null
		);

		// call parent to render category link
		$output .= parent::start_el( $output, $category, $depth, $args );
	}
}

?>