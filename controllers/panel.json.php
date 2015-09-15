<?php
/**
 * @package         PixPublish
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
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
		//$this->logThis( 'data: '.print_r( $data, true ) );
		$result = array();
		
		if( $start != 0 && $end != 0 )
		{
			$dispatcher = $this->importPlugins();
			$results = $dispatcher->trigger( 'onDataFetch', array( JDate::getInstance( $start ), JDate::getInstance( $end ), $data ) );
			
			$rows = array();
			foreach ($results as $result)
			{
				$rows = array_merge((array) $rows, (array) $result);
			}
		}
		echo json_encode( $rows );
		JFactory::getApplication()->close();
	}
	
	public function updateEndTime()
	{
		JFactory::getApplication()->close();
	}
	
	public function move()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dayd = $input->getInt( 'dayd', 0 );
		$mind = $input->getInt( 'mind', 0 );
		//$this->logThis( 'id:'.$id.' source:'.$source );
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onItemMove', array( $source, $id, $dayd, $mind, '' ) );
		
		//throw new Exception('Whoops, something happened!', 404);
		JFactory::getApplication()->close();
	}
	
	public function edit()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dispatcher = $this->importPlugins();
		$form = new JForm( 'com_pixpublish' );
		$extra = '';
		$results = $dispatcher->trigger( 'onGetDialog', array( $source, $id, $form ) );
		$item = null;
		if( count( $results ) != 0 )
		{
			$item = $results[0];
			
			if( $item != null )
			{
				$form->bind( $item );
				echo '<form action="" method="post" id="pixsubmit_form">'.$form->renderFieldset('').'</form>';
			}
		}
		else
		{
			throw new Exception('Whoops, something happened!', 500);
			JFactory::getApplication()->close();
		}
		JFactory::getApplication()->close();
	}

	public function create()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$dispatcher = $this->importPlugins();
		$form = new JForm( 'com_pixpublish' );
		$extra = '';

/*
		TODO
		Source is set correct, but the function triggered is still always the one in plugin content..?
		(Saving only works using correct plugin source...)
		
		Seems to work now.. 16/6
*/
		$results = $dispatcher->trigger( 'onCreateNew', array( $source, $id, $form ) );

//		echo $results;
/*
		$item = null;
		if( count( $results ) != 0 )
		{
			$item = $results[0];
			
			if( $item != null )
			{
				$form->bind( $item );
				echo '<form action="" method="post" id="pixsubmit_form">'.$form->renderFieldset('').'</form>';
			}
		}
		else
		{
			throw new Exception('Whoops, something happened!', 500);
			JFactory::getApplication()->close();
		}
*/
		JFactory::getApplication()->close();
	}
	
	public function savecreated()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$date = $input->getCmd( 'date', date('YYYY-MM-DD') );
		$source = $input->getCmd( 'plugin', '' );
		$data = json_decode( $input->get( 'data', '', 'raw' ) );
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onNewSave', array( $source, $id, $date, $data ) );
		
		JFactory::getApplication()->close();
	}
	
	public function save()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd( 'id', '' );
		$source = $input->getCmd( 'plugin', '' );
		$data = json_decode( $input->get( 'data', '', 'raw' ) );
		
		$dispatcher = $this->importPlugins();
		$results = $dispatcher->trigger( 'onItemSave', array( $source, $id, $data ) );
		
		JFactory::getApplication()->close();
	}
	
	/**
	 * @return JDispatcher
	 */
	protected function importPlugins()
	{
		JPluginHelper::importPlugin( 'pixpublish' );
		$dispatcher = JDispatcher::getInstance();
		return $dispatcher;
	}
	
	protected function logThis( $message )
	{
		/*jimport( 'joomla.log.log' );
	
		JLog::addLogger
			(
					array
					(
							'text_file' => 'com_pixpublish.log.php'
					),
					JLog::ALL,
					'com_pixpublish'
			);
		JLog::add( $message, JLog::WARNING, 'com_pixpublish' );*/
	}
}