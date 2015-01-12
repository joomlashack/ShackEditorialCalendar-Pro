<?php
/**
 * @package         PixPublish
 * @author          Johan Sundell <johan@pixpro.net>
 * @link            http://www.pixpro.net/labs
 * @copyright       Copyright ©2014-2015 Pixpro Stockholm AB All Rights Reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$base_url = JRoute::_( 'index.php?option=com_pixpublish&format=json', false );
$script = <<<SCRIPT
(function($) {
$(document).ready( function()
{
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	$('#calendar').fullCalendar(
	{
		header:
		{
			left: 'prev,next today',
			center: 'title',
			right: 'month'
		},
		editable: true,
		firstDay: 1,
		timeFormat: 'H(:mm)',
		events:
		{
			url: "$base_url&task=panel.getdata",
			data: function()
			{
				return { data: JSON.stringify( $('#pixpublish_search').serializeObject() ) };
			},
			error: function()
			{
                alert( 'there was an error while fetching events!' );
            },
		},
		allDayDefault: false,
		eventResize: function(event,dayDelta,minuteDelta,revertFunc)
		{
			var url = "$base_url&task=panel.updateEndTime" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;

	        $.ajax({
	            url: url,
	            success: function(){
	                $('#calendar').fullCalendar( 'refetchEvents' );
	            },
	            error: function(){
	                revertFunc();
	            }
	        });
	    },
	    eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc)
	    {
	        var url = "$base_url&task=panel.move" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;
	        $.ajax({
	            url: url,
	            success: function(){
	                $('#calendar').fullCalendar( 'refetchEvents' );
	            },
	            error: function(){
	                revertFunc();
	            }
	        });
	    },
	    eventClick: function(calEvent, jsEvent, view)
	    {
		   Messi.load( "$base_url&task=panel.edit"  + '&id=' + calEvent.id + "&plugin=" + calEvent.plugin,
			{
			   title: 'Edit',
			   modal: true,
			   unload : false,
			   buttons: [{id: 0, label: 'Save', val: 'Y', class: 'btn-success'}, {id: 1, label: 'Cancel', val: 'N'}],
			   callback: function(val)
			   {
				   var form_data = JSON.stringify( $('#pixsubmit_form').serializeObject() );
				   if( val == 'Y' )
				   {
					   var url = "$base_url&task=panel.save" + "&id=" + calEvent.id + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + calEvent.plugin + "&title=" + $('#pixtest_title').val() + "&data=" + form_data;
					   $.ajax({
				            url: url,
				            success: function(){
				                $('#calendar').fullCalendar( 'refetchEvents' );
				            },
				            error: function(){
				                revertFunc();
				            }
					   });
				   }
				},
				onopen: function()
				{
					$('.timepicker').timepicker({
						template: false,
						showInputs: false,
						minuteStep: 5,
						defaultTime: false,
						showMeridian: false,
					});
				}
			});
	    },
	});

    $('#timepicker1').timepicker({
        template: false,
        showInputs: false,
        minuteStep: 5,
        defaultTime: false,
        showMeridian: false,
    });


    $('#pixpublish_search_submit').click( function()
    {
    	$('#calendar').fullCalendar( 'refetchEvents' );
    });

    $( ".pixpublish_trigger" ).change(function() {
    	 $('#calendar').fullCalendar( 'refetchEvents' );
    });
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    //console.debug( o );
    return o;
};

}(jQuery));

SCRIPT;

JFactory::getDocument()->addScriptDeclaration( $script );
?>

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
	<div id='calendar' style='margin:3em 0;font-size:13px'>
	</div>
</div>


