@extends('layouts.app')

@section('content')

<style>
.card .card-header, .card-light .card-header{
    padding: 10px 20px !important;
}
.card-title {
    color: #fff !important;
}
.card, .card-light {
    margin: 0px 8px 50px;
}
.main-card {
    background-color: #327bd7 !important;
}
.bg-custom-grey {
    background-color: #ededed !important;
}
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header text-white text-center main-card">
                            <h4 class="card-title mb-0">📊 {{__('reports.home_card_title')}} </h4>
                        </div>

                        <div class="card-body">
                            <div class="row m-t-20">
                        
                                <!-- General Reports -->
                                <div class="col-md-4">
                                    <div class="card border-info shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">📈 {{__('reports.sales_and_buy_report')}}</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.daily') }}')">📅 
                                                    {{__('reports.daily')}}</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.monthly') }}')">📆 
                                                    {{__('reports.monthly')}}</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.yearly') }}')">📊 
                                                    {{__('reports.yearly')}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer & Seller Reports -->
                                <div class="col-md-4">
                                    <div class="card border-success shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">👥 {{__('reports.suppliers_and_customers_report')}}</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('cacheflow.index')}}">💰  {{__('reports.customer_accounts')}} </a>
                                                </li>
                                                 <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('cacheflowWithBalance.index')}}">💰 {{__('reports.customer_accounts_with_balance')}}</a>
                                                </li>

                                                <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('balancesheet.index') }}">📑 
                                                    {{__('reports.balance_sheet')}}</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('chartOfAccount.index')}}">📌 {{__('reports.chart_of_account')}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Treasury Reports -->
                                <div class="col-md-4">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">🏦 {{__('reports.profit_and_loss_title')}}</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <!-- <li class="list-group-item">
                                                    <a target="_blank" href="#">📜 سهم سهامداران</a>
                                                </li> -->
                                                <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('profitloss.index') }}">💹  {{__('reports.company_net_profit')}}  </a>
                                                </li>
                                                <li class="list-group-item">
                                                    .
                                                </li>
                                                <!-- <li class="list-group-item">
                                                    <a target="_blank" href="#">💳 قرضه ها</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="#">🧾 طلبات</a>
                                                </li> -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>


                                  <!-- general low -->

                                  <!-- <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="card border-success shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">👥 
                                            {{__('reports.system_usage_low')}} </h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a target="_blank" href="{{ route('laws.index') }}">✅ {{__('reports.low_list')}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> -->

                            </div> <!-- End row -->
                        </div> <!-- End card-body -->
                    </div> <!-- End main card -->
                </div>
            </div>
        </div>
    </div>
</div>

<form id="reportForm" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function submitReportForm(event, url) {
        event.preventDefault();
        let form = document.getElementById("reportForm");
        form.action = url;
        form.target = "_blank"; // Open form submission in a new tab
        form.submit();
    }

   
</script>
@endsection
