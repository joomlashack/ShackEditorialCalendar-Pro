<?php
/**
* @author		Johan Sundell <labs@pixpro.net>
* @link			https://www.pixpro.net/labs
* @copyright	Copyright © You Rock AB 2003-2017 All Rights Reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal');

$base_url = JRoute::_( 'index.php?option=com_pixpublish&format=json', false );
$ed = JFactory::getEditor();
?>
<script type="text/javascript">
<!--

//-->
</script>
<?php if( !empty( $this->sidebar ) ) : ?>
<div id="j-sidebar-container" class="span2">
	<form id="pixpublish_search" method="POST" onsubmit="" action="javascript:(function($) { $('#calendar').fullCalendar( 'refetchEvents' ); }(jQuery));">
	<?php echo $this->sidebar; ?>
	</form>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif;?>
	<?php if( count( $this->infotexts ) > 0 ):?>
	<div>
		<?php foreach( $this->infotexts as $row ): ?>
		<?php echo $row;?>
		<?php endforeach; ?>
	</div>
	<?php endif;?>
	<div id='calendar' style='margin:3em 0;font-size:13px' data-base-url="<?php echo $base_url; ?>">
	</div>
</div>
<div style="display: none;" id="sudde">
<?php
if( $ed->get( '_name' ) != 'jce' )
	echo $ed->display('content_sudde', '', '0', '0', '0', '0', false);
?>
</div>

