@extends('layouts.app')
@section('content')



<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                              <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation ?? 0 }}" >
                              <!-- Generate Invoice Button -->
                              <span class="card-title">   {{__('sales.list_title')}}  {{ $warehouse->name ?? ''}} </span>
                              
                            <button type="button" class="btn pull-right m-r-10 btn-success btn-sm" id="generateInvoiceBtn" 
                            style="display:none;">
                                <i class="fas fa-file-invoice"></i> {{__('buy.generate_invoice')}}
                            </button>

                              <!-- Responsive Filter Toggle Button - Visible only on XS -->
                            <div class="pull-left" style="width:90px">
                                <button type="button" class="responsive_button btn btn-sm  visible-xs"
                                  id="filterToggleBtn" onclick="toggleFilterForm()"  style="margin-left:2px; margin-top:2px;">
                                   <i class="fas fa-filter"></i>
                                 </button>
                                 <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            </div>
                        </div>


                        <div class="filter-section no-print" id="searchWrapper">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="{{__('sales.customer')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="bill_number" placeholder="{{__('common.bill')}}" class="form-control">
                                    </div>

                                     <div class="col-md-2 col-sm-6 col-xs-6">
                                         <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date"  placeholder="{{__('common.start_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>
                                     <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="end_date" placeholder="{{__('common.end_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>


                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="warehouse_id" value="14" >
                                <span class="pull-left visible-print"> {{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="salesTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="11">
                                            <center> {{__('sales.list_title')}}   </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width:5%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.bill')}}  </th>
                                            <th> {{__('sales.customer')}} </th>
                                            <th> {{__('buy.factor')}} </th>
                                            <th> {{__('sales.total_price')}}</th>
                                            <th> {{__('buy.cur_pay')}}  </th>
                                            <th> {{__('buy.remained')}}  </th>
                                            <th> {{__('common.currency')}} </th>
                                            <th> {{__('common.date')}}  </th>
                                            <th> {{__('common.details')}}  </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="4">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>   
                                </table>
                            </div> <!-- /table responsive -->
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

@include('sales.scripts')
@endsection

