<?php $bul = base_url(); ?>
	<!--   Core JS Files   -->
<style>
 /* .select2-container--open .select2-dropdown--below{margin-left: 0px !important;} */
 .select2-container--open .select2-dropdown--below{margin-left: -8px;}
</style>
	<script src="<?php echo $bul; ?>assets/plugin/select2/select2.full.min.js"></script>  
    <script src="<?php echo $bul; ?>assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/jquery.dataTables.js"></script> 
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/dataTables.bootstrap.js"></script>
<!-- for persian date_picker -->
    <script type="text/javascript">
        $('#input1').change(function() {  var $this = $(this), value = $this.val();  alert(value);});
        $('#textbox1').change(function () {  var $this = $(this),  value = $this.val(); alert(value); });
        $('[data-name="disable-button"]').click(function() {
            $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
        });
        $('[data-name="enable-button"]').click(function () {
            $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
        });
    </script>
    <script src="<?php echo base_url(); ?>assets/datepicker/jalaali.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js" type="text/javascript"></script>
<!-- / p date picker -->


    <script type="text/javascript">
   $('#example2 thead tr#filterrow td').each( function () {
        var title = $('#example2 thead td').eq( $(this).index() ).text();
        $(this).html( '<input type="text" class="form-control" onclick="stopPropagation(event);" placeholder="جستجو '+title+'" />' );
    } );
     var table =  $('#example2').DataTable({
        "lengthMenu" : [[10, 25, 50, -1 ], [10, 25, 50, "همه"]], "pageLength" : 12
    });
    $("#example2 thead input").on( 'keyup change', function () {
        table
            .column( $(this).parent().index()+':visible' )
            .search( this.value )
            .draw();
    } );

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
    var table = $('#example10').DataTable();
</script>






