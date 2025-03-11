@extends('layouts.app')
@section('title', 'داشبورد')

@section('content')
<script>
    function submit12MonthForm() {
        $('#firstTabSearch').submit();
    }
    function submit12MonthForm() {
        $('#secondTabSearch').submit();
    }
    function submit12MonthForm() {
        $('#thirdTabSearch').submit();
    }
    function submitCircleGraphForm() {
        $('#myForm2').submit();
    }
</script>

<script>
$(document).ready(function () {
    // Restore active tab from local storage
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        $('#myTab2 a[href="' + activeTab + '"]').tab('show');
    }

    // Handle tab click event
    $('#myTab2 a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var targetTab = $(e.target).attr('href');
        localStorage.setItem('activeTab', targetTab);

        // Reinitialize select dropdowns for the active tab
        setTimeout(function () {
            $(targetTab).find('select[name="currency_id"]').trigger('change');
        }, 100);
    });

    // Handle currency change properly
    $(document).on('change', 'select[name="currency_id"]', function() {
        var currencyId = $(this).val();
        console.log("Currency changed: " + currencyId);
        updateURLWithCurrencyId(currencyId); // Make sure this function exists
    });
});
// $(document).ready(function () {
//     // Debugging: Check what's stored in localStorage
//     console.log("Stored active tab:", localStorage.getItem('activeTab'));

//     var activeTab = localStorage.getItem('activeTab');
//     if (activeTab) {
//         console.log("Trying to activate tab:", activeTab);
//         $('#myTab2 a[href="' + activeTab + '"]').tab('show'); // Activate the correct tab
//     }

//     // Handle tab click event
//     $('#myTab2 a[data-toggle="tab"]').on('shown.bs.dropdown', function (e) {
//     // $('#myTab2 a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
//         var targetTab = $(e.target).attr('href');
//         console.log("Saving active tab:", targetTab);
//         localStorage.setItem('activeTab', targetTab);
//     });
// });

// if (typeof jQuery == 'undefined') {
//     alert("jQuery is not loaded!");
// } else {
//     console.log("jQuery is working!"); 
//     alert("jQuery is working");
// }
</script>

<style>
    a {
        color: #555 !important;
    }

    a:hover {
        text-decoration: none;
    }
</style>

<div class="main-panel">
    <input type="hidden" id="todays_date" value="{{ date('Y-m-d') }}">
    <div class="content">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h1 class="text-white pb-2 fw-bold main_title">
                            {{ $orgBio->name }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
   
        <!-- tab -->
        <div class="col-12 tab-wrapper">
            <ul class="nav my_nave nav-tabs" id="myTab2">
                <li class="active"><a data-toggle="tab" href="#todaysTransaction">معاملات امروز</a></li>
                <li><a data-toggle="tab" href="#importantTrans">معاملات مهم تجارت</a></li>
                <li><a data-toggle="tab" href="#cache">خزانه</a></li>
                @if($isAdmin)
                <li><a data-toggle="tab" href="#branch">شعبات</a></li>
                @endif
            </ul>

            <div class="tab-content">
                <!-- todaysTransaction -->
                <div id="todaysTransaction" class="tab-pane fade in active">
                     @include('dashboard.first-tab.todays_search')
                     @include('dashboard.first-tab.todays_card')
                      {{--   @include('dashboard.first-tab.graph') --}}
                </div>
                <!-- / todaysTransaction -->

                <!-- importantTrans -->
                <div id="importantTrans" class="tab-pane fade">
                    @include('dashboard.second-tab.overall_business_search', ['data', $data])
                    @include('dashboard.second-tab.cards', ['data' => $data])
                </div>
                <!-- / importantTrans -->                

                <!-- cache -->
                <div id="cache" class="tab-pane fade">
                    @include('dashboard.third-tab.cash_search', ['data' => $data])
                    @include('dashboard.third-tab.cash_cards', ['thirdTab' => $thirdTab])
                </div>
                <!-- / cache -->

                @if($isAdmin)
                <!-- branch -->
                <div id="branch" class="tab-pane fade">
                    @include('dashboard.fourth-tab.branch_cards', ['branches' => $branches,'branch_id' => $branch_id])
                </div>
                <!-- / branch -->
                @endif

            </div>
        </div>
       
        <!-- / tab -->

    </div>
    @include('component.footer-text')
</div>

<script>
    function changeBranch(branch_id) {
    if (confirm('آیا میخواهید به این شعبه وارید شوید ؟')) {
        fetch("{{ route('login.changeBranch') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ branch_id: branch_id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                location.reload(); // Refresh page to reflect new session data
            } else {
                alert("خطا در تغییر شعبه!");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}

</script>

@endsection
