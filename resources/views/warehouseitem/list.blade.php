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
                            <span class="card-title">   {{__('wh.existing_list')}} </span>
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
                            <div class="col-md-12 col-sm-12 col-xs-12 pr-10 pl-10">
                                <div class="row">

                                   <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="car_name" placeholder="{{__('common.car')}}" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('common.item_name')}}" class="form-control">
                                    </div>

                                      <div class="col-md-3 col-sm-6 col-xs-6">
                                         <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="start_date"  placeholder="{{__('common.start_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>
                                     <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="filter-group" style="min-width: 120px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker-input" id="end_date" placeholder="{{__('common.end_date')}}">
                                                <span class="input-group-text datepicker-icon"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
							     	</div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="filter-group">
                                            <select class="form-control" name="availability_options" id="availability_options">
                                                <!-- <option value=""> {{ __('wh.options') }}</option> -->
                                                <option value="1">{{ __('wh.available_items') }}</option>
                                                <option value="2">{{ __('wh.all') }}</option>
                                            </select>
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
                                <input type="hidden" id="tax_activation" value="{{ $orgbios[0]->tax_activation ?? 0}}" />
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="warehouseItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                             @if($orgbios[0]->tax_activation === 1)
                                             <td colspan="15">
                                             @else
                                             <td colspan="12">
                                             @endif 
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                             @if($orgbios[0]->tax_activation === 1)
                                             <td colspan="15">
                                             @else
                                             <td colspan="12">
                                             @endif 
                                            <center> {{__('wh.existing_list')}}  {{ $warehouse->name ?? '' }}  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.car')}} </th>
                                            <th> {{__('common.name')}} </th>
                                            <th> {{__('common.in')}} </th>
                                            <th> {{__('common.out')}} </th>
                                            <th> {{__('common.available')}}  </th>
                                            <th> {{__('common.unit')}} </th>
                                            <th> {{__('buy.buy_up')}} </th>
                                            @if($orgbios[0]->tax_activation === 1)
                                            <th>% {{__('buy.tax') ?? ''}}</th>
                                            <th>{{__('buy.buy_tax_price_s') ?? ''}}</th>
                                            <th> {{__('buy.buy_up_vat') ?? ''}} </th>
                                            @endif 
                                            <th> {{__('common.available_total')}} </th> 
                                            <th> {{__('common.total')}} </th> 
                                            <th> {{__('buy.sell_up')}} </th> 
                                            <th> {{__('common.date')}} </th>
                                             <th class="hidden-print">{{ __('common.transfer')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="4">{{__('common.total')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @if($orgbios[0]->tax_activation === 1)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            @endif 
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="hidden-print"></td>
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


<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="width:800px !important">
            <!-- <form action="{{ route('warehousesList.updateTransfer')}}" method="POST">
            @csrf -->
            <div class="modal-header">
                <h5 class="modal-title"> {{__('common.edit')}} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ModalContent"></div>
                <div id="loading" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin font-20"></i> {{__('common.loading')}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm"  data-dismiss="modal">{{__('common.close')}}</button>
                <button type="submit" id="submitTransfer" class="btn btn-success btn-sm m-r-10" >
                    {{__('common.transfer')}}
                </button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>


@include('warehouseitem.scripts')
@endsection

