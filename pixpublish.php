<?php
/**
* @copyright	Copyright (C) 2014 Johan Sundell. All rights reserved.
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
$controller->execute( JRequest::getCmd( 'task' ) );
$controller->redirect();
