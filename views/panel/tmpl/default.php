<?php
/**
 * @package         PixPublishfree
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal');

$base_url = JRoute::_( 'index.php?option=com_pixpublishfree&format=json', false );

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
	<div id='calendar' style='margin:3em 0;font-size:13px' data-base-url="<?php echo $base_url; ?>">
	</div>
</div>


