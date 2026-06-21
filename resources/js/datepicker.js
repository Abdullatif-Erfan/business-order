// Date Picker Configuration
(function($) {
    'use strict';

    // Gregorian Date Picker
    $.fn.gregorianDatePicker = function(options) {
        var settings = $.extend({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom',
            language: 'en',
            weekStart: 1,
            startDate: '1900-01-01',
            endDate: '2100-12-31',
            clearBtn: true,
            todayBtn: true,
            container: 'body'
        }, options);

        return this.each(function() {
            var $input = $(this);
            
            // Check if datepicker already initialized
            if ($input.data('datepicker')) {
                return;
            }

            $input.datepicker(settings);

            // Trigger datepicker on icon click
            var $wrapper = $input.closest('.date-picker-wrapper');
            $wrapper.find('.date-picker-icon').on('click', function(e) {
                e.preventDefault();
                $input.datepicker('show');
            });

            // Format date on change
            $input.on('changeDate', function(e) {
                var formattedDate = e.format(0, settings.format);
                $input.val(formattedDate);
                $input.trigger('change');
            });

            // Store instance for later use
            $input.data('datepicker-instance', $input.data('datepicker'));
        });
    };

    // Auto-initialize all date pickers
    $(document).ready(function() {
        $('.date-picker-input').each(function() {
            var $this = $(this);
            var format = $this.data('format') || 'yyyy-mm-dd';
            
            $this.gregorianDatePicker({
                format: format
            });
        });
    });

})(jQuery);