<?php
/**
* @copyright	Copyright (C) 2013 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

class PixPublishControllerPanel extends JControllerLegacy
{
	public function getData()
	{
		$input = JFactory::getApplication()->input;
		$start = $input->getUint( 'start', 0 );
		$end = $input->getUint( 'end', 0 );
		
		$result = array();
		
		if( $start != 0 && $end != 0 )
		{
			/*echo JDate::getInstance( $start )->toISO8601().PHP_EOL;
			echo JDate::getInstance( $end )->toISO8601();
			die();*/
			
			JPluginHelper::importPlugin( 'pixsubmit' );
			$dispatcher =& JDispatcher::getInstance();
			$results = $dispatcher->trigger( 'onDataFetch', array( JDate::getInstance( $start ), JDate::getInstance( $end ) ) );
			
			$rows = array();
			foreach ($results as $result)
			{
				$rows = array_merge((array) $rows, (array) $result);
			}
			
			//print_r( $rows ); die();
			
			/*$o1 = new stdClass();
			$o1->id = 1;
			$o1->title = 'Test 1';
			$o1->start = '2014-03-18';
			
			
			$result[] = $o1;*/
		}
		echo json_encode( $rows );
		JFactory::getApplication()->close();
	}
	
	public function updateEndTime()
	{
		$input = JFactory::getApplication()->input;
	}
	
	public function move()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getString( 'id', '' );
		$this->logThis( 'id= '.$id );
		JFactory::getApplication()->close();
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
