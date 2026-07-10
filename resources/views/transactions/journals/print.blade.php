@extends('layouts.app')

@section('content')
<script>
 function submitAccountIdToURL() {
    var account_id = parseInt($('#account_id').val());
    var base_url = $('#base_url').val();
    if(account_id > 0) {
        window.location.href = base_url + "reports/ledger/" + account_id;
    } else {
        alert("حساب را انتخاب نمایید");
    }
 }
</script>

<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
}
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
                    <div class="card">
                        <div class="col-12" style="padding: 20px; margin-bottom: 10px">
                            <button class="printBtn" onclick="print_page_with_image()" style="margin-left: 50px">
                               <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route('journal.index') }}">
                                <button class="printBtn">
                                   <i class="fas fa-arrow-left"></i>
                                </button>
                             </a>
                        </div>

                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="col-12" style="border:2px dotted #ddd; padding: 20px" id="print_area">
                               <!-- header -->
                                <div class="row">
                                    <table class="noBorder" style="width:100%">
                                        <tr>
                                            <td>
                                                <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 98% !important;">
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered" style="width:100%">
                                        <tr>
                                            <td colspan="4" style="background-color: #1483b0; color: #fff; text-align:center; font-size: 20px; padding: 4px">
                                            رسید</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="width: 120px;">کد نمبر</td>
                                            <td colspan="2">{{ $journals[0]['code'] }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">تاریخ ثبت</td>
                                            <td colspan="2">{{ $journals[0]['idate'] }}</td>
                                        </tr>
                                        <tr>
                                            <td> حساب بردگی </td>
                                            <td>{{ $journals[0]->accountRelation->name ?? '' }} </td>
                                            <td> حساب رسیدگی </td>
                                            <td>{{ $journals[1]->accountRelation->name ?? '' }} </td>
                                        </tr>
                                        <tr>
                                            <td> مبلغ  </td>
                                            <td>{{ number_format($journals[0]['amount'],2) }} {{ $journals[0]['currency_name'] }}</td>
                                            <td> مبلغ  </td>
                                            <td>{{ number_format($journals[1]['amount'],2) }} {{ $journals[1]['currency_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>مبلغ   به حروف</td>
                                            <td>{{$journals[0]['amount_in_words']}}</td>
                                            <td>مبلغ   به حروف</td>
                                            <td>{{$journals[1]['amount_in_words']}}</td>
                                        </tr>
                                        <tr>
                                            <td>تفصیلات</td>
                                            <td>{{ $journals[0]['details'] }}  </td>
                                            <td>تفصیلات</td>
                                            <td>{{ $journals[1]['details'] }}  </td>
                                        </tr>

                                        <tr>
                                            <td colspan="4" style="text-align:center; height: 100px;">مهر و امضاه 
                                             <div style="margin-top: 20px;">-------------------------</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:center; height: 20px;">خدمات نرم افزار نوین تیک</td>
                                        </tr>
                                    </table>
                                </div>

                            </div>
                       </div>
                     </div>  
                  </div>
               </div>
            </div>
        </div>
    </div>
<!-- footer -->
<!-- No footer content is provided, but you can add it here -->
</div>
<!-- /main content -->
@endsection
