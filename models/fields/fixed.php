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

jimport( 'joomla.form.editor' );
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pixeditor.php';

class JFormFieldFixed extends JFormFieldEditor
{
	public $type = 'fixed';
	
	public function getInit()
	{
		$editor = $this->getEditor();
		return $editor->getInit();
	}
	
	public function save()
	{
		return $this->getEditor()->save($this->id);
		//return 'sudde';
	}
	
	/**
	 * Method to get a PixEditor object based on the form field.
	 *
	 * @return  PixEditor  The PixEditor object.
	 *
	 */
	protected function getEditor()
	{
		// Only create the editor if it is not already created.
		if (empty($this->editor))
		{
			$editor = null;
	
			if ($this->editorType)
			{
				// Get the list of editor types.
				$types = $this->editorType;
	
				// Get the database object.
				$db = JFactory::getDbo();
	
				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query = $db->getQuery(true)
					->select('element')
					->from('#__extensions')
					->where('element = ' . $db->quote($element))
					->where('folder = ' . $db->quote('editors'))
					->where('enabled = 1');
	
					// Check of the editor exists.
					$db->setQuery($query, 0, 1);
					$editor = $db->loadResult();
	
					// If an editor was found stop looking.
					if ($editor)
					{
						break;
					}
				}
			}
	
			// Create the JEditor instance based on the given editor.
			if (is_null($editor))
			{
				$conf = JFactory::getConfig();
				$editor = $conf->get('editor');
			}
	
			//$this->editor = JEditor::getInstance($editor);
			$this->editor = PixEditor::getInstance($editor);
		}
	
		return $this->editor;
	}
}