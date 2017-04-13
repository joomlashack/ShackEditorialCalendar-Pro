<?php

// No direct access.
defined('_JEXEC') or die;

JLoader::import( 'pixpublishplugin', JPATH_COMPONENT_ADMINISTRATOR.'/classes' );
JLoader::import( 'schedule', JPATH_ADMINISTRATOR.'/components/com_acymailing/helpers' );

class PlgPixPublishAcymailing extends PixPublishPlugin implements iPixPublishPlugin
{
	public function onDataFetch( $start, $stop, $data )
	{
		
	}
	
	public function onItemMove( $source, $id, $dayd, $mind )
	{
		
	}
	
	public function onGetDialog( $source, $id, $form )
	{
		
	}
	
	public function onItemSave( $source, $id, $data  )
	{
		
	}
	
	public function onRegisterSearchFilters()
	{
		
	}
	
	protected function logThis( $message )
	{
		jimport( 'joomla.log.log' );
			JLog::addLogger
		(
				array
				(
						'text_file' => 'com_pixpublish.log.php'
				),
				JLog::ALL,
				'com_pixpublish'
		);
		JLog::add( $message, JLog::WARNING, 'com_pixpublish' );
	}
}
