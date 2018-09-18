<?php
/**
 * @copyright       Copyright (C) 2018. All rights reserved.
 * @link            https://www.joomlashack.com/joomla-extensions/shack-editorial-calendar/
 * @author          You Rock AB 2003-2017 All Rights Reserved
 * @author          2018, Joomlashack <help@joomlashack.com> - https://www.joomlashack.com.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.form.editor');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pixeditor.php';

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
        $str = $this->getEditor()->save($this->id);
        // Hack to fix the problem with 3.4.4 update, see https://github.com/joomla/joomla-cms/pull/7263
        if ($this->getEditor()->getEditorType() == 'PlgEditorTinymce') {
            $str .= ' tinyMCE.get("' . $this->id . '").save();';
        }
        return $str;
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
        if (empty($this->editor)) {
            $editor = null;

            if ($this->editorType) {
                // Get the list of editor types.
                $types = $this->editorType;

                // Get the database object.
                $db = JFactory::getDbo();

                // Iterate over teh types looking for an existing editor.
                foreach ($types as $element) {
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
                    if ($editor) {
                        break;
                    }
                }
            }

            // Create the JEditor instance based on the given editor.
            if (is_null($editor)) {
                $conf   = JFactory::getConfig();
                $editor = $conf->get('editor');
            }

            //$this->editor = JEditor::getInstance($editor);
            $this->editor = PixEditor::getInstance($editor);
        }

        return $this->editor;
    }
}
