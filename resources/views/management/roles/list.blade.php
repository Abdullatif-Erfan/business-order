@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
        $(document).ready(function(){
            showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
        });
    </script>
@endif
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			// columnDefs: [
            //     { width: '150px', targets: 6 } // Adjust the index (6) to the correct column index
            // ]
        });
    });
</script>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center" style="padding:10px;">
                            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-default pull-right"> 
                                <span class="fas fa-plus-square"></span> &nbsp; ثبت جدید 
                            </a>
                            <span class="card-title"> لیست رول ها </span>
                        </div>
                        <div class="card-body">
						<table id='myTable'  class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>شماره</th>
                                        <th>رول</th>
                                        <th>وضعیت</th>
                                        <th class="text-center"> تعیین صلاحیت</th>
                                        <th class="text-center">ویرایش</th>
                                        <th class="text-center">حذف</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $key => $role)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $role->role }}</td>
                                            <td>{{ $role->status ? 'فعال' : 'غیرفعال' }}</td>

											<td class="text-center">
											     <a href="{{ route('roles.permissions', ['roleId' => $role->roleId]) }}" class="btn btn-sm btn-primary">+ علاوه</a>
											</td>
                                            <td class="text-center">
												  <a href="{{ route('roles.edit', ['roleId' => $role->roleId]) }}">
                                                    <button  class="btn btn-sm btn-info" >ویرایش</button>
												  </a>
                                            </td>

											<td class="text-center">
											    <a href="{{ route('roles.destroy', ['roleId' => $role->roleId]) }}">
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="return confirm('آیا مطمئن هستید؟')">حذف</button>
												  </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $roles->links() }}


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
