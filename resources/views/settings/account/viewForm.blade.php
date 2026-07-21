<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
          <div class="table-responsive">
           <table class="table table-bordered table-hover">
              <tr>
                <th width="20%" style="background: #f8f9fa;">{{ __('settings.account_type_selection') }}</th>
                <td width="30%"><strong>{{ $account->accountType->name ?? '' }}</strong></td>
                <th width="20%" style="background: #f8f9fa;">{{ __('settings.account_name') }}</th>
                <td width="30%"><strong>{{ $account->name ?? '' }}</strong></td>
              </tr>

              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.phone') }}</th>
                <td>{{ $account->phone ?? '-' }}</td>
                <th style="background: #f8f9fa;">{{ __('settings.address') }}</th>
                <td>{{ $account->address ?? '-' }}</td>
              </tr>
              
              <!-- Account Type 5: Shareholder -->
              @if($account->account_type_id == 5)
              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.percentage') }}</th>
                <td colspan="3">
                    @if($account->percent > 0)
                        <span class="badge badge-info">{{ $account->percent }} %</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
              </tr>
              @endif

              <!-- Account Type 1: Company -->
              @if($account->account_type_id == 1)
              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.default_account') }}</th>
                <td colspan="3">
                    @if($account->is_pre_select == 1)
                        <span class="badge badge-success">{{ __('settings.yes') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('settings.no') }}</span>
                    @endif
                </td>
              </tr>
              @endif

              <!-- Account Type 2: Employee -->
              @if($account->account_type_id == 2)
              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.net_salary') }}</th>
                <td>
                    @if($account->net_salary > 0)
                        <strong>{{ number_format($account->net_salary, 2) }}</strong>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <th style="background: #f8f9fa;">{{ __('settings.car') }}</th>
                <td>
                    @if($account->car)
                        <span class="badge badge-info">{{ $account->car->name }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
              </tr>
              <tr>
                <th style="background: #f8f9fa;">{{ __('common.start_date') }}</th>
                <td colspan="3">
                    @if($account->emp_start_date)
                        <i class="fas fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($account->emp_start_date)->format('Y-m-d') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
              </tr>
              @endif

              <!-- Account Type 7: Car -->
              @if($account->account_type_id == 7)
              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.car') }}</th>
                <td colspan="3">
                    @if($account->car)
                        <span class="badge badge-info"><i class="fas fa-car"></i> {{ $account->car->name }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
              </tr>
              @endif

              <!-- Account Type 3 or 4: Customer or Supplier -->
              @if($account->account_type_id == 3 || $account->account_type_id == 4)
              <tr>
                <th style="background: #f8f9fa;">{{ __('settings.loan_limit_label') }}</th>
                <td colspan="3">
                    @php
                        $hasLimit = (int)$account->loan_limit > 0;
                    @endphp
                    @if($hasLimit)
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> {{ number_format($account->loan_limit, 2) }}
                        </span>
                        @if($account->loan_limit_option == 1)
                            <span class="badge badge-info">{{ __('settings.yes') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('settings.no') }}</span>
                        @endif
                    @else
                        <span class="badge badge-secondary">
                            <i class="fas fa-minus-circle"></i> {{ __('settings.noactive') }}
                        </span>
                    @endif
                </td>
              </tr>
              @endif

           </table>
          </div>
          
            
            @if($journals->count() > 0)
            <div class="col-12" style="margin-top: 20px;">
              <hr />
               <h4><i class="fas fa-history"></i> {{ __('settings.paid_old_journal') }}</h4>
            </div>
           
            <!-- form repeater -->
            <div id="formContainer" class="col-12" style="padding:10px; background: #f9f9f9; border-radius: 5px;">
                <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th style="text-align:center; width:10%;">{{ __('common.number') }}</th>
                            <th style="text-align:center; width:25%;">{{ __('settings.amount') }}</th>
                            <th style="text-align:center; width:35%;">{{ __('common.talab') }} / {{ __('common.baqi') }}</th>
                            <th style="text-align:center; width:30%;">{{ __('settings.name_label') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journals as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center"><strong>{{ number_format($item['amount'], 2) }}</strong></td>
                                <td class="text-center">
                                    @if($item['options'] == 1)
                                        <span class="badge badge-primary">{{ __('settings.increase_cache') }}</span>
                                    @elseif($item['options'] == 2)
                                        <span class="badge badge-warning">{{ __('settings.save_in_talabat') }}</span>
                                    @elseif($item['options'] == 3)
                                        <span class="badge badge-danger">{{ __('settings.save_in_qarza') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('common.unknown') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(isset($item['currency_id']) && $item['currency_id'])
                                        @php
                                            $currency = \App\Models\Setting\Currency::find($item['currency_id']);
                                        @endphp
                                        {{ $currency->name ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background: #f1f3f5; font-weight: bold;">
                        <tr>
                            <td colspan="1" class="text-center">{{ __('common.total') }}</td>
                            <td class="text-center">{{ number_format(collect($journals)->sum('amount'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
            <!-- /form repeater -->
            @endif


    </div>
</form>

<style>
    
    .badge {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 4px;
    }
    .badge-success {
        background-color: #28a745;
        color: #fff;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }
    .badge-primary {
        background-color: #007bff;
        color: #fff;
    }
    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }
    .badge-secondary {
        background-color: #6c757d;
        color: #fff;
    }
    .text-muted {
        color: #6c757d !important;
    }
    hr {
        margin: 20px 0;
        border-top: 2px solid #dee2e6;
    }
    h4 {
        color: #495057;
        font-weight: 600;
    }
    .table-responsive {
        overflow-x: auto;
    }
    i.fas {
        margin-right: 5px;
    }
</style>