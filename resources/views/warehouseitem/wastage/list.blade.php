@extends('layouts.app')

@section('content')

<style>
.dataTables_filter {
    display: none !important;
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
                            <a href="{{route('warehousesList.wastage.create')}}">
                               <button type="button" class="btn btn-sm mybtn pull-right"><i class="fas fa-plus"></i> {{__('common.add')}}</button>  
                            </a>                         
                            <span class="">  {{__('wh.wastage_title')}} </span>
                            <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">

                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <input type="text" id="item_name" placeholder="{{__('common.item_name')}}" class="form-control">
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
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="{{__('common.start_date')}}"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                

                                     <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="end_date" id="end_date"
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="{{__('common.end_date')}}"  data-placement="right" data-englishnumber="true" >
                                        </div>
							     	</div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <span class="pull-left visible-print">{{__('common.print_date')}} : {{ $todaysDate }}</span>
                                <table id="warehouseItemTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                            <img src="{{ asset($orgbios[0]->header) }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="10">
                                            <center> {{__('wh.wastage_list_title')}}  </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> {{__('common.number')}} &nbsp; </th>
                                            <th> {{__('common.name')}} </th>
                                            <th> {{__('wh.wastage_amount')}} </th>
                                            <th> {{__('common.unit')}} </th>
                                            <th> {{__('wh.wastage_up')}} </th>
                                            <th> {{__('common.total')}} </th>
                                            <th> {{__('common.currency')}} </th>                                            
                                            <th> {{__('common.expired_date')}} </th>
                                            <th> {{__('common.save_date')}}</th>
                                            <th> {{__('common.user')}} </th>
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

@include('warehouseitem.wastage.scripts')
@endsection

