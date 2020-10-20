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

defined('_JEXEC') or die();

if (!defined('SECAL_LOADED')) {
    define('SECAL_LOADED', true);

    define('SECAL_ADMIN', JPATH_ADMINISTRATOR . '/components/com_pixpublish');
    define('SECAL_CLASSES', SECAL_ADMIN . '/classes');
    define('SECAL_HELPERS', SECAL_ADMIN . '/helpers');
    define('SECAL_LAYOUTS', SECAL_ADMIN . '/layouts');

    JLoader::register('PixEditor', SECAL_HELPERS . '/pixeditor.php');
    JLoader::register('PixPublishHelper', SECAL_HELPERS . '/pixpublish.php');

    JLoader::register('InterfacePixPublishPlugin', SECAL_CLASSES . '/InterfacePixPublishPlugin.php');
    JLoader::register('PixPublishPlugin', SECAL_CLASSES . '/pixpublishplugin.php');
    JLoader::register('ColorFixer', SECAL_CLASSES . '/ColorFixer.php');

    JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_pixpublish/models/fields');

    $acymailing = JPATH_ADMINISTRATOR . '/components/com_acymailing';
    define('SECAL_ACYMAILING', is_dir($acymailing));
    if (SECAL_ACYMAILING) {
        JLoader::import('helper', $acymailing);
    }
    unset($acymailing);

}
