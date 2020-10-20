<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2019-2020 Joomlashack.com. All rights reserved
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

interface InterfacePixPublishPlugin
{
    /**
     * @param Date   $start
     * @param Date   $stop
     * @param object $data
     *
     * @return ColorFixer[]
     */
    public function onDataFetch($start, $stop, $data);

    /**
     * @param string $source
     * @param string $id
     * @param int    $dayd
     * @param int    $mind
     *
     * @return bool
     */
    public function onItemMove($source, $id, $dayd, $mind);

    /**
     * @param string $source
     * @param int    $id
     * @param Form   $form
     *
     * @return object
     */
    public function onGetDialog($source, $id, $form);

    /**
     * @param string $source
     * @param int    $id
     * @param object $data
     *
     * @return bool
     */
    public function onItemSave($source, $id, $data);

    /**
     * @return void
     */
    public function onRegisterSearchFilters();
}
