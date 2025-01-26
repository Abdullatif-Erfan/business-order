</div><!-- /wrapper -->
<!-- Core JS Files -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/custom.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
<script src="{{ asset('assets/plugin/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- Uncomment if needed -->
<!-- 
<script src="{{ asset('assets/plugin/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/plugin/datatable/js/dataTables.bootstrap.js') }}"></script>
-->

<script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.responsive.js') }}"></script>

<!-- Persian Date Picker -->
<script type="text/javascript">
    $('#input1').change(function () {
        var $this = $(this), value = $this.val();
        alert(value);
    });
    $('#textbox1').change(function () {
        var $this = $(this), value = $this.val();
        alert(value);
    });
    $('[data-name="disable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
    });
    $('[data-name="enable-button"]').click(function () {
        $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
    });
</script>
<script src="{{ asset('assets/datepicker/jalaali.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $('#example2 thead tr#filterrow td').each(function () {
        var title = $('#example2 thead td').eq($(this).index()).text();
        $(this).html('<input type="text" class="form-control" onclick="stopPropagation(event);" placeholder="جستجو ' + title + '" />');
    });
    var table = $('#example2').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "همه"]],
        "pageLength": 12
    });
    $("#example2 thead input").on('keyup change', function () {
        table
            .column($(this).parent().index() + ':visible')
            .search(this.value)
            .draw();
    });

    function stopPropagation(evt) {
        if (evt.stopPropagation !== undefined) {
            evt.stopPropagation();
        } else {
            evt.cancelBubble = true;
        }
    }

    $(function () {
        $(".select2").select2();
    });
</script>
<script type="text/javascript">
    var table = $('#example').DataTable();
    var table = $('#example3').DataTable();
    var table = $('#example4').DataTable();
    var table = $('#example5').DataTable();
    var table = $('#example6').DataTable();
    var table = $('#example7').DataTable();
    var table = $('#example8').DataTable();
    var table = $('#example9').DataTable();
    var table = $('#table').DataTable();
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navItems = document.querySelectorAll('.nav-item');

        // Function to set active class based on stored value
        function setActiveClass() {
            const activeItem = localStorage.getItem('activeNavItem');
            if (activeItem) {
                navItems.forEach(item => {
                    if (item.querySelector('a').getAttribute('href') === activeItem) {
                        item.classList.add('active');
                    }
                });
            }
        }

        // Set active class on page load
        setActiveClass();

        navItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove 'active' class from all items
                navItems.forEach(nav => nav.classList.remove('active'));
                // Add 'active' class to the clicked item
                this.classList.add('active');

                // Store the href of the active item in localStorage
                const activeHref = this.querySelector('a').getAttribute('href');
                localStorage.setItem('activeNavItem', activeHref);
            });
        });
    });
</script>

</body>
</html>
