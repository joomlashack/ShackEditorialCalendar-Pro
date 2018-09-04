<?php
/**
* @author		Johan Sundell <labs@pixpro.net>
* @link			https://www.pixpro.net/labs
* @copyright	Copyright © You Rock AB 2003-2017 All Rights Reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

// Access check.
if( !JFactory::getUser()->authorise( 'core.manage', 'com_pixpublish' ) )
	return JError::raiseWarning( 404, JText::_( 'JERROR_ALERTNOAUTHOR' ) );

if( !JLoader::import( 'pixpublish', JPATH_COMPONENT.'/helpers' ) )
	return JError::raiseWarning( 500, JText::_( 'COM_PIXPUBLISH_INC_FILES_NOT_FOUND' ) );

// Execute the task.
$controller	= JControllerLegacy::getInstance( 'PixPublish' );
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();