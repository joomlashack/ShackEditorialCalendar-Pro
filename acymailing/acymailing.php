<?php

// No direct access.
defined('_JEXEC') or die;

JLoader::import( 'pixpublishplugin', JPATH_COMPONENT_ADMINISTRATOR.'/classes' );
JLoader::import( 'helper', JPATH_ADMINISTRATOR.'/components/com_acymailing/helpers' );

class PlgPixPublishAcymailing extends PixPublishPlugin implements iPixPublishPlugin
{
	protected $autoloadLanguage = true;
	
	public function onDataFetch( $start, $stop, $data )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->select( 'tbl.mailid AS id, tbl.subject AS title, tbl.senddate AS start, "'.$this->getName().'" as plugin' )
			->from( '#__acymailing_mail tbl' )
			->where( 'tbl.senddate >= '.$query->q( $start->toUnix() ) )
			->where( 'tbl.senddate <= '.$query->q( $stop->toUnix() ) );
		
		//$this->logThis( (string)$query );
		ColorFixer::$st_color = $this->params->get( 'background_colour', '#3a87ad' ); // #3a87ad
		$result = $db->setQuery( $query )->loadObjectList( '', 'ColorFixer' );
		
		// Fix dates
		$result = self::fixDates( $result, 'start' );
		//$this->logThis( print_r( $result, true ) );
		
		return $result;
	}
	
	public function onItemMove( $source, $id, $dayd, $mind )
	{
		if( $source === $this->getName() )
		{
			/*if( !$this->getAuth( $id )->get( 'core.edit' ) )
			{
				if( !$this->canEditOwn( $id ) )
					return false;
			}*/
			//$this->logThis( 'dayd: '.$dayd.' mind:'.$mind );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->update( '#__acymailing_mail' );
			$query->set( 'senddate = UNIX_TIMESTAMP( DATE_ADD( ADDDATE( FROM_UNIXTIME( senddate ), '.(int)$dayd.' ), INTERVAL "'.(int)$mind.'" MINUTE ) )' );
		
			$query->where( 'mailid = '.(int)$id );
			//$this->logThis( (string)$query );
			if( !$db->setQuery($query)->execute() )
				return false;
		}
		return true;
	}
	
	public function onGetDialog( $source, $id, $form )
	{
		if( $source === $this->getName() )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			$query->select( 'tbl.mailid AS id, tbl.subject AS title, tbl.senddate AS start, "'.$this->getName().'" as plugin' )
				->from( '#__acymailing_mail tbl' )
				->where( 'tbl.mailid = '.$id );
			$result = $db->setQuery( $query )->loadObject();
			if( $result )
			{
				$arr = array( $result );
				$arr = self::fixDates( $arr, 'start' );
				$result = $arr[0];
			}
			$this->logThis( print_r( $result, true ) );
			
			JForm::addFormPath( __DIR__ . '/form' );
			$form->loadFile( 'form', false );
			return $result;
		}
		return false;
	}
	
	public function onItemSave( $source, $id, $data  )
	{
		if( $source === $this->getName() )
		{
			$this->logThis( print_r($data, true) );
			$db = JFactory::getDbo();
			$query = $db->getQuery( true );
			
			if( (int)$id > 0 )
				$query->update( '#__acymailing_mail' );
			
			$time = $data->start;
			if( $time )
			{
				$time = acymailing_getTime( $time );
				$query->set( 'senddate = UNIX_TIMESTAMP( TIMESTAMP( DATE( FROM_UNIXTIME( senddate ) ), TIME( FROM_UNIXTIME( '.$query->q( $time ).' ) ) ) )' );
			}
			
			if( (int)$id > 0 )
				$query->where( 'mailid = '.(int)$id );
			
			if( !$db->setQuery($query)->execute() )
			{
				return false;
			}
		}
		return true;
	}
	
	public function onRegisterSearchFilters()
	{
		$this->logThis( 'register' );
		
		JFactory::getDocument()->addScriptDeclaration('PLUGIN["acymailing"] = "'.JText::_('PLG_PIXPUBLISH_ACYMAILING_TYPE_NAME').'";');
		JFactory::getDocument()->addScript( JUri::root().'plugins/pixpublish/acymailing/media/js/acymailing.js' );
	}
	
	protected function getName()
	{
		return 'acymailing';
	}
	
	protected static function fixDates( &$arr, $fieldname )
	{
		foreach( $arr as $row )
		{
			$row->$fieldname = acymailing_getDate( $row->$fieldname );
		}
		return $arr;
	}
	
	protected static function getUserTimeoffset()
	{
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		self::logThis( print_r( $config, true ) );
		return $config->get('offset', 'UTC' );
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
