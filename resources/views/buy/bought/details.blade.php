@extends('layouts.app')
@section('content')
@php
$currency_name = $boughtItems->first()->currencyRelation->symbols ?? '';
@endphp

<style>
.price-section {
    background-color: #f6f6f6;
}
.final-total{
    background-color:#436fa7;
    color: #fff;
    font-size: 20px;
    font-weight:bolder;
}
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title"> {{__('buy.details_form_title')}}
                                <span class="pull-left">
                                    <a href="{{ url('boughtList') }}">
                                        <button class="btn mybtn bg-default"> {{__('common.back')}} </button>
                                    </a>
                                </span>
                            </h4>
                        </div>
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                           
                                <div class="container col-md-12 col-sm-12 col-xs-12" style="padding: 10px 10px;">
                                   
                                    <table style="width:100%">
                                         <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="4">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td> {{__('common.account_payer')}}: {{ $boughtItems->first()->account->name ?? '' }}</td>
                                            <td>   {{__('common.currency')}}: {{ $boughtItems->first()->currencyRelation->name ?? '' }}</td>
                                            <td> {{__('common.save_date')}} : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td> {{__('common.bill')}} : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('order.supplier_name')}}</th>
                                                    <th>{{__('buy.item')}} </th>
                                                    <th>{{__('buy.bought_amount')}}</th>
                                                    <th>{{__('common.unit')}}</th>
                                                    <th>{{__('common.unit_price')}}</th>
                                                    @if($orgbios[0]->tax_activation === 1)
                                                    <th>{{__('buy.buy_tax_percentage')}}</th>
                                                    <th>{{__('buy.buy_tax_price')}}</th>
                                                    @endif
                                                    <th>{{__('common.total_price')}}</th>
                                                    <th>{{__('common.unit')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($boughtItemDetails as $key => $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->accountRelation->name ?? ' ' }}</td>
                                                <td>{{ $detail->preListRelation->name ?? ' ' }}</td>
                                                
                                                <td>
                                                    @php
                                                    echo fmod($detail->amount, 1) == 0
                                                        ? number_format($detail->amount, 0)
                                                        : number_format($detail->amount, 2);
                                                    @endphp
                                                </td>
                                                
                                                <td>{{ $detail->unitRelation->name }}</td>

                                                <td>
                                                    @php
                                                    echo fmod($detail->bought_up, 1) == 0
                                                        ? number_format($detail->bought_up, 0)
                                                        : number_format($detail->bought_up, 2);
                                                    @endphp
                                                </td>
                                                
                                                @if($orgbios[0]->tax_activation === 1)
                                                <td> {{$detail->buy_tax_percentage}} % </td>
                                                <td> {{$detail->buy_tax_price}} </td>
                                                @endif

                                                <td>
                                                    @php
                                                    echo fmod($detail->total, 1) == 0
                                                        ? number_format($detail->total, 0)
                                                        : number_format($detail->total, 2);
                                                    @endphp
                                                </td>

                                                <td>{{ $currency_name ?? '' }}</td>
                                                
                                             
                                            </tr>
                                        @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <table class="table table-bordered new" style="background-color:#f6f6f6; width:100%;margin-top:20px">
                                        <tr>
                                            <td>{{__('common.total_price')}} &nbsp; </td>
                                            <td>
                                                @php
                                                    echo  number_format($boughtItems->first()->total_price,2);
                                                @endphp
                                            </td>
                                             <td>  {{__('buy.cur_pay')}} </td>
                                            <td> 
                                                @php
                                                    echo  number_format($boughtItems->first()->cur_pay,2);
                                                    @endphp
                                            </td>
                                            <td> {{__('buy.remained')}} </td>
                                            <td> @php
                                                    echo 
                                                    number_format($boughtItems->first()->remained,2);
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('buy.note')}}</td>
                                            <td colspan="5">{{$boughtItems->first()->note}}</td>
                                        </tr>
                                    </table>
                                </div>


                                <div class=" visible-print" style="width:100%;margin: 35px 0px; overflow:hidden; height: 24px;color:#000"> ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ </div>

                                <div class="container col-md-12 col-sm-12 col-xs-12 visible-print" id="print_area">
                                 <p class="d-none">{{__('common.print_date')}}‌ : {{ now()->format('Y-m-d') }}</p>
                                    <table style="width:100%">
                                       <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="2">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{__('common.print_date')}}‌ : {{ $boughtItems->first()->idate ?? '' }}</td>
                                            <td>{{__('common.bill')}}‌ : {{ 'BUY_' . ($boughtItems->first()->billno ?? '') }}</td>
                                        </tr>
                                        <tr>
                                            <td> {{__('common.account_payer')}}‌: {{ $boughtItems->first()->account->name ?? '' }}</td>
                                            <td>   {{__('common.user')}}‌ : {{ $boughtItems->first()->iby ?? '' }}</td>
                                        </tr>
                                    </table>
                                    <hr class="hidden-print" style="margin-bottom:20px; padding-bottom:20px;" />
                                    <div class="table-responsive">
                                        <table class="table table-bordered new" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('common.number')}}</th>
                                                    <th>{{__('common.seller')}}</th>
                                                    <th> {{__('buy.item')}} </th>
                                                    <th>{{__('buy.bought_amount')}}</th>
                                                    <th>{{__('common.unit')}}</th>
                                                    <th>{{__('common.unit_price')}}</th>
                                                    <th>{{__('common.total_price')}}</th>
                                                    <th>{{__('common.currency')}}</th>
                                                    <th>{{__('buy.discount')}}</th>
                                                    <th>{{__('buy.transport')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($boughtItemDetails as $key => $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->accountRelation->name ?? ' ' }}</td>
                                                <td>{{ $detail->preListRelation->name ?? ' ' }}</td>
                                                
                                                <td>
                                                    @php
                                                        echo  number_format($detail->amount,2);
                                                    @endphp
                                                </td>
                                                
                                                <td>{{ $detail->unitRelation->name }}</td>

                                                <td>
                                                    @php
                                                        echo number_format($detail->bought_up,2);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->total,2);
                                                    @endphp
                                                </td>

                                                <td>{{  $boughtItems->first()->currencyRelation->name ?? ''}}</td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->discount,2);
                                                    @endphp
                                                </td>

                                                <td>
                                                    @php
                                                        echo  number_format($detail->transport,2);
                                                    @endphp
                                                </td>
                                            </tr>
                                        @endforeach

                                                <tr>
                                                    <td colspan="5" rowspan="7" style="padding: 40px;">
                                                        <div class="col-md-12" style="border:2px dotted #999; min-height:80px;background-color:#f8f8f8;border-top-right-radius:10px; border-bottom-left-radius:10px; padding: 10px;">
                                                        {{__('buy.note')}} : {{ $orgbios[0]->note_for_print }}
                                                        </div>
                                                         <div class="col-md-12 m-t-20">
                                                              <br>
                                                             <strong>
                                                                 <h3> {{__('buy.sign_and_stamp')}} ---------------------</h3>
                                                             </strong>
                                                         </div>
                                                    </td>
                                                    <td colspan="2" class="price-section">{{__('buy.total_bill_price')}}</td>
                                                    <td colspan="3" class="price-section">
                                                       
                                                        @php
                                                           echo  number_format($boughtItems->first()->total_price,2);
                                                        @endphp

                                                        {{ $currency_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section">  {{__('buy.discount')}} </td>
                                                    <td colspan="3" class="price-section">
                                                        @php
                                                           echo 
                                                           number_format($boughtItems->first()->trans_spend);
                                                        @endphp
                                                        {{ $currency_name ?? '' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="price-section"> {{__('buy.payable')}} </td>
                                                    <td colspan="3" class="price-section">
                                                        @php echo number_format($boughtItems->first()->cur_pay);
                                                        @endphp
                                                        {{ $currency_name ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section"> {{__('buy.cur_pay')}}  </td>
                                                    <td colspan="3" class="price-section">
                                                         @php
                                                           echo 
                                                           number_format($boughtItems->first()->payable,2) ;
                                                         @endphp
                                                         {{ $currency_name ?? '' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2" class="price-section">  {{__('buy.remained')}}  </td>
                                                    <td colspan="3" class="price-section">
                                                          @php
                                                           echo  number_format($boughtItems->first()->remained,2);
                                                          @endphp
                                                          {{ $currency_name ?? '' }}
                                                    </td>
                                                </tr>

                                                <!-- <tr>
                                                    <td colspan="2" class="price-section"> بقایای سابقه   </td>
                                                    <td colspan="2" class="price-section">
                                                        ???
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="final-total">  مجموع عمومی   </td>
                                                    <td colspan="2" class="final-total">
                                                        ???
                                                    </td>
                                                </tr> -->
                                                
                                            </tbody>
                                        </table>
                                    </div>


                               


                                </div>

                                <!-- buttons -->
                                <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    
                                    <!-- print button -->
                                    <button onclick="print_page_with_image()" class="btn btn-success btn-sm btn-border m-r-10 hidden-print" >
                                    <i class="fas fa-print"></i> {{__('buy.print_bill')}}  
                                    </button>
                                            
                                    <!-- edit button -->
                                    @if($boughtItems->first()->is_cleared == 0)
                                    <a href="{{ route('boughtList.edit', $boughtItems->first()->times) }}"   class="hidden-print">
                                        <button class="btn btn-primary btn-sm m-r-10">
                                        <i class="fas fa-pen"></i> {{__('common.edit')}}
                                        </button>
                                    </a>
                                    @endif

                                     <!-- حذف بل های ناقص -->
                                     @if(!$jexists)
                                    <a href="{{ route('buy.delete_uncompleted_buy', $boughtItems->first()->times) }}"  onClick="return doConfirm();"   class="hidden-print">
                                        <button class="btn btn-warning btn-sm m-r-10">
                                        <i class="fas fa-trash"></i> {{__('buy.delete_uncompleted_buy')}} 
                                        </button>
                                    </a>
                                    @endif

                                      

                                    </div>
                                </div>
                                <!-- /buttons -->

                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
