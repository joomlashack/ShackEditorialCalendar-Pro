// Active plugins
var PixPublishPlugins = [];

jQuery(function($) {
    $(document).ready(function() {
        var $calendar = $('#calendar');

        $calendar.fullCalendar({
            header       : {
                left  : 'prev,next, today',
                center: 'title',
                right : 'month'
            },
            editable     : true,
            firstDay     : 1,
            timeFormat   : 'H:mm',
            events       : {
                url  : $calendar.data('base-url') + "&task=panel.getdata",
                data : function() {
                    return {data: JSON.stringify($('#pixpublish_search').serializeObject())};
                },
                error: function(xhr, status, error) {
                    alert('Fetch Events Error: ' + error);
                },
            },
            eventRender  : function(event, element) {
                element.addClass('pp-state-' + event.state);
            },
            allDayDefault: false,
            eventResize  : function(event, dayDelta, minuteDelta, revertFunc) {
                var url = $calendar.data('base-url') + "&task=panel.updateEndTime" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;

                $.ajax({
                    url    : url,
                    success: function() {
                        $calendar.fullCalendar('refetchEvents');
                    },
                    error  : function() {
                        revertFunc();
                    }
                });
            },
            eventDrop    : function(event, dayDelta, minuteDelta, allDay, revertFunc) {
                var url = $calendar.data('base-url') + "&task=panel.move" + "&id=" + event.id + "&dayd=" + dayDelta + "&mind=" + minuteDelta + "&plugin=" + event.plugin;
                $.ajax({
                    url    : url,
                    success: function() {
                        $calendar.fullCalendar('refetchEvents');
                    },
                    error  : function() {
                        revertFunc();
                    }
                });
            },
            eventClick   : function(calEvent, jsEvent, view) {
                Messi.load($calendar.data('base-url') + "&task=panel.edit" + '&id=' + calEvent.id + "&plugin=" + calEvent.plugin,
                    {
                        title   : Joomla.JText._('COM_PIXPUBLISH_EDIT') + ' ' + PLUGIN[calEvent.plugin].toLowerCase(),
                        modal   : true,
                        unload  : false,
                        buttons : [{id: 0, label: Joomla.JText._('JSAVE'), val: 'Y', class: 'btn-success'}, {
                            id   : 1,
                            label: Joomla.JText._('JCANCEL'),
                            val  : 'N'
                        }],
                        callback: function(val) {
                            if (typeof (toggleMe) == 'function') {
                                try {
                                    toggleMe();
                                } catch (ex) {
                                }
                            }

                            if (val === 'Y') {
                                var url = $calendar.data('base-url'); // + "&task=panel.save" + "&id=" + calEvent.id + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + calEvent.plugin + "&title=" + $('#pixtest_title').val() + "&" + $.param( { data: JSON.stringify( $('#pixsubmit_form').serializeObject() ) } );
                                $.ajax({
                                    url    : url,
                                    method : "POST",
                                    data   : "&task=panel.save" + "&id=" + calEvent.id + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + calEvent.plugin + "&title=" + $('#pixtest_title').val() + "&" + $.param({data: JSON.stringify($('#pixsubmit_form').serializeObject())}),
                                    success: function() {
                                        $calendar.fullCalendar('refetchEvents');
                                    }
                                });
                            }
                        },
                        onopen  : function() {
                            $('.timepicker').timepicker({
                                template    : false,
                                showInputs  : false,
                                minuteStep  : 5,
                                defaultTime : false,
                                showMeridian: false,
                            });
                        }
                    });
            },
        }).pixifyCalendar();

        $('#timepicker1').timepicker({
            template    : false,
            showInputs  : false,
            minuteStep  : 5,
            defaultTime : false,
            showMeridian: false,
        });

        $('#pixpublish_search_submit').click(function() {
            $calendar.fullCalendar('refetchEvents');
        });

        $(".pixpublish_trigger").change(function() {
            $calendar.fullCalendar('refetchEvents');
        });

        // run pixifyCalendar when calendar view is changed
        $('.fc-button').click(function() {
            $calendar.pixifyCalendar();
        });

        // activate plugin new buttons
        $('.pp-new').live('click', function(calEvent) {
            var date = $(this).parent().parent().parent().data('date');
            var plugin = $(this).data('plugin');

            Messi.load($calendar.data('base-url') + "&task=panel.edit" + '&id=' + 0 + "&plugin=" + plugin,
                {
                    title   : Joomla.JText._('COM_PIXPUBLISH_ADD_NEW') + ' ' + PLUGIN[plugin].toLowerCase(),
                    modal   : true,
                    unload  : false,
                    buttons : [{id: 0, label: Joomla.JText._('JSAVE'), val: 'Y', class: 'btn-success'}, {
                        id   : 1,
                        label: Joomla.JText._('JCANCEL'),
                        val  : 'N'
                    }],
                    callback: function(val) {
                        if (typeof (toggleMe) == 'function') {
                            try {
                                toggleMe();
                            } catch (ex) {
                            }
                        }

                        var data = $('#pixsubmit_form').serializeObject();
                        data["publish_up"] = date;

                        if (val === 'Y') {
                            var url = $calendar.data('base-url');
                            $.ajax({
                                url    : url,
                                method : "POST",
                                data   : "&task=panel.save" + "&id=" + 0 + "&start=" + $('#pixtest_start').val() + "&mind=" + 0 + "&plugin=" + plugin + "&title=" + $('#pixtest_title').val() + "&" + $.param({data: JSON.stringify(data)}),
                                success: function() {
                                    $calendar.fullCalendar('refetchEvents');
                                }
                            });
                        }
                    },
                    onopen  : function() {
                        $('.timepicker').timepicker({
                            template    : false,
                            showInputs  : false,
                            minuteStep  : 5,
                            defaultTime : false,
                            showMeridian: false,
                        });
                    }
                });
        });

        // Activate Bootstrap Tooltip
        $('.hasTooltip').tooltip({"html": true, "container": "body"});
    }); // End Document Ready

    $.fn.pixifyCalendar = function() {
        if (!$(this).find('.fc-today .fc-day-number').parent().hasClass('pp-day-head')) {
            // create day header
            $(this).find('.fc-day .fc-day-number').wrap('<div class="pp-day-head clearfix"></div>');

            // add plugin new buttons
            for (var i = 0; i < PixPublishPlugins.length; i++) {
                var method = PixPublishPlugins[i];
                $(this)[method]();
            }
        }

        return this;
    };

    $.fn.serializeObject = function() {
        var o = {},
            a = this.serializeArray();

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

        return o;
    };
});
