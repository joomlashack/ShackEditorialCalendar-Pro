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

defined('_JEXEC') or die();

class PixPublishControllerPanel extends JControllerLegacy
{
    public function getData()
    {
        $start = $this->input->getUint('start', 0);
        $end   = $this->input->getUint('end', 0);
        $data  = json_decode($this->input->get('data', '', 'raw'));

        if ($start != 0 && $end != 0) {
            $dispatcher = $this->importPlugins();
            $results    = $dispatcher->trigger(
                'onDataFetch',
                array(JDate::getInstance($start), JDate::getInstance($end), $data)
            );

            $rows = array();
            foreach ($results as $result) {
                $rows = array_merge((array)$rows, (array)$result);
            }
        }
        echo json_encode($rows);

        jexit();
    }

    public function updateEndTime()
    {
        jexit();
    }

    public function move()
    {
        $id     = $this->input->getCmd('id', '');
        $source = $this->input->getCmd('plugin', '');
        $dayd   = $this->input->getInt('dayd', 0);
        $mind   = $this->input->getInt('mind', 0);

        $dispatcher = $this->importPlugins();
        $dispatcher->trigger('onItemMove', array($source, $id, $dayd, $mind, ''));

        jexit();
    }

    public function edit()
    {
        $id         = $this->input->getCmd('id', '');
        $source     = $this->input->getCmd('plugin', '');
        $dispatcher = $this->importPlugins();
        $form       = new JForm('com_pixpublish');
        $extra      = '';
        $results    = $dispatcher->trigger('onGetDialog', array($source, $id, $form));
        $item       = null;
        if (count($results) != 0 || (int)$id == 0) {
            if (count($results) != 0) {
                foreach ($results as $row) {
                    if ($row) {
                        $item = $row;
                    }
                }
            }

            if ($item != null || (int)$id == 0) {
                $form->bind($item);

                // Output form (XML fieldsets must have name attribute set!)
                echo '<form action="" method="post" id="pixsubmit_form">';

                $fieldsets = $form->getFieldsets();
                foreach ($fieldsets as $fieldset) {
                    echo sprintf(
                        '<fieldset class="%s">%s</fieldset>',
                        $fieldset->class,
                        $form->renderFieldset($fieldset->name)
                    );
                }
                echo '</form>';

                $lines = array();
                $inits = array();
                foreach ($form->getFieldset() as $row) {
                    if ($row->type == 'fixed') {
                        $lines[] = $row->save();
                        $inits[] = $row->getInit();
                    }
                }
                if (count($lines) > 0) {
                    echo '<script type="text/javascript">';
                    echo 'function toggleMe(){';
                    foreach ($lines as $row) {
                        echo $row;
                    }
                    echo '};</script>';
                }
                foreach ($inits as $row) {
                    echo $row;
                }

            }
        } else {
            throw new Exception('Whoops, something happened!', 500);
        }

        jexit();
    }

    public function save()
    {
        $id     = $this->input->getCmd('id', '');
        $source = $this->input->getCmd('plugin', '');
        $data   = json_decode(urldecode($this->input->get('data', '', 'raw')));

        $dispatcher = $this->importPlugins();
        $dispatcher->trigger('onItemSave', array($source, $id, $data));

        jexit();
    }

    /**
     * @return JEventDispatcher
     */
    protected function importPlugins()
    {
        JPluginHelper::importPlugin('pixpublish');
        $dispatcher = JEventDispatcher::getInstance();

        return $dispatcher;
    }
}
