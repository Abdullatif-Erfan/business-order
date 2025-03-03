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
 function updateURLWithCurrencyId(currencyId) {
        let currentUrl = window.location.href; // Get current full URL
        let baseUrl = window.location.origin + '/chartOfAccount'; // Ensure base URL is correct

        // If the current URL already has an ID, replace it
        let pathParts = window.location.pathname.split('/').filter(part => part !== ""); // Remove empty segments
        let lastPart = pathParts[pathParts.length - 1]; // Get last part of URL

        let newUrl;

        // If the last part is a number, replace it with the new currency ID
        if (!isNaN(lastPart)) {
            pathParts[pathParts.length - 1] = currencyId;
            newUrl = window.location.origin + '/' + pathParts.join('/');
        } else {
            newUrl = baseUrl + '/' + currencyId; // Append new ID
        }

        window.location.href = newUrl; // Redirect
}

</script>

@endsection
