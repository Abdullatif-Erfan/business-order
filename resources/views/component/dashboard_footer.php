@php
    use App\Helpers\FinanceHelper; // Adjust the namespace based on your Laravel setup

    $incomes = [];
    $outcomes = [];
    for ($i = 1; $i <= 12; $i++) {
        $incomes[] = FinanceHelper::showMonthlyIncome($i, $year, $currency_id);
        $outcomes[] = FinanceHelper::showMonthlyOutcome($i, $year, $currency_id);
    }

    $totalTalabat = FinanceHelper::showTotalTalabatTillNow($year, $currency_id);
    $totalLoan = FinanceHelper::showTotalLoanTillNow($year, $currency_id);
    $finalBalance = $totalLoan - $totalTalabat;
@endphp

<script src="{{ asset('assets/js/core/custom.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jqvmap/maps/jquery.vmap.world.js') }}"></script>
<script src="{{ asset('assets/js/demo.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>

<script>
    var barChart = document.getElementById('barChart').getContext('2d'),
        pieChart = document.getElementById('pieChart').getContext('2d');

    var myBarChart = new Chart(barChart, {
        type: 'bar',
        data: {
            labels: ["حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
            datasets: [
                {
                    label: "قرضه",
                    backgroundColor: '#3f7cc7',
                    data: @json($incomes),
                },
                {
                    label: "طلبات",
                    backgroundColor: '#c78f3f',
                    data: @json($outcomes),
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
        }
    });

    var myPieChart = new Chart(pieChart, {
        type: 'pie',
        data: {
            datasets: [{
                data: [{{ $totalTalabat }}, {{ $totalLoan }}, {{ $finalBalance }}],
                backgroundColor: ["#3f7cc7", "#c78f3f", "#9dd3e2"],
                borderWidth: 0
            }],
            labels: ['قرضه', 'طلبات', 'بیلانس']
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
                labels: {
                    fontColor: 'rgb(154, 154, 154)',
                    fontSize: 11,
                    usePointStyle: true,
                    padding: 20
                }
            },
            pieceLabel: {
                render: 'percentage',
                fontColor: 'white',
                fontSize: 14,
            },
            layout: {
                padding: {
                    left: 20,
                    right: 20,
                    top: 20,
                    bottom: 20
                }
            }
        }
    });
</script>
