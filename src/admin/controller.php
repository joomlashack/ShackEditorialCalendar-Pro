<?php
/**
* @author		Johan Sundell <labs@pixpro.net>
* @link			https://www.pixpro.net/labs
* @copyright	Copyright © You Rock AB 2003-2017 All Rights Reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

class PixPublishController extends JControllerLegacy
{
	protected $default_view = 'panel';
	
	public function display($cachable = false, $urlparams = false)
	{
		parent::display( $cachable, $urlparams );
		
		return $this;
	}
}
