<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

class PixPublishController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar( 'view', JRequest::getCmd( 'view', 'panel' ) );
		parent::display( $cachable, $urlparams );
		
		return $this;
	}
}
