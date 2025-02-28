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
                    <div class="card shadow-lg">
                        <div class="card-header text-white text-center main-card">
                            <h4 class="card-title mb-0">📊 گزارشات سیستم</h4>
                        </div>

                        <div class="card-body">
                            <div class="row m-t-20">
                                <!-- General Reports -->
                                <div class="col-md-4">
                                    <div class="card border-info shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">📈 گزارش خرید و فروش</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.daily') }}')">📅 روزانه</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.monthly') }}')">📆 ماهانه</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a href="#" onclick="submitReportForm(event, '{{ route('reports.yearly') }}')">📊 سالانه</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer & Seller Reports -->
                                <div class="col-md-4">
                                    <div class="card border-success shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">👥 گزارش مشتریان و فروشندگان</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a target="_blank" href="cashflow">💰 کهاته حسابات</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="reports/balancesheet">📑 بیلانس شیت</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="chartOfAccount">📌 چارت حسابات</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="reports/clearance">✅ تصفیه حساب</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Treasury Reports -->
                                <div class="col-md-4">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-header bg-custom-grey">
                                            <h6 class="mb-0 font-bold">🏦 گزارش خزانه</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">
                                                    <a target="_blank" href="cashflow">📜 سهم سهامداران</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="reports/balancesheet">💹 مفاد و ضرر</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="chartOfAccount">💳 قرضه ها</a>
                                                </li>
                                                <li class="list-group-item">
                                                    <a target="_blank" href="reports/clearance">🧾 طلبات</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

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
        form.submit();
    }
</script>

@endsection
