<?php
/**
 * @package         PixPublish
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class PixEditor extends JEditor
{
	public function getInit()
	{
		// Check if editor is already loaded
		if (is_null(($this->_editor)))
		{
			return;
		}
	
		$args['event'] = 'onInit';
	
		$results[] = $this->_editor->update($args);
		//return print_r( $results, true );
		foreach ($results as $result)
		{
			if (trim($result))
			{
				// @todo remove code: $return .= $result;
				$return = $result;
			}
		}
		return $return;
	}
	
	/**
	 * Returns the global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $editor  The editor to use.
	 *
	 * @return  PixEditor The Editor object.
	 *
	 * @since   1.5
	 */
	public static function getInstance($editor = 'none')
	{
		$signature = serialize($editor);
	
		if (empty(self::$instances[$signature]))
		{
			self::$instances[$signature] = new PixEditor($editor);
		}
	
		return self::$instances[$signature];
	}
}