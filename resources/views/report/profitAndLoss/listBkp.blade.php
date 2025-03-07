@extends('layouts.app')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                            <h4 class="card-title">  مفاد و ضرر 
                            <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
                            </h4>
                        </div>

                        <div class="card-body">
                            
                    <!-- panel -->
                    <div class="col-md-12"  id="print_area">
                        <div class="panel-group" id="accordion">
                            <div class="col-xs-12">
                                <div class="row">

                                     <!-- Income Seciont -->
                                    <div class="col-md-6">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseIncomes" class="">
                                                    <strong>بخش عوایدی</strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseIncomes" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">       

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >طلبات</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >عواید متفرقه</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >مفاد خالص فروشات</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >عواید فروشات</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                </div>
                                            </div>

                                    </div>
                                    <!-- / Income Section  -->


                                    <!-- Expense Section -->
                                    <div class="col-md-6">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseExpense" class="">
                                                    <strong>بخش مصرفی</strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseExpense" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">       

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >قرضه</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >مصارف متفرقه</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" > معاشات کارمندان</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" > خرید + ترانسپورت</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>


                                                </div>
                                            </div>

                                    </div>
                                    <!-- /Expense Section -->


                                    <!-- General Section -->
                                    <div class="col-md-12">
                                     
                                        <div class="panel-heading m-t-10" style="background-color:#f0eded">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseGeneral" class="">
                                                    <strong>بخش عمومی</strong>   
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseGeneral" class="panel-collapse collapse in" style="height: auto;">
                                                <div class="panel-body" id="body">       

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >سرمایه شرکت</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" > پول نقد شرکت</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>

                                                    <table class="table table-bordered"  style="width:100%">
                                                            <tr>
                                                                <th rowspan="2" style="width:150px !important;" >  مفاد خالص شرکت</th>
                                                                  <td style="width:70px !important;">افغانی : </td>
                                                                  <td>123</td>
                                                            </tr>
                                                            <tr>
                                                                  <td>دالر : </td>
                                                                  <td>456 </td>
                                                            </tr>
                                                    </table>


                                                </div>
                                            </div>

                                    </div>
                                    <!-- /Expense Section -->


                                </div>
                            </div>
                         </div>
                      </div>
                    </div> <!-- End card-body -->
                    </div> <!-- End main card -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
