<?php
/**
 * @package         ShackEditorialCalendarfree
 * @copyright       Copyright (C) 2018. All rights reserved.
 * @link            https://www.joomlashack.com/joomla-extensions/shack-editorial-calendar/
 * @author          You Rock AB 2003-2017 All Rights Reserved
 * @author          2018, Joomlashack <help@joomlashack.com> - https://www.joomlashack.com.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class PixPublishfreeController extends JControllerLegacy
{
	protected $default_view = 'panel';
	
	public function display($cachable = false, $urlparams = false)
	{
		parent::display( $cachable, $urlparams );
		
		return $this;
	}
}
