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

class PixEditor extends JEditor
{
    public function getInit()
    {
        // Check if editor is already loaded
        if (is_null(($this->_editor))) {
            return;
        }

        $args['event'] = 'onInit';

        $results[] = $this->_editor->update($args);
        //return print_r( $results, true );
        foreach ($results as $result) {
            if (trim($result)) {
                // @todo remove code: $return .= $result;
                $return = $result;
            }
        }
        return $return;
    }

    public function getEditorType()
    {
        // Check if editor is already loaded
        if (is_null(($this->_editor))) {
            return;
        }

        return get_class($this->_editor);
    }

    /**
     * Returns the global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @param   string $editor The editor to use.
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
