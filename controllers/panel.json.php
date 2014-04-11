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
		$data = json_decode( $input->get( 'data', '', 'raw' ) );
		$this->logThis( 'data: '.print_r( $data, true ) );
		$result = array();
		
		if( $start != 0 && $end != 0 )
		{
			/*echo JDate::getInstance( $start )->toISO8601().PHP_EOL;
			echo JDate::getInstance( $end )->toISO8601();
			die();*/
			
			//JPluginHelper::importPlugin( 'pixsubmit' );
			//$dispatcher =& JDispatcher::getInstance();
			$dispatcher = $this->importPlugins();
			$results = $dispatcher->trigger( 'onDataFetch', array( JDate::getInstance( $start ), JDate::getInstance( $end ), $data ) );
			
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
		JFactory::getApplication()->close();
		/*$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dayd = $input->getInt( 'dayd', 0 );
		$mind = $input->getInt( 'mind', 0 );
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onItemReseize', array( $source, $id, $dayd, $mind ) );*/
	}
	
	public function move()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dayd = $input->getInt( 'dayd', 0 );
		$mind = $input->getInt( 'mind', 0 );
		$this->logThis( 'id:'.$id.' source:'.$source );
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onItemMove', array( $source, $id, $dayd, $mind, '' ) );
		
		//throw new Exception('Whoops, something happened!', 404);
		JFactory::getApplication()->close();
	}
	
	public function test()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dispatcher = $this->importPlugins();
		$html = '';
		$results = $dispatcher->trigger( 'onGetDialog', array( $source, $id, &$html ) );
		$item = null;
		if( count( $results ) != 0 )
		{
			$item = $results[0];
		}
		//print_r( $item );
		$start = JDate::getInstance( $item->start );
		$start->hour = (int)$start->hour;
		$options = JHtml::_( 'select.options', JHtml::_( 'jgrid.publishedOptions', array( 'all' => false ) ), 'value', 'text', (int)$item->state, true );
		//print_r(JHtml::_('jgrid.publishedOptions') );die();
		
		/*$options = JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', true, true);
		echo $options;
		echo JHtml::_('select.genericlist', $options );
		die();*/
		//echo
		if( $html == '' )
		{
		//echo '<input id="pixtest_title" type="text" value="test" /><br/>';
$html = <<<HTML
<form action="" method="post" id="pixsubmit_form">
<input id="pixtest_title" type="text" name="pixtest_title" value="$item->title" /><br/>
<div class="input-append bootstrap-timepicker">
             start: <input id="pixtest_start" name="pixtest_start" type="text" class="input-small" value="$start->hour:$start->minute">
             <!-- <span class="add-on"><i class="icon-calendar"></i></span>-->
             <i class="icon-calendar"></i>
</div>
<div class="filter-select">
<select name="filter_status" class="inputbox" id="filter_status">
$options
</select>
</div>
</form>
HTML;
		}
		echo $html;
		JFactory::getApplication()->close();
	}
	
	public function save()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		//$title = $input->getString( 'title', null );
		//$start = $input->getString( 'start', null );
		$data = json_decode( $input->get( 'data', '', 'raw' ) );
		//print_r( $data ); die();
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onItemSave', array( $source, $id, $data ) );
		
		JFactory::getApplication()->close();
	}
	
	/**
	 * @return JDispatcher
	 */
	protected function importPlugins()
	{
		JPluginHelper::importPlugin( 'pixsubmit' );
		$dispatcher = JDispatcher::getInstance();
		return $dispatcher;
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
