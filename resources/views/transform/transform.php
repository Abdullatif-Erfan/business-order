<script>
	$(document).on('click','#edit',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:"<?php echo base_url() . 'inventory/inventory/editModalData'; ?>",
        success: function(result)
        {
            $("#EditData").html(result);
            jQuery("#edit_modal").modal('show');
        }
        });
    });
    
    $(document).on('click','#transfer',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        var account_id = $(this).data('id2');
        if(account_id==1){ show_stock_data(id); }
        else { show_item_data(id); }
    });
    function show_item_data(id)
    {
        $('#itemData').html('<center><img src="<?php echo base_url(); ?>assets/img/loader.gif" style="width:6%;margin:10%;" alt="Loading"/></center>');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:'<?php echo site_url("transform/transform/listForTransfer"); ?>',
        success: function(result)
        {
            $('#itemData').html(result); 
        },
            error: function (xhr, status) {
                $('#itemData').html('ایرور , به نسبت ضعیف بودن انترنت دیتا یافت نشد');
            }
        });
    }
    function show_stock_data(id)
    {
        $('#itemData').html('<center><img src="<?php echo base_url(); ?>assets/img/loader.gif" style="width:6%;margin:10%;" alt="Loading"/></center>');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:'<?php echo site_url("transform/transform/stockListForTransfer"); ?>',
        success: function(result)
        {
            $('#itemData').html(result); 
        },
            error: function (xhr, status) {
                $('#itemData').html('ایرور , به نسبت ضعیف بودن انترنت دیتا یافت نشد');
            }
        });
    }
    function show_item_type(bid)
    {
        $.ajax({
        type:'POST',
        data:{bid:bid},
        url:"<?php echo base_url() . 'transform/transform/show_item_type'; ?>",
        success: function(result)
        {
            $("#dynamic_branch").html(result);
        }
        });
    }
    function refresh()
    {
        window.location.reload();
    }
</script>
<script>
function show_item_list_modal_fromModalButton(bid)
{
  $('#itemData').html(''); 
  show_item_list(bid);
}
function show_item_list_modal(account_id)
{
//    if(account_id==1)
//    {
    // jQuery("#stockModal").modal('show');
//    } 
//    else {
    jQuery("#itemListModal").modal('show');
    show_item_list(account_id);
//    }
}
function show_item_list(account_id)
{
        $('#itemTable').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
		"bDestroy" : true,
        dom: 'lBfrtip',
        stateSave: true,
        buttons: [ {  
                    extend: 'print',
                    exportOptions: 
                    {
                     columns: [0,1,2,3,4,5,6] }
                    },
                 ],
            "ajax": {
            "url": "<?php echo site_url('transform/transform/item_list')?>",
            "type": "POST",
            "data": function ( data ) {
                data.account_id = account_id;	
            }
        },
        "columnDefs":[  
        {  
            "targets":[0,1,2,3,4,5,6],  
            "orderable":true,  
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 15,
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            // computing column Total of the complete result 
				
            var total = api .column( 5 ) .data()
                .reduce( function (a, b) {  return intVal(a) + intVal(b); }, 0 );
            // Update footer by showing the total with the reference of the column index 
	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('');
            $( api.column( 4 ).footer() ).html('');
            $( api.column( 5 ).footer() ).html(total);
			$( api.column( 6 ).footer() ).html('');
        },
    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
        $('#form-filter')[0].reset();
        table.ajax.reload();  
    });
    // $('#itemTable tfoot').each(function () {
    // $(this).insertAfter($(this).siblings('tbody'));
    //  });
    $('#itemTable tbody').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
     });

    localStorage.setItem('id',account_id);
}
 
