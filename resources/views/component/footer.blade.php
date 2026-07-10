<!-- ========================================= -->
<!-- CORE JS FILES -->
<!-- ========================================= -->
<script src="{{ asset('assets/js/core/custom.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
<script src="{{ asset('assets/plugin/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- ========================================= -->
<!-- DATATABLES JS -->
<!-- ========================================= -->
<script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.responsive.js') }}"></script>

<!-- ========================================= -->
<!-- JS INITIALIZATION -->
<!-- ========================================= -->
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        if ($.fn.select2) {
            $(".select2").select2({
                width: '100%'
            });
        }
    });
</script>


 <script>
$(document).ready(function() {
    // Initialize all datepickers
    // function initDatepicker(selector) {
    //     $(selector).datepicker({
    //         format: 'yyyy-mm-dd',
    //         autoclose: true,
    //         todayHighlight: true,
    //         orientation: 'bottom auto',
    //         clearBtn: true,
    //         todayBtn: 'linked'
    //     });
    // }
    
    // Initialize specific datepickers
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom'
    });
    
    $('#start_date, #end_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true,
        todayBtn: 'linked'
    });
});
</script>
<script>
    function toggleFilterForm() {
    var elem = document.getElementById('searchWrapper');
    var icon = document.querySelector('.responsive_button i');
    
    if (!elem) return;
    
    // Toggle the 'filter-visible' class
    elem.classList.toggle('filter-visible');
    
    // Update icon
    if (elem.classList.contains('filter-visible')) {
        if (icon) icon.className = 'fas fa-times';
    } else {
        if (icon) icon.className = 'fas fa-filter';
    }
}
</script>



<script>
    function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
        var content = {};
        content.message = '<span style="font-size:16px;">' + message + '</span>';
        content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }} </span>';
        
        if (style === "withicon") {
            content.icon = 'fa fa-bell';
        } else {
            content.icon = 'none';
        }
        content.url = '#';
        content.target = '_blank';

        $.notify(content, {
            type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
            placement: {
                from: from, // top, bottom
                align: align // right, center, left
            },
            time: 500
        });
    }
</script>
        

<!-- ========================================= -->
<!-- CUSTOM SCRIPTS -->
<!-- ========================================= -->
@stack('footer-scripts')