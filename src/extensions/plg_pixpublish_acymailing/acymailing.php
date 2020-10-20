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

use Joomla\CMS\Date\Date;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();

require_once JPATH_ADMINISTRATOR . '/components/com_pixpublish/include.php';

class PlgPixPublishAcymailing extends PixPublishPlugin implements InterfacePixPublishPlugin
{
    protected $autoloadLanguage = true;

    /**
     * @param Date   $start
     * @param Date   $stop
     * @param object $data
     *
     * @return ColorFixer[]
     */
    public function onDataFetch($start, $stop, $data)
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                array(
                    'tbl.mailid AS id',
                    'tbl.subject AS title',
                    'tbl.senddate AS start',
                    $db->quote($this->getName()) . ' as plugin'
                )
            )
            ->from('#__acymailing_mail tbl')
            ->where(
                array(
                    'tbl.senddate >= ' . $db->quote($start->toSql()),
                    'tbl.senddate <= ' . $db->quote($stop->toSql())
                )
            );

        ColorFixer::$st_color = $this->params->get('background_colour', '#3a87ad');

        /** @var ColorFixer[] $result */
        $result = $db->setQuery($query)->loadObjectList('', 'ColorFixer');
        $result = static::fixDates($result, 'start');

        return $result;
    }

    /**
     * @param string $source
     * @param string $id
     * @param int    $dayd
     * @param int    $mind
     *
     * @return bool
     */
    public function onItemMove($source, $id, $dayd, $mind)
    {
        if ($source === $this->getName()) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->update('#__acymailing_mail')
                ->set(
                    sprintf(
                        'senddate = UNIX_TIMESTAMP(DATE_ADD(ADDDATE(FROM_UNIXTIME(senddate), %s), INTERVAL %s MINUTE))',
                        (int)$dayd,
                        $db->quote((int)$mind)
                    )
                )
                ->where('mailid = ' . (int)$id);

            if (!$db->setQuery($query)->execute()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $source
     * @param int    $id
     * @param Form   $form
     *
     * @return object
     */
    public function onGetDialog($source, $id, $form)
    {
        if ($source === $this->getName()) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->select(
                    array(
                        'tbl.mailid AS id',
                        'tbl.subject AS title',
                        'tbl.senddate AS start',
                        $db->quote($this->getName()) . ' AS plugin'
                    )
                )
                ->from('#__acymailing_mail tbl')
                ->where('tbl.mailid = ' . (int)$id);

            if ($result = $db->setQuery($query)->loadObject()) {
                $arr = array($result);
                $arr = self::fixDates($arr, 'start');

                $result = $arr[0];
            }

            JForm::addFormPath(__DIR__ . '/form');
            $form->loadFile('form', false);

            return $result;
        }

        return null;
    }

    /**
     * @param string $source
     * @param int    $id
     * @param object $data
     *
     * @return bool
     */
    public function onItemSave($source, $id, $data)
    {
        $id    = (int)$id;
        $title = empty($data->title) ? null : trim($data->title);
        $time  = empty($data->time) ? null : $data->time;

        if ($source === $this->getName()
            && $id > 0
            && ($title || $time)
        ) {
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                ->update('#__acymailing_mail')
                ->where('mailid = ' . $id);

            if ($title) {
                $query->set('subject = ' . $db->quote($title));
            }

            if ($time) {
                $time = acymailing_getTime($time);
                $query->set(
                    sprintf(
                        'senddate = UNIX_TIMESTAMP(TIMESTAMP(DATE(FROM_UNIXTIME(senddate)), TIME(FROM_UNIXTIME(%s))))',
                        $db->quote($time)
                    )
                );
            }

            return (bool)$db->setQuery($query)->execute();
        }

        return true;
    }

    /**
     * @return void
     */
    public function onRegisterSearchFilters()
    {
        JFactory::getDocument()
            ->addScriptDeclaration(
                sprintf(
                    "PLUGIN['acymailing'] = '%s';",
                    JText::_('PLG_PIXPUBLISH_ACYMAILING_TYPE_NAME')
                )
            );

        JHtml::_('script', 'plugins/pixpublish/acymailing/media/js/acymailing.js');
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return 'acymailing';
    }

    /**
     * @param object[] $arr
     * @param string   $fieldname
     *
     * @return object[]
     */
    protected static function fixDates(&$arr, $fieldname)
    {
        foreach ($arr as $row) {
            $row->$fieldname = acymailing_getDate($row->$fieldname, 'Y-m-d H:i:s');
        }

        return $arr;
    }
}
