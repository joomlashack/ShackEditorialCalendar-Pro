<?php
/**
 * @package         ShackEditorialCalendarfree
 * @copyright       Copyright (C) 2018. All rights reserved.
 * @link            https://www.joomlashack.com/joomla-extensions/shack-editorial-calendar/
 * @author       	You Rock AB 2003-2017 All Rights Reserved
 * @author       	2018, Joomlashack <help@joomlashack.com> - https://www.joomlashack.com.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal');

$base_url = JRoute::_( 'index.php?option=com_pixpublishfree&format=json', false );
$ed = JFactory::getEditor();
?>
<script type="text/javascript">
<!--

//-->
</script>
<?php if( !empty( $this->sidebar ) ) : ?>
<div id="j-sidebar-container" class="span2">
	<form id="pixpublishfree_search" method="POST" onsubmit="" action="javascript:(function($) { $('#calendar').fullCalendar( 'refetchEvents' ); }(jQuery));">
	<?php echo $this->sidebar; ?>
	</form>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif;?>
	<?php echo JText::_('COM_PIXPUBLISHFREE_INFOTEXT'); ?>
	<div id='calendar' style='margin:3em 0;font-size:13px' data-base-url="<?php echo $base_url; ?>">
	</div>
</div>
<div style="display: none;">
<?php
if( $ed->get( '_name' ) != 'jce' )
	echo $ed->display('content_sudde', $this->content, '0', '0', '0', '0', false);
?>
</div>

