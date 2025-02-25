@extends('layouts.app')

@section('content')
@php
    $total = 0; 
@endphp

<style>
    /* Make checkboxes larger */
    input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }
</style>

<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="card-header" style="padding: 10px;text-align:center">
                            <a href="{{ route('clearance.index') }}" class="pull-left">
                                <button class="btn mybtn bg-default">  
                                  <i class="fa fa-arrow-left"></i>
                                </button>
                            </a>
                            <strong class="center">  تصفیه حساب 
                            {{ $account_name ?? '' }} با حساب  {{ $currency_name ?? '' }}  </strong>
                        </div>


                        <div class="card-body">
                          <form action="{{ route('clearance.sales.store') }}" method="POST">
                          @csrf
                             <p>
                                شما میتوانید همه بل نمبر هارا ویا چندین بل نمبر خاص را انتخاب نمایید برای تصفیه حساب.  
                                <br>
                                لطفا قبل از تصفیه حساب بک اپ بیگیرید و ریکارد تصفیه حساب قابل ویرایش نمیباشد. 
                             </p>
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <table id="clearanceTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr>
                                            <th> شماره </th>
                                            <th>  بل نمبر  </th>
                                             <th> مبلغ باقیمانده </th>
                                            <th> انتخاب برای تصفیه حساب </th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        @foreach($salesRecords as $key => $value)
                                        @php
                                            $total += $value->remained;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->billno }}</td>
                                            <td>{{ number_format($value->remained) }}</td>
                                            <td>
                                                <input type="checkbox" name="check[{{ $key }}]" class="remained-checkbox" checked value="1" data-amount="{{ $value->remained }}">
                                                <input type="hidden" name="bill_numbers[{{ $key }}]" value="{{ $value->billno }}">
                                                <input type="hidden" name="remained[{{ $key }}]" value="{{ $value->remained }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                     </tbody>
                                     <tfoot>
                                          <tr style="background:#eefcff">
                                              <td colspan="2">مبلغ مجموعی</td>
                                              <td>
                                                 <strong id="totalAmount">{{ number_format($total) }}</strong>
                                                 <input type="hidden" name="total" id="total_price" value="{{ number_format($total) }}">
                                              
                                              </td>
                                               <td></td>
                                          </tr>
                                     </tfoot>
                                    </tfoot>  
                                </table>
                            </div> <!-- /table responsive -->
                            <input type="hidden" name="customer_account_id" value="{{ $salesRecords->first()->customer_account_id }}">
                            <input type="hidden" name="currency_id" value="{{ $salesRecords->first()->currency_id }}">

                             <button type="submit" class="btn btn-primary btn-sm form-control col-md-4" id="submit-btn"> تایید وثبت تصفیه حساب </button>
                            </form>
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function recalculateTotal() {
            let total = 0;
            document.querySelectorAll('.remained-checkbox').forEach((checkbox) => {
                if (checkbox.checked) {
                    total += parseFloat(checkbox.dataset.amount);
                }
            });

            // Update the total in the footer
            document.getElementById('totalAmount').innerText = total.toLocaleString();
            // document.getElementById('total_price').value = total.toLocaleString();
            document.getElementById('total_price').value = total.toLocaleString().replace(/,/g, '');

            // Show or hide submit button based on total
            let submitBtn = document.getElementById('submit-btn');
            if (total === 0) {
                submitBtn.style.display = 'none';
            } else {
                submitBtn.style.display = 'block';
            }
        }

        // Attach event listener to checkboxes
        document.querySelectorAll('.remained-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', recalculateTotal);
        });

        // Initial check to set button visibility correctly on page load
        recalculateTotal();
    });
</script>



@endsection

