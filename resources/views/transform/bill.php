<?php $bul = base_url(); ?>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>سیستم مدیریتی  شیرنی سرای رضوی</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="<?php echo $bul; ?>assets/img/icon.ico" type="image/x-icon"/>
	<script src="<?php echo $bul; ?>assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
		WebFont.load({
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['<?php echo $bul; ?>assets/css/fonts.min.css']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/css/style.css">
	<!-- <link rel="stylesheet" href="<?php echo $bul; ?>assets/css/rtl.css"> -->
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/css/dr.css">
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/plugin/select2/select2.min.css">
	<script src="<?php echo $bul; ?>assets/plugin/select2/jquery-2.1.4.min.js"></script> 
	
	<!-- DataTable  -->
	<script type="text/javascript" src="<?php echo $bul;?>assets/plugin/datatable/js/datatables_and_button.js"></script>
	<link href="<?php echo $bul;?>assets/plugin/datatable/css/dataTables.bootstrap.css" rel="stylesheet">
	<link href="<?php echo $bul;?>assets/plugin/datatable/css/buttons.bootstrap4.css" rel="stylesheet">


	<link rel="stylesheet" href="<?php echo $bul; ?>assets/css/custom.css">
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.css">
	<script src="<?php echo $bul; ?>assets/datepicker/bootstrap.min.js"></script>
	<link rel="stylesheet" href="<?php echo $bul; ?>assets/css/myHelper.css">

	<style>
  	span.panel-title a{font-size:14px !important;}
	</style>
    <script> window.onload= function (e) { window.print(); } </script>
</head>


					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
				

                    <div class="col-md-12 col-sm-12 col-xs-12" id="print_area">
                        <div class="row">
                        <!-- print header -->
                        <!-- / end of print header -->
                                <div class="table-responsive">
                                 <table id="todaysTables" class="table table-striped table-bordered">
                                    <thead>
                                       <tr>
                                       <img src="<?php echo base_url().show('header','org_bio'); ?>"  style="width: 100% !important;border: 1px solid #ddd;padding: 1px;">
                                        </tr>
                                        <tr>
                                            <td colspan="8"><center> بل نمبر (<?=show_bill_number($record[0]['bill_no'])?>) لیست  اینتقالات از   <?php echo show_this_column('name','branch',
                                            array('id'=>$record[0]['from_branch'])); ?> به  <?php echo show_this_column('name','branch',array('id'=>$record[0]['to_branch'])); ?></center></td>
                                        </tr>
                                        <tr>
                                            <th>شماره</th>
                                            <th>نام جنس</th>				
                                            <th><center>تعداد  / وزن </center> </th>	
                                            <th><center>واحد</center></th>
                                            <th><center>قیمت مجموعی</center></th>		
                                            <th><center>تاریخ</center></th>		
                                            <th><center>ثبت کننده</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $id=1; $total=0;  
                                        foreach($record as $key => $value)
                                        { $total += $value['total_price']; ?>
                                            <tr>
                                                <td><?=$id?></td>
                                                <td><?=$value['name']?></td>
                                                <td><center><?=$value['amount']?></center></td>
                                                <td><center><?=$value['unit_name']?></center></td>
                                                <td><center><?=$value['total_price']?></center></td>
                                                <td><center><?=$value['idate']?></center></td>
                                                <td><center><?=$value['inserted_by']?></center></td>
                                            </tr>
                                       <?php $id++; } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#e7ecd8">
                                            <td colspan="4"><strong><center> </center></strong></td>
                                            <td><center><strong>مجموع : <?=$total?></strong></center></td>
                                            <td><strong><center>پرداخت شده :  <?php $payed = show_this_column('payed','bill_list',array('bill_no'=>$record[0]['bill_no']));
                                            echo $payed; ?></center></strong></td>
                                            <td><strong><center>قابل پرداخت : <?php echo $total - $payed; ?></center></strong></td>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                
                                </table>
                                <div class="col-md-12 col-sm-12 col-xs-12" style="padding:40px 0px;">
                                <div class="col-md-12 col-sm-12 col-xs-12" style="border-bottom:2px dotted #000;"></div>
                                </div>
                                <table id="todaysTables" class="table table-striped table-bordered">
                                    <thead>
                                       <tr>
                                       <img src="<?php echo base_url().show('header','org_bio'); ?>"  style="width: 100% !important;border: 1px solid #ddd;padding: 1px;">
                                        </tr>
                                        <tr>
                                        <td colspan="8"><center> بل نمبر (<?=show_bill_number($record[0]['bill_no'])?>) لیست  اینتقالات از   <?php echo show_this_column('name','branch',
                                            array('id'=>$record[0]['from_branch'])); ?> به  <?php echo show_this_column('name','branch',array('id'=>$record[0]['to_branch'])); ?></center></td>
                                        </tr>
                                        <tr>
                                            <th>شماره</th>
                                            <th>نام جنس</th>				
                                            <th><center>تعداد  / وزن </center> </th>	
                                            <th><center>واحد</center></th>
                                            <th><center>قیمت مجموعی</center></th>		
                                            <th><center>تاریخ</center></th>		
                                            <th><center>ثبت کننده</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $id=1; $total=0;  
                                        foreach($record as $key => $value)
                                        { $total += $value['total_price']; ?>
                                            <tr>
                                                <td><?=$id?></td>
                                                <td><?=$value['name']?></td>
                                                <td><center><?=$value['amount']?></center></td>
                                                <td><center><?=$value['unit_name']?></center></td>
                                                <td><center><?=$value['total_price']?></center></td>
                                                <td><center><?=$value['idate']?></center></td>
                                                <td><center><?=$value['inserted_by']?></center></td>
                                            </tr>
                                       <?php $id++; } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#e7ecd8">
                                            <td colspan="4"><strong><center> </center></strong></td>
                                            <td><center><strong>مجموع : <?=$total?></strong></center></td>
                                            <td><strong><center>پرداخت شده :  <?php $payed = show_this_column('payed','bill_list',array('bill_no'=>$record[0]['bill_no']));
                                            echo $payed; ?></center></strong></td>
                                            <td><strong><center>قابل پرداخت : <?php echo $total - $payed; ?></center></strong></td>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                
                                </table>
                            </div>
                        </div>


                        </div>

					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		  
        <!-- /main content -->
        