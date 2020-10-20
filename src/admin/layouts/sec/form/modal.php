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

use Joomla\CMS\Form\Form;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die();

/**
 * @var FileLayout $this
 * @var array      $displayData
 * @var string     $layoutOutput
 * @var string     $path
 * @var Form       $form
 */
extract($displayData);

$fieldsets = $form->getFieldsets();
?>
    <form action="" method="post" id="pixsubmit_form">
        <?php
        foreach ($fieldsets as $fieldset) :
            echo $form->renderFieldset($fieldset->name);
        endforeach;
        ?>
    </form>
<?php

$lines = array();
foreach ($form->getFieldset() as $row) :
    if ($row instanceof SecFormFieldEditor) :
        $lines[] = $row->save();
        $row->getInit();
    endif;
endforeach;

if ($lines = array_filter($lines)) :
    $jScript = array('function toggleMe() {');
    foreach ($lines as $row) {
        $jScript[] = $row;
    }
    $jScript[] = '}';

    echo sprintf('<script>%s</script>', join("\n", $jScript));
endif;
