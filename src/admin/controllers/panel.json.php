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
        $input = JFactory::getApplication()->input;
        $start = $input->getUint('start', 0);
        $end   = $input->getUint('end', 0);
        $data  = json_decode($input->get('data', '', 'raw'));
        //$this->logThis( 'data: '.print_r( $data, true ) );
        $result = array();

        if ($start != 0 && $end != 0) {
            $dispatcher = $this->importPlugins();
            $results    = $dispatcher->trigger('onDataFetch',
                array(JDate::getInstance($start), JDate::getInstance($end), $data));

            $rows = array();
            foreach ($results as $result) {
                $rows = array_merge((array)$rows, (array)$result);
            }
        }
        echo json_encode($rows);
        JFactory::getApplication()->close();
    }

    public function updateEndTime()
    {
        JFactory::getApplication()->close();
    }

    public function move()
    {
        $input  = JFactory::getApplication()->input;
        $id     = $input->getCmd('id', '');
        $source = $input->getCmd('plugin', '');
        $dayd   = $input->getInt('dayd', 0);
        $mind   = $input->getInt('mind', 0);
        //$this->logThis( 'id:'.$id.' source:'.$source );

        $dispatcher = $this->importPlugins();
        $results    = $dispatcher->trigger('onItemMove', array($source, $id, $dayd, $mind, ''));

        //throw new Exception('Whoops, something happened!', 404);
        JFactory::getApplication()->close();
    }

    public function edit()
    {
        $input      = JFactory::getApplication()->input;
        $id         = $input->getCmd('id', '');
        $source     = $input->getCmd('plugin', '');
        $dispatcher = $this->importPlugins();
        $form       = new JForm('com_pixpublish');
        $extra      = '';
        $results    = $dispatcher->trigger('onGetDialog', array($source, $id, $form));
        $item       = null;
        //$this->logThis( 'sudde 1: '.$id );
        if (count($results) != 0 || (int)$id == 0) {
            if (count($results) != 0) {
                foreach ($results as $row) {
                    if ($row) {
                        $item = $row;
                    }
                }
            }

            //$this->logThis( 'sudde 2: '.$id );
            //$this->logThis( print_r( $item, true ) );
            if ($item != null || (int)$id == 0) {
                //$this->logThis( 'sudde 3: '.$id );
                $form->bind($item);

                // Output form (XML fieldsets must have name attribute set!)
                echo '<form action="" method="post" id="pixsubmit_form">';
                $fieldsets = $form->getFieldsets();
                foreach ($fieldsets as $fieldset) {
                    echo '<fieldset class="' . $fieldset->class . '">' . $form->renderFieldset($fieldset->name) . '</fieldset>';
                }
                echo '</form>';

                $lines = array();
                $inits = array();
                foreach ($form->getFieldset() as $row) {
                    if ($row->type == 'fixed') {
                        $lines[] = $row->save();
                        $inits[] = $row->getInit();
                        //$this->logThis( print_r( $row->getInit(), true ) );
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
            JFactory::getApplication()->close();
        }
        JFactory::getApplication()->close();
    }

    public function save()
    {
        $input  = JFactory::getApplication()->input;
        $id     = $input->getCmd('id', '');
        $source = $input->getCmd('plugin', '');
        $data   = json_decode(urldecode($input->get('data', '', 'raw')));
        $this->logThis(urldecode($input->get('data', '', 'raw')));

        $dispatcher = $this->importPlugins();
        $results    = $dispatcher->trigger('onItemSave', array($source, $id, $data));

        JFactory::getApplication()->close();
    }

    /**
     * @return JDispatcher
     */
    protected function importPlugins()
    {
        JPluginHelper::importPlugin('pixpublish');
        $dispatcher = JDispatcher::getInstance();
        return $dispatcher;
    }

    protected function logThis($message)
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
