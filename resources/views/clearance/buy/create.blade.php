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
                            <strong class="center"> {{__('clearance.create_title')}} 
                            {{ $account_name ?? '' }} {{__('clearance.with_this_account')}}  {{ $currency_name ?? '' }}  </strong>
                        </div>


                        <div class="card-body" style="padding-bottom:20px;">
                          <form action="{{ route('clearance.buy.store') }}" method="POST">
                          @csrf
                             <ol>
                                <li>
                                 {{__('clearance.note1')}} 
                                </li>
                                 <li>
                                 {{__('clearance.note2')}}
                                
                                 </li>
                                 <!-- <li>
                                از اینکه تصفیه حساب نوع از معاملات نسیه به نسیه میباشد بعداز تصفیه حساب تمام ریکاردهای قبلی دیگر محاسبه نشده و مجموع شان یک ریکارد در ژورنال ویا روزنامچه ثبت میگردد که نیز نوع نسیه به نسیه میباشد. 
                                و اگر شرکت بخواهد نقد پرداخت نمایند معاملات نقد به نقد را در ژورنال ویا روزنامچه انتخاب نموده انجام بدهند. 
                                 </li> -->
                             </ol>
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <table id="clearanceTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr>
                                            <th> {{__('common.number')}} </th>
                                            <th>  {{__('common.bill')}}   </th>
                                             <th> {{__('clearance.remained_amount')}} </th>
                                            <th> {{__('clearance.select_for_clearance')}} </th>
                                        </tr>
                                    </thead>
                                     <tbody>
                                        @foreach($boughtItem as $key => $value)
                                        @php
                                            $total += $value->remained;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $value->billno }}</td>
                                            <td>{{ number_format($value->remained,2) }}</td>
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
                                              <td colspan="2"> {{__('clearance.total_price')}} </td>
                                              <td>
                                                 <strong id="totalAmount">{{ number_format($total,2) }}</strong>
                                                 <input type="hidden" name="total" id="total_price" value="{{ number_format($total,2) }}">
                                              
                                              </td>
                                               <td></td>
                                          </tr>
                                     </tfoot>
                                    </tfoot>  
                                </table>
                            </div> <!-- /table responsive -->
                            <input type="hidden" name="customer_account_id" value="{{ $boughtItem->first()->customer_account_id }}">
                            <input type="hidden" name="currency_id" value="{{ $boughtItem->first()->currency_id }}">
                            <input type="hidden" name="company_account_id" value="{{ $ownBanks->id }}">
                            <input type="hidden" name="company_account_name" value="{{ $ownBanks->name ?? '' }}">
                            <input type="hidden" name="customer_account_name" value="{{ $account_name ?? '' }}">

                            <div class="col-12 m-b-10 m-t-20">
                                 <input type="checkbox" name="confirm" class="confirmed-checkbox" value="1" onchange="enableSubmitButton(this)">
                                <!-- اینجانیب {{ auth()->user()->full_name ?? '' }} تایید مینمایم که مبلغ انتخاب شده را با آقای {{ $account_name ?? '' }} با واحد پولی {{ $currency_name ?? '' }} همرای  {{ $ownBanks->name ?? '' }}  تصفیه نمایم و نقدا پرداخت نمایم. -->
                                {{ __('clearance.confirmation_text', [
                                    'user' => auth()->user()->full_name ?? '',
                                    'account' => $account_name ?? '',
                                    'currency' => $currency_name ?? '',
                                    'bank' => $ownBanks->name ?? '',
                                ]) }}

                            </div>
                             <button type="submit" disabled class="btn btn-primary btn-sm form-control col-md-4" id="submit-btn"> 
                            {{__('clearance.confirm_and_save')}} </button>
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
            // let submitBtn = document.getElementById('submit-btn');
            // if (total === 0) {
            //     // submitBtn.style.display = 'none';
            //     submitBtn.disabled = true;
            // } else {
            //     // submitBtn.style.display = 'block';
            //     submitBtn.disabled = false;
            // }
        }

        // Attach event listener to checkboxes
        document.querySelectorAll('.remained-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', recalculateTotal);
        });

        // Initial check to set button visibility correctly on page load
        recalculateTotal();
    });


    function enableSubmitButton(checkbox) 
    {  
        let submitBtn = document.getElementById('submit-btn');
        let total_price = parseInt(document.getElementById('total_price').value) || 0;

        // Enable the button only if checkbox is checked and total price is greater than 0
        submitBtn.disabled = !(checkbox.checked && total_price > 0);
   }
</script>



@endsection

