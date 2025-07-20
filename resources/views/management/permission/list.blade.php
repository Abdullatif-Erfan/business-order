@extends('layouts.app')

@section('content')

<style>
    /* Ensure the table layout is correct */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        padding: 8px;
        text-align: right;
    }

    /* Style the checkboxes */
    input[type="checkbox"] {
        transform: scale(1.5); /* Make the checkboxes bigger */
        margin: 0 auto;
    }
</style>




<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:15px;">
                            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-default pull-left"> 
                                <span class="fas fa-arrow-left"></span> 
                            </a>
                            <span class="card-title pull-right"> {{__('user.priviledge_selection')}} </span>
                        </div>
                        <div class="card-body">
                        <form action="{{ route('roles.permissions.store_permission') }}" method="POST">
                        @csrf
                        <input type="hidden" name="roleId" value="{{ $roleId }}">
						<table class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>{{__('user.item')}}</th>
                                    <th>{{__('user.all_priviledges')}}</th>
                                    <th> {{__('user.view')}}</th>
                                    <th>{{__('user.add')}}</th>
                                    <th>{{__('common.edit')}}</th>
                                    <th>{{__('common.delete')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($roleAccessMatrix))
                                    @foreach(config('modules.moduleList') as $record)
                                        @php
                                            $key = array_search($record['module'], array_column($roleAccessMatrix, 'module'));
                                            $matrix = $key !== false ? (array) $roleAccessMatrix[$key] : null;
                                        @endphp

                                        <tr>
                                            <td>
                                                <b>{{ $record['label'] }}</b>
                                                <input type="hidden" name="access[{{ $record['module'] }}][module]" value="{{ $record['module'] }}" />
                                            </td>

                                            <td>
                                                <input type="checkbox" name="access[{{ $record['module'] }}][total_access]" 
                                                {{ isset($matrix['total_access']) && $matrix['total_access'] == 1 ? 'checked' : '' }} />
                                            </td>

                                            <td>
                                                <input type="checkbox" name="access[{{ $record['module'] }}][list]" 
                                                {{ isset($matrix['list']) && $matrix['list'] == 1 ? 'checked' : '' }} />
                                            </td>

                                            <td>
                                                <input type="checkbox" name="access[{{ $record['module'] }}][create_records]" 
                                                {{ isset($matrix['create_records']) && $matrix['create_records'] == 1 ? 'checked' : '' }} />
                                            </td>

                                            <td>
                                                <input type="checkbox" name="access[{{ $record['module'] }}][edit_records]" {{ isset($matrix['edit_records']) && $matrix['edit_records'] == 1 ? 'checked' : '' }} />
                                            </td>

                                            <td>
                                                <input type="checkbox" name="access[{{ $record['module'] }}][delete_records]" {{ isset($matrix['delete_records']) && $matrix['delete_records'] == 1 ? 'checked' : '' }} />
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>

                        </table>

                        <div class="box-footer clearfix">
                            <input type="submit" class="btn btn-primary" value="{{__('user.save_priviledge')}}" />

                            <a href="{{ route('roles.index') }}" style="margin-right:20px">
                                <button type="button" class="btn btn-warning"> {{__('user.cancel')}} </button>
                            </a>
                            <br />
                            <br />

                        </div>

                        </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
