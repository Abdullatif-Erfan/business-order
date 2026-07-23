<style>
.user-detail-card {
border-radius: 10px;
box-shadow: 0 2px 20px rgba(0,0,0,0.06);
overflow: hidden;
}
.user-detail-card .card-header {
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
padding: 15px 20px !important;
color: #fff;
}
.user-detail-card .card-header a {
color: #fff !important;
}
.user-detail-card .card-header .card-title {
font-size: 18px;
font-weight: 600;
}
.user-photo-container {
position: relative;
display: inline-block;
}
.user-status-badge {
position: absolute;
bottom: 5px;
right: 5px;
padding: 4px 12px;
border-radius: 20px;
font-size: 11px;
font-weight: 600;
}
.badge-admin {
background: #dc3545;
color: #fff;
}
.badge-user {
background: #17a2b8;
color: #fff;
}
.assigned-item {
display: inline-block;
padding: 4px 12px;
margin: 2px 4px;
border-radius: 15px;
font-size: 12px;
font-weight: 500;
}
.assigned-car {
background: #e3f2fd;
color: #0d47a1;
border: 1px solid #90caf9;
}
.assigned-customer {
background: #e8f5e9;
color: #1b5e20;
border: 1px solid #a5d6a7;
}
</style>

        
        <div class="card-body" style="padding: 25px;">
            <div class="row">
                <!-- User Photo -->
                <div class="col-md-3 text-center">
                    <div class="user-photo-container">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" 
                                alt="{{ $user->full_name }}" 
                                style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #ddd;">
                        @else
                            <div style="width: 150px; height: 150px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="mt-3">{{ $user->full_name }}</h4>
                    <span class="badge {{ $user->isAdmin == 1 ? 'badge-danger' : 'badge-info' }}" style="font-size: 14px; padding: 6px 16px;">
                        {{ $user->isAdmin == 1 ? __('user.admin') : __('user.simple_user') }}
                    </span>
                    <div class="mt-2">
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('user.user_edit') }}
                        </a>
                    </div>
                </div>

                <!-- User Details -->
                <div class="col-md-9">
                    <div class="table-responsive">
                        <table class="table table-bordered ">
                            <tbody>
                                <tr>
                                    <th width="25%" style="background: #f8f9fa;">{{ __('user.user_name') }}</th>
                                    <td width="25%"><strong>{{ $user->user_name }}</strong></td>
                                    <th width="25%" style="background: #f8f9fa;">{{ __('user.email') }}</th>
                                    <td width="25%">{{ $user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th style="background: #f8f9fa;">{{ __('user.role') }}</th>
                                    <td>{{ $user->roleRelationName->role ?? '-' }}</td>
                                    <th style="background: #f8f9fa;">{{ __('journal.account') }}</th>
                                    <td>
                                        @if($user->account)
                                            <span class="badge badge-primary">{{ $user->account->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #f8f9fa;">{{ __('user.assigned_cars') }}</th>
                                    <td colspan="3">
                                        @if($assignedCars && $assignedCars->count() > 0)
                                            @foreach($assignedCars as $car)
                                                <span class="assigned-item assigned-car">
                                                    <i class="fas fa-car"></i> {{ $car->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #f8f9fa;">{{ __('user.assigned_customers') }}</th>
                                    <td colspan="3">
                                        @if($assignedCustomers && $assignedCustomers->count() > 0)
                                            @foreach($assignedCustomers as $customer)
                                                <span class="assigned-item assigned-customer">
                                                    <i class="fas fa-user"></i> {{ $customer->name }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="background: #f8f9fa;">{{ __('user.created_at') }}</th>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    <th style="background: #f8f9fa;">{{ __('user.updated_at') }}</th>
                                    <td>{{ $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

            
                </div>
            </div>
        </div> <!-- /card-body -->
