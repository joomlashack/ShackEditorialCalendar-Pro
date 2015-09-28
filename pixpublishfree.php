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

// Access check.
if( !JFactory::getUser()->authorise( 'core.manage', 'com_pixpublishfree' ) )
	return JError::raiseWarning( 404, JText::_( 'JERROR_ALERTNOAUTHOR' ) );

if( !JLoader::import( 'pixpublishfree', JPATH_COMPONENT.'/helpers' ) )
	return JError::raiseWarning( 500, JText::_( 'COM_PIXPUBLISHFREE_INC_FILES_NOT_FOUND' ) );

// Execute the task.
$controller	= JControllerLegacy::getInstance( 'PixPublishfree' );
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();
