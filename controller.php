<?php
/**
 * @package         PixPublishfree
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
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
