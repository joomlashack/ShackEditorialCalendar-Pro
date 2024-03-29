<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @author    2003-2017 You Rock AB. All Rights Reserved
 * @copyright 2018-2020 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with ShackEditorialCalendar-Pro.  If not, see <https://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

class PixEditor extends JEditor
{
    /**
     * @return void
     */
    public function getInit()
    {
        if ($this->_editor) {
            $args['event'] = 'onInit';
            $this->_editor->update($args);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getEditorType()
    {
        return $this->_editor ? get_class($this->_editor) : null;
    }

    /**
     * Returns the global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @param string $editor The editor to use.
     *
     * @return  PixEditor The Editor object.
     *
     * @since   1.5
     */
    public static function getInstance($editor = 'none')
    {
        $signature = serialize($editor);

        if (empty(self::$instances[$signature])) {
            self::$instances[$signature] = new PixEditor($editor);
        }

        return self::$instances[$signature];
    }
}