function generalList()
   {   
        localStorage.setItem('id','1');
        localStorage.setItem('function',"general");

        $('#transferTable').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
		"bDestroy" : true,
        dom: 'lBfrtip',
        stateSave: true,
        buttons: [ {  
                    extend: 'print',
                    exportOptions: 
                    {
                     columns: [0,1,2,3,4,5,6] }
                    },
                 ],
            "ajax": {
            "url": "<?php echo site_url('transform/transform/transfer_list')?>",
            "type": "POST",
            "data": function ( data ) {	
            }
        },
        "columnDefs":[  
        {  
            "targets":[0,1,2,3,4,5,6],  
            "orderable":true,  
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 15,
    	"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            // computing column Total of the complete result 
				
            var total = api .column( 4 ) .data()
                .reduce( function (a, b) {  return intVal(a) + intVal(b); }, 0 );

             var income = api .column( 5 ) .data()
                .reduce( function (a, b) {  return intVal(a) + intVal(b); }, 0 );
            // Update footer by showing the total with the reference of the column index 
	        $( api.column( 0 ).footer() ).html('');
            $( api.column( 1 ).footer() ).html('');
            $( api.column( 2 ).footer() ).html('');
            $( api.column( 3 ).footer() ).html('');
            $( api.column( 4 ).footer() ).html(total);
            $( api.column( 5 ).footer() ).html(income);
			$( api.column( 6 ).footer() ).html('');
            $( api.column( 7 ).footer() ).html('');
            $( api.column( 8 ).footer() ).html('');
            $( api.column( 9 ).footer() ).html('');
        },
    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
        $('#form-filter')[0].reset();
        table.ajax.reload();  
    });
    $('table tfoot').each(function () {
    $(this).insertAfter($(this).siblings('tbody'));
     });

}
$(document).on('click','#selection',function(e){
        // e.preventDefault();
        var transfer_id=$(this).data('id');
        var no=$(this).data('id2');
        var input  = document.createElement("input");
        if(!document.getElementById('tid'+no))
        {
            var c = parseInt($('#counter').val());
            var counter = c+1;
            $('#counter').val(counter);
            input.setAttribute("type","text");
            input.setAttribute("name","tid"+counter);
            input.setAttribute("id","tid"+counter);
            input.setAttribute("value",transfer_id);
            document.getElementById("selectedFields").appendChild(input);
        } else {
            // $('#tid'+no).fadeOut(10);
            document.getElementById('tid'+no).remove();
            var c = parseInt($('#counter').val());
            if(c>=1){
                var counter = c-1;
                $('#counter').val(counter);
            } else  {
                $('#counter').val(0);
            }
        }
    });
    function submitForm()
    {
        var conf = confirm("آیا میخواهید بل ایجاد نمایید ؟");
        if(conf){ $('#myForm').submit(); }
    }
    function showBillsData()
    {
        localStorage.setItem('id','2');
        localStorage.setItem('function','bill');

        $('#billTable').DataTable({ 
        "processing": true, 
        "serverSide": true, 
        "order": [], 
		"bDestroy" : true,
        dom: 'lBfrtip',
        stateSave: true,
        buttons: [ {  
                    extend: 'print',
                    exportOptions: 
                    {
                     columns: [0,1,2,3,4,5,6] }
                    },
                 ],
            "ajax": {
            "url": "<?php echo site_url('transform/transform/bill_list')?>",
            "type": "POST",
            "data": function ( data ) {	
            }
        },
        "columnDefs":[  
        {  
            "targets":[0,1,2,3,4,5,6],  
            "orderable":true,  
        },  
        ],
        "lengthMenu" : [[10, 25, 50, 100, -1 ], [10, 25, 50, 100, "همه"]], "pageLength" : 15,
    });
    $('#btn-filter').click(function(){ 
        table.ajax.reload();  
    });
    $('#btn-reset').click(function(){ 
        $('#form-filter')[0].reset();
        table.ajax.reload();  
    });
    
    }

    $(document).on('click','#printBill',function(e){
        // e.preventDefault();
        var bill_no=$(this).data('id');
        window.location.href="<?php echo base_url(); ?>printBill/"+bill_no; 
    });
    $(document).on('click','#payMoney',function(e){
        var bill_no=$(this).data('id');
        var payed=$(this).data('id2');
        var total=$(this).data('id3');
        $('#bill_no').val(bill_no);
        $('#payed_before').val(payed);
        $('#total').val(total);
        jQuery("#pay_modal").modal('show');
    });
    function noGreater(value)
	{
		var oldpayed = parseInt($('#payed_before').val());
        var limit = parseInt($('#total').val());
        var curPrice = oldpayed + parseInt(value);
		if(value>=1) { if(curPrice<=limit)
        { $('#submitBtn').fadeIn(1); } else {  $('#submitBtn').fadeOut(1); alert('مقدار دریافت نادرست میباشد'); } } else { $('#submitBtn').fadeOut(1);}
	}
</script>
<script>
window.onload = function(e)
{   
    if(localStorage.getItem('id')==2)
    { showBillsData(); } else { generalList(); }
}
</script>

