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
		timeFormat: 'H(:mm)',
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
	    eventClick: function(calEvent, jsEvent, view)
	    {
		   // $(this).popbox();
		   Messi.load( "<?php echo JRoute::_( 'index.php?option=com_pixpublish&format=json&task=panel.test', false );?>"  + '&id=' + calEvent.id + "&plugin=" + calEvent.plugin,
			{
			   title: 'Modal Window',
			   modal: true,
			   unload : false,
			   buttons: [{id: 0, label: 'Save', val: 'Y', class: 'btn-success'}, {id: 1, label: 'Cancel', val: 'N'}],
			   callback: function(val)
			   {
				   console.debug( val );
				   //alert('Your selection: ' + val);
				   if( val == 'Y' )
				   {
					   console.debug( $('#pixtest_title').val() );
					   console.debug( $('#pixtest_start').val() );
					   var url = "<?php echo JRoute::_( 'index.php?option=com_pixpublish&format=json&task=panel.save', false ); ?>" + "&id=" + calEvent.id + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + calEvent.plugin + "&title=" + $('#pixtest_title').val();
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
				   }
				},
				onopen: function()
				{
					//console.debug( 'works;=)' );
					//$('#pixtest_title').val( 'johan' );
					 $('#pixtest_start').timepicker({
					        template: false,
					        showInputs: false,
					        minuteStep: 5,
					        defaultTime: false,
					        showMeridian: false,
					    });
					
				}
			});

	      /*  alert('Event: ' + calEvent.title);
	        alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
	        alert('View: ' + view.name);*/
	        // change the border color just for fun
	        //$(this).css('border-color', 'red');

	    },
	});

	// test
	//console.debug( $('#basic_example_1').datetimepicker() );
	//$('#timepicker1').timepicker();
	/* $('#timepicker1').timepicker({
         minuteStep: 1,
         template: 'modal',
         appendWidgetTo: 'body',
         showSeconds: true,
         showMeridian: false,
         defaultTime: false
     });*/

    $('#timepicker1').timepicker({
        template: false,
        showInputs: false,
        minuteStep: 5,
        defaultTime: false,
        showMeridian: false,
    });
});

}(jQuery));



//-->
</script>
<div class="input-append bootstrap-timepicker">
             <input id="timepicker1" type="text" class="input-small">
             <!-- <span class="add-on"><i class="icon-calendar"></i></span>-->
             <i class="icon-calendar"></i>
</div>

<div class='popbox'>
          <a class='open' href='#'>Click Here!</a>

          <div class='collapse'>
            <div class='box'>
              <div class='arrow'></div>
              <div class='arrow-border'></div>

              Content in PopBox goes here :)
              <a href="#" class="close">close</a>
            </div>
          </div>
        </div>

<div id='calendar' style='margin:3em 0;font-size:13px'>
	test
</div>

