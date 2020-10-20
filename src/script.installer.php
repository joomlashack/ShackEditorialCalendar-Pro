<?php
/**
 * @package   ShackEditorialCalendar-Pro
 * @contact   www.joomlashack.com, help@joomlashack.com
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

use Alledia\Installer\AbstractScript;

defined('_JEXEC') or die();

// Adapt for install and uninstall environments
if (file_exists(__DIR__ . '/admin/library/Installer/AbstractScript.php')) {
    require_once __DIR__ . '/admin/library/Installer/AbstractScript.php';
} else {
    require_once __DIR__ . '/library/Installer/AbstractScript.php';
}

/**
 * Custom installer script
 */
class Com_PixpublishInstallerScript extends AbstractScript
{
    public function postFlight($type, $parent)
    {
        switch ($type) {
            case 'update':
                $this->removeObsoleteFiles();
                break;
        }

        parent::postFlight($type, $parent);
    }

    /**
     * Removes all obsolete files that are easier to remove here rather than in the manifest
     */
    protected function removeObsoleteFiles()
    {
        $files = JFolder::files(JPATH_ADMINISTRATOR . '/language', '.*pixpublish.*', true, true);

        foreach ($files as $file) {
            JFile::delete($file);
        }
    }
}
