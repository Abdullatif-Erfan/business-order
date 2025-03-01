@extends('layouts.app')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                            <h4 class="card-title"> چارت حسابات 
                            <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
                            </h4>
                        </div>

                        <div class="card-body">
                            
                    <!-- panel -->
                    <div class="col-md-12"  id="print_area">
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                               @include('report.chart_of_account.company_accounts')
                               @include('report.chart_of_account.suppliers')
                               @include('report.chart_of_account.customers')

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

<script>
 function updateURLWithCurrencyId(currency_id) {
    let currentUrl = new URL(window.location.href);
    let baseUrl = currentUrl.origin + currentUrl.pathname.split('/').slice(0, -1).join('/') + '/';

    // Redirect to new URL with currency ID
    window.location.href = baseUrl + currency_id;
}

</script>

@endsection
