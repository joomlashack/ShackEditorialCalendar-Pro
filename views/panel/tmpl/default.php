<?php
/**
* @copyright	Copyright (C) 2013 Johan Sundell. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;
?>
<script type="text/javascript">
<!--
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
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		firstDay: 1,
		/*events:
		[
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
		]*/
		events: "<?php echo JRoute::_( 'index.php?option=com_pixpublish&format=json&task=panel.getdata', false );?>",
		allDayDefault: false,
		eventResize: function(event,dayDelta,minuteDelta,revertFunc)
		{
			var url = "<?php echo JRoute::_( 'index.php?option=com_pixpublish&format=json&task=panel.updateEndTime', false ); ?>" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;

	        $.ajax({
	            url: url,
	            success: function(){
	                $('#calendar').fullCalendar( 'refetchEvents' );
	            },
	            error: function(){
	                //$('#calendar').fullCalendar( 'refetchEvents' );
	                revertFunc();
	            }
	        });

	        /*alert(
	            "The end date of " + event.title + "has been moved " +
	            dayDelta + " days and " +
	            minuteDelta + " minutes."
	        );

	        if (!confirm("is this okay?"))
		    {
	            revertFunc();
	        }*/

	    },
	    eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc)
	    {

	        /*alert(
	    	    event.id + " id " +
	            event.title + " was moved " +
	            dayDelta + " days and " +
	            minuteDelta + " minutes."
	        );*/
	        //console.debug( event );
	        var url = "<?php echo JRoute::_( 'index.php?option=com_pixpublish&format=json&task=panel.move', false ); ?>" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;

	        $.ajax({
	            url: url,
	            success: function(){
	                $('#calendar').fullCalendar( 'refetchEvents' );
	            },
	            error: function(){
	                //$('#calendar').fullCalendar( 'refetchEvents' );
	                revertFunc();
	            }
	        });
	        
	        /*if (allDay) {
	            alert("Event is now all-day");
	        }else{
	            alert("Event has a time-of-day");
	        }

	        if (!confirm("Are you sure about this change?")) {
	            revertFunc();
	        }*/

	    },
	});
});
}(jQuery));
//-->
</script>
<div id='calendar' style='margin:3em 0;font-size:13px'>
	test
</div>
