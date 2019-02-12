<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @author    2003-2017 You Rock AB. All Rights Reserved
 * @copyright 2018-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackEditorialCalendar-Pro.
 *
 * ShackEditorialCalendar-Pro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackEditorialCalendar-Pro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackEditorialCalendar-Pro.  If not, see <http://www.gnu.org/licenses/>.
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
