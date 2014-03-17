<?php
/**
* @copyright	Copyright (C) 2012 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

/**
 * Base controller class for Menu Manager.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class PixPublishController extends JControllerLegacy
{
	
	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar( 'view', JRequest::getCmd( 'view', 'articles' ) );
		parent::display( $cachable, $urlparams );
		PixPublishHelper::addSubmenu( 'articles' );
		
		return $this;
	}
}
