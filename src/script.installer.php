<?php
/**
 * @package   ShackEditorialCalendarPro
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2018 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of ShackEditorialCalendarPro.
 *
 * ShackEditorialCalendarPro is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * ShackEditorialCalendarPro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ShackEditorialCalendarPro.  If not, see <http://www.gnu.org/licenses/>.
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
