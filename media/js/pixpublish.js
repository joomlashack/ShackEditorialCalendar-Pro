// Active plugins
var PixPublishPlugins = new Array();

jQuery(function($) {
	$.noConflict();
	
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
				left: 'prev,next, today',
				center: 'title',
				right: 'month'
			},
			editable: true,
			firstDay: 1,
			timeFormat: 'H:mm',
			events:
			{
				url: $('#calendar').data('base-url') + "&task=panel.getdata",
				data: function()
				{
					return { data: JSON.stringify( $('#pixpublish_search').serializeObject() ) };
				},
				error: function(a,b,c)
				{
					console.log(a.responseText);
	                alert( 'there was an error while fetching events!' );
	            },
			},
			eventRender: function(event, element) {
				element.addClass('pp-state-' + event.state);
			},
			allDayDefault: false,
			eventResize: function(event,dayDelta,minuteDelta,revertFunc)
			{
				var url = $('#calendar').data('base-url') + "&task=panel.updateEndTime" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;

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
		        var url = $('#calendar').data('base-url') + "&task=panel.move" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;
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
			   Messi.load( $('#calendar').data('base-url') + "&task=panel.edit"  + '&id=' + calEvent.id + "&plugin=" + calEvent.plugin,
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
						   var url = $('#calendar').data('base-url') + "&task=panel.save" + "&id=" + calEvent.id + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + calEvent.plugin + "&title=" + $('#pixtest_title').val() + "&data=" + form_data;
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
		}).pixifyCalendar();

	    $('#timepicker1').timepicker({
	        template: false,
	        showInputs: false,
	        minuteStep: 5,
	        defaultTime: false,
	        showMeridian: false,
	    });

	    $('#pixpublish_search_submit').click(function() {
	    	$('#calendar').fullCalendar( 'refetchEvents' );
	    });

	    $( ".pixpublish_trigger" ).change(function() {
			$('#calendar').fullCalendar( 'refetchEvents' );
	    });
		
		// run pixifyCalendar when calendar view is changed
		$('.fc-button').click(function() {
			$('#calendar').pixifyCalendar();
		});

		// activate plugin new buttons
		$('.pp-new').live('click', function(calEvent) {
			var date = $(this).parent().parent().parent().data('date');
			var plugin = $(this).data('plugin');

			Messi.load( $('#calendar').data('base-url') + "&task=panel.create&plugin=" + plugin,
			{
			   title: 'New',
			   modal: true,
			   unload : false,
			   buttons: [{id: 0, label: 'Save', val: 'Y', class: 'btn-success'}, {id: 1, label: 'Cancel', val: 'N'}],
			   callback: function(val)
			   {
				   var form_data = JSON.stringify( $('#pixsubmit_form').serializeObject() );
				   if( val == 'Y' )
				   {
					   var url = $('#calendar').data('base-url') + "&task=panel.savecreated" + "&mind=" + 0 + "&plugin=" + plugin + "&date=" + date + "&data=" + form_data;
					   $.ajax({
				            url: url,
				            success: function(response){
				                $('#calendar').fullCalendar( 'refetchEvents' );
								//console.log(response);
				            },
				            error: function(jqXHR, textStatus, errorThrown){
								revertFunc(); // TODO: Does't exsist...
								//console.log(errorThrown);
				            }
					   });
				   }
				   else
				   {
					   
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
					
					// Bootstrap tabs
					$('#pixTabContent > div').each( function (){
						var li_class = '';
						if ( $(this).hasClass('active') ) {
							li_class = ' class="active"';
						}
						$('#pixTabTabs').append('<li' + li_class + '><a href="#' + $(this).attr('id') + '" data-toggle="tab" style="text-transform:capitalize;">' + $(this).attr('id').substring(3) + '</a></li>');
					});
				}
			});			
		});
	
	// End Document Ready
	});
	
	$.fn.pixifyCalendar = function()
	{
		// create day header
		$(this).find('.fc-day .fc-day-number').wrap('<div class="pp-day-head clearfix"></div>');

		// add plugin new buttons
		for ( var i = 0; i < PixPublishPlugins.length; i++ ) {
			var method = PixPublishPlugins[i];
			$(this)[method]();
		}

		return this;
	}

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

});