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

        if ($start && $end) {
            $results = $this->getDispatcher()->trigger(
                'onDataFetch',
                array(JDate::getInstance($start), JDate::getInstance($end), $data)
            );

            $rows = array();
            foreach ($results as $result) {
                $rows = array_merge($rows, (array)$result);
            }

            echo json_encode($rows);
        }

        jexit();
    }

    public function updateEndTime()
    {
        jexit();
    }

    public function move()
    {
        $source = $this->input->getCmd('plugin', '');
        $id     = $this->input->getCmd('id', '');
        $dayd   = $this->input->getInt('dayd', 0);
        $mind   = $this->input->getInt('mind', 0);

        $this->getDispatcher()->trigger('onItemMove', array($source, $id, $dayd, $mind, ''));

        jexit();
    }

    public function edit()
    {
        echo __METHOD__;
        echo '<pre>' . print_r($_REQUEST, 1) . '</pre>';
        jexit();


        $source = $this->input->getCmd('plugin', '');
        $id     = $this->input->getCmd('id', '');
        $form   = new JForm('com_pixpublish');

        $items = array_filter($this->getDispatcher()->trigger('onGetDialog', array($source, $id, $form)));
        $form->bind(array_shift($items));

        $displayData = array(
            'form' => $form,
        );

        echo JLayoutHelper::render('sec.form.modal', $displayData, SECAL_LAYOUTS);
        jexit();
    }

    public function save()
    {
        $id     = $this->input->getCmd('id', '');
        $source = $this->input->getCmd('plugin', '');
        $data   = json_decode(urldecode($this->input->get('data', '', 'raw')));

        $dispatcher = $this->getDispatcher();
        $dispatcher->trigger('onItemSave', array($source, $id, $data));

        jexit();
    }

    /**
     * @return JEventDispatcher
     */
    protected function getDispatcher()
    {
        JPluginHelper::importPlugin('pixpublish');
        $dispatcher = JEventDispatcher::getInstance();

        return $dispatcher;
    }
}