<style>
.dt-button{ display:none !important;}
.p10{padding:10px !important;}
</style>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				
					
			   <div class="row">		
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">



                  
				   <div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->		

					<ul class="nav my_nave nav-tabs" id="myTab2">
                        <li  class="active" style="width:33% !important;" onclick="generalList();"><a data-toggle="tab" href="#new">
                            <center> لیست انتقالات جدید</center> </a></li>
						<li  style="width:33% !important;" onclick="showBillsData()"><a data-toggle="tab" href="#bills"><center>لیست بل ها</center></a></li>
					</ul>

					 <div class="tab-content">
				<!-- new -->
						<div id="new" class="tab-pane fade in active">
                        <br> <?php $this->load->view('transform/new'); ?>      
						</div>
				<!-- / new -->

				<!-- bills -->
						<div id="bills" class="tab-pane fade">
                        <br> <?php $this->load->view('transform/bill_lists'); ?>    
						</div>
				<!-- /bills -->


                        </div>
                     </div> <!-- / card-body -->
                  
                       
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        

 	<!-- Edit modal -->
	       <div id="transfer_modal" class="modal fade in"  role="dialog" aria-labelledby="transfer_modal" aria-hidden="true">
            <div class="modal-dialog2">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> انتقال  </h5>
                </div>
                <div class="modal-body" id="EditData"></div>   
               </div>
            </div>
        </div>
	<!-- /Edit modal --> 


    <!-- Edit modal -->
	       <div id="itemListModal" class="modal fade in"  role="dialog" aria-labelledby="itemListModal" aria-hidden="true">
            <div class="modal-dialog3">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> لیست اجناس  </h5>
                </div>
                <div class="modal-body" id="itemData">
                <table id="itemTable" class="table table-striped table-bordered my_table">
                       <thead>
                            <tr>
                                <th>شماره</th>
                                <th>نام جنس</th>										
                                <th><center>مقدار موجود</center> </th>	
                                <th><center>واحد</center></th>
                                <th><center>قیمت فی واحد</center></th>
                                <th><center>مجموع</center></th>		
                                <th><center>انتقال</center></th>			
                            </tr>
                        </thead>
                        <tfoot>
                            <tr style="background:#eefcff">
                                <td></td><td></td>
                                <td></td><td></td>
                                <td></td><td></td>
                                <td></td>
                            </tr>
                        </tfoot>
    					</table>
                </div>   
               </div>
            </div>
        </div>
	<!-- /Edit modal -->  

    <!-- // stockModal -->
       <div id="stockModal" class="modal fade in"  role="dialog" aria-labelledby="stockModal" aria-hidden="true">
            <div class="modal-dialog3">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> لیست اجناس  </h5>
                </div>
                <?php echo form_open('transform/transform/addTransform'); ?>
                <div class="modal-body" style="overflow-x2: scroll;">
                <table id="itemTable" class="table table-striped table-bordered my_table" style="min-width2: 1100px !important;">
                       <thead>
                            <tr>
                                <th>نام جنس</th>
                                <th>تعداد / وزن</th>						
                                <th>واحد</th>	
                                <th>قیمت فی واحد</th>
                                <th>تمام شد</th>
                                <th>مفاد فی واحد</th>		
                                <th>انتقال به </th>			
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p10" style="width:200px !important;"><input type="text" name="name" placeholder="نام جنس" required class="form-control"></td>
                                <td class="p10" style="width:150px !important;"><input type="number" step="0.01" name="amount" placeholder="تعداد / وزن" required class="form-control"></td>
                                <td class="p10" style="width:100px !important;"><input type="text" name="unit_name" placeholder="واحد" required class="form-control"></td>
                                <td class="p10" style="width:118px !important;"><input type="number" step="0.01" name="uprice" placeholder="  فی واحد" required class="form-control"></td>
                                <td class="p10" style="width:100px !important;"><input type="number" step="0.01" name="buyuprice" placeholder="تمام شد " required class="form-control"></td>
                                <td class="p10" style="width:100px !important;"><input type="number" step="0.01" name="profituprice" placeholder="مفاد " required class="form-control"></td>
                                <input type="hidden" name="from_branch" value="1"  class="form-control"> </td>
                                <td class="p10" style="width:100px !important;"><select name="to_branch" required class="form-control">
                                        <option value=""> انتقال به </option>
                                        <?php foreach($branch as $k => $v){ ?>
                                            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                          </tbody>
    					</table>
                </div>
                <div class="modal-footer" style="background:#dcf5ff">
					<button type="submit" id="subBtn" name="submit" class="btn btn-info btn-sm m-l-10" >
                  <span class="btn-label"> <i class="fa fa-save"></i> </span>  ثبت  </button>
                <button type="button" class="btn btn-warning btn-sm m-l-10 pull-left" data-dismiss="modal">لغو </button>
				</div>  
			   <?php echo form_close(); ?>   
               </div>
            </div>
        </div>
    <!-- // end of stockModal -->

    <!-- pay modal -->
      <div id="pay_modal" class="modal fade in"  role="dialog" aria-labelledby="pay_modal" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> مقدار پرداخت  </h5>
                </div>
				<?php echo form_open('transform/transform/addPayment'); ?>
                <div class="modal-body">
                    <input type="hidden" id="bill_no" name="bill_no">
                    <input type="hidden" id="payed_before" name="payed_before">
                    <input type="hidden" id="total" name="total">
                    <input type="number" step="0.01"  id="payed" name="payed" onkeyup="noGreater(this.value)" class="form-control" placeholder="مقدار پول" required>
                    </div> 
                    <div class="modal-footer" style="background:#dcf5ff">
                    <button type="submit" id="submitBtn" name="submit" class="btn btn-info btn-sm m-l-10" >
                     <span class="btn-label"> <i class="fa fa-save"></i> </span>
                        ثبت
                </button>
                <button type="button" class="btn btn-warning btn-sm m-l-10 pull-left" data-dismiss="modal">لغو </button>
			   </div>  
			<?php echo form_close(); ?>
               </div>
            </div>
        </div>
	<!-- /pay modal -->  