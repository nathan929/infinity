<?php
/**
 * PIE API: option extensions, yes/no radio class file
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://marshallsorenson.com/
 * @copyright Copyright (C) 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package PIE
 * @subpackage options-ext
 * @since 1.0
 */

Pie_Easy_Loader::load_ext( 'options/radio' );

/**
 * Yes/No radio option
 *
 * @package PIE
 * @subpackage options-ext
 */
class Pie_Easy_Exts_Option_Yesno
	extends Pie_Easy_Exts_Option_Radio
		implements Pie_Easy_Options_Option_Auto_Field
{
	public function load_field_options()
	{
		return array(
			true => __( 'Yes', pie_easy_text_domain ),
			false => __( 'No', pie_easy_text_domain )
		);
	}
}

?>