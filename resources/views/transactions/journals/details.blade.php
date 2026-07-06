@extends('layouts.app')

@section('content')
<script>
    function submitAccountIdToURL() {
        var account_id = parseInt(document.getElementById('account_id').value);
        var base_url = document.getElementById('base_url').value;
        if (account_id > 0) {
            window.location.href = base_url + "/reports/ledger/" + account_id;
        } else {
            alert("{{ __('journal.select_account') }}");
        }
    }
</script>
<style>
    .dt-button { display: none !important; }
    #table_filter { display: none !important; }
    table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th:first-child:before {
        display: none !important;
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
                    <div class="card">
                        <div class="col-12" style="padding: 20px; margin-bottom: 10px;">
                            <div class="row">
                                <a href="{{ route('journal.index') }}">
                                    <button class="printBtn">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                </a>
                            </div>
                        </div>

                        <div class="card-body" style="border-top: 1px solid #ddd; margin-top: 10px">
                            <div class="row">
                                <div class="col-md-6 col-xs-8 col-xs-12" id="print_area">
                                    <table class="table table-bordered" style="width:50%">
                                        <tr>
                                            <td colspan="2" style="background-color: #3f7cc7; color: #fff; text-align:center; font-size: 20px; padding: 4px">
                                                {{ __('journal.details_title') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 120px;">{{ __('journal.code') }}</td>
                                            <td>{{ $journals[0]['code'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('journal.register_date') }}</td>
                                            <td>{{ $journals[0]['idate'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{__('journal.account')}} {{ intval($journals[0]['transaction_type']) === 1 ? __('journal.account_receive') : __('journal.account_pay') }}</td>
                                            <td>{{ $journals[0]->accountRelation->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('journal.amount') }}</td>
                                            <td>{{ number_format($journals[0]['amount'],2) }} {{ $journals[0]->currencyRelation->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('journal.amount_in_words') }}</td>
                                            <td>{{$journals[0]['amount_in_words']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('journal.details') }}</td>
                                            <td>{{ $journals[0]['details'] }}</td>
                                        </tr>

                                        <!-- second account -->
                                        <tr style="background-color:#f2fbfb">
                                            <td>{{__('journal.account')}}  {{ intval($journals[1]['transaction_type']) === 1 ? __('journal.account_receive') : __('journal.account_pay') }}</td>
                                            <td>{{ $journals[1]->accountRelation->name ?? '' }}</td>
                                        </tr>
                                        <tr style="background-color:#f2fbfb">
                                            <td>{{ __('journal.amount') }}</td>
                                            <td>{{ number_format($journals[1]['amount'],2) }} {{ $journals[1]->currencyRelation->name }}</td>
                                        </tr>
                                        <tr style="background-color:#f2fbfb">
                                            <td>{{ __('journal.amount_in_words') }}</td>
                                            <td>{{$journals[1]['amount_in_words']}}</td>
                                        </tr>
                                        <tr style="background-color:#f2fbfb">
                                            <td>{{ __('journal.details') }}</td>
                                            <td>{{ $journals[1]['details'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('journal.registered_by') }}</td>
                                            <td>{{ $journals[1]->user_name ?? '' }}</td>
                                        </tr>

                                        <!-- action buttons -->
                                        <tr>
                                            <td colspan="2" style="padding: 10px;">
                                                <!-- return button -->
                                                <a href="{{ route('journal.index') }}" class="hidden-print">
                                                    <button class="btn btn-primary btn-sm btn-border m-r-10">
                                                        <i class="fas fa-arrow-left"></i> {{ __('journal.back') }}
                                                    </button>
                                                </a> 

                                                <!-- print button -->
                                                @if(isset($journals[0]) && $journals[0]->status == 2)
                                                    <a target="_blank" href="{{ route('journal.print', $journals[1]->times) }}" class="hidden-print">
                                                        <button class="btn btn-primary btn-sm btn-border m-r-10 hidden-print">
                                                            <i class="fas fa-print"></i> {{ __('journal.print_receipt') }}
                                                        </button>
                                                    </a>
                                                @endif

                                                <!-- edit button -->
                                                @if(isset($journals[0]) && $journals[0]->status == 2 && auth()->user()->hasAccess('journal', 'edit_records')) 
                                                    <a href="{{ route('journal.edit', $journals[0]->times) }}" class="hidden-print">
                                                        <button class="btn btn-primary btn-sm m-r-10">
                                                            <i class="fas fa-pen"></i> {{ __('journal.edit') }}
                                                        </button>
                                                    </a>
                                                @endif 

                                                <!-- delete button -->
                                                @if(isset($journals[0]) && $journals[0]->status == 2 && auth()->user()->hasAccess('journal', 'delete_records'))
                                                    <form style="display:inline" action="{{ route('journal.destroy', $journals[0]->times) }}" method="POST" onsubmit="return doConfirm();">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm m-r-10">
                                                            <i class="fas fa-trash error"></i> {{ __('journal.delete') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        <!-- / action buttons -->
                                    </table>
                                </div>

                                <div class="col-md-6 col-xs-4 col-xs-12">
                                    @if(empty($journals[0]['doc']))
                                        <form action="{{ route('journal.update_document') }}" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="times" value="{{ $journals[0]->times }}">
                                            @csrf
                                            @method('PATCH') 
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label m-t--15">
                                                    <label>{{ __('journal.document') }}</label>
                                                    <input type="file" class="form-control input-solid" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" required>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <input type="submit" name="submit" value="{{ __('journal.upload_document') }}" class="form-control btn btn-sm btn-primary">
                                            </div>
                                        </form>
                                    @else
                                        @php
                                            $extension = pathinfo($journals[0]['doc'], PATHINFO_EXTENSION);
                                            $path = asset("{$journals[0]['doc']}");
                                        @endphp
                                        @if(in_array($extension, ['jpg', 'jpeg', 'png','PNG', 'gif']))
                                            <img class="img-fluid" src="{{ $path }}" alt=""> 
                                        @else
                                            <a href="{{ $path }}">
                                                <button class="btn btn-sm"> {{ __('journal.download') }} <i class="fas fa-download"></i></button>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
