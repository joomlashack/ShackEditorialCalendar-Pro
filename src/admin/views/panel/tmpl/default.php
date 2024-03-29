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

defined('_JEXEC') or die();
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal');

$baseUrl   = JRoute::_('index.php?option=com_pixpublish&format=json', false);
$mainClass = empty($this->siderbar) ? '' : 'span10';

if (!empty($this->sidebar)) :
    ?>
    <div id="j-sidebar-container" class="span2">
        <form id="pixpublish_search" method="POST" onsubmit=""
              action="javascript:(function($) { $('#calendar').fullCalendar( 'refetchEvents' ); }(jQuery));">
            <?php echo $this->sidebar; ?>
        </form>
    </div>
<?php endif; ?>
<div id="j-main-container" class="<?php echo $mainClass; ?>">
    <?php
    if (count($this->infotexts) > 0) : ?>
        <div>
            <?php
            foreach ($this->infotexts as $row) :
                echo $row;
            endforeach;
            ?>
        </div>
    <?php endif; ?>
    <div id="calendar" style="margin:3em 0;font-size:13px" data-base-url="<?php echo $baseUrl; ?>">
    </div>
</div>

<div style="display: none;" id="sudde">
    <?php
    $editor = JEditor::getInstance(JFactory::getApplication()->get('editor'));

    if ($editor->get('_name') != 'jce') {
        echo $editor->display('content_sudde', '', '0', '0', '0', '0', false);
    }
    ?>
</div>
