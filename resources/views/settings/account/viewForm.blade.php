<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
          <div class="table-responsive">
           <table class="table table-bordered">
              <tr>
                <th width="20%">{{ __('settings.account_type_selection') }}</th>
                <td width="30%">{{ $account->accountType->name ?? '' }}</td>
                <th width="20%">{{ __('settings.account_name') }}</th>
                <td width="30%">{{ $account->name ?? '' }}</td>
              </tr>

              <tr>
                <th>{{ __('settings.phone') }}</th>
                <td>{{ $account->phone ?? '' }}</td>
                <th>{{ __('settings.address') }}</th>
                <td>{{ $account->address ?? '' }}</td>
              </tr>
              
              <!-- ✅ FIXED: Separate row for account_type_id == 5 -->
              @if($account->account_type_id == 5)
              <tr>
                <th>{{ __('settings.percentage') }}</th>
                <td colspan="3">{{ $account->percent > 0 ? $account->percent . ' %' : '' }}</td>
              </tr>
              @endif

              <!-- ✅ FIXED: Separate row for account_type_id == 1 -->
              @if($account->account_type_id == 1)
              <tr>
                <th>{{ __('settings.default_account') }}</th>
                <td colspan="3">{{ $account->is_pre_select == 1 ? __('settings.yes') : __('settings.no') }}</td>
              </tr>
              @endif

              <!--  Separate rows for account_type_id == 2 -->
              @if($account->account_type_id == 2)
              <tr>
                <th>{{ __('settings.net_salary') }}</th>
                <td>{{ number_format($account->net_salary, 2) }}</td>
                <th>{{ __('settings.car') }}</th>
                <td>{{ $account->car->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>{{ __('common.start_date') }}</th>
                <td colspan="3">{{ $account->emp_start_date ? \Carbon\Carbon::parse($account->emp_start_date)->format('Y-m-d') : '-' }}</td>
              </tr>
              @endif

              <!--  Separate row for account_type_id == 3 or 4 -->
              @if($account->account_type_id == 3 || $account->account_type_id == 4)
              <tr>
                <th>{{ __('settings.loan_limit_label') }}</th>
                <td colspan="3">
                    @php
                        $hasLimit = (int)$account->loan_limit > 0;
                        $icon = $hasLimit ? 
                            ($account->loan_limit_option == 1 ? 
                                '<i class="fas fa-check-circle text-success"></i>' : 
                                '<i class="fas fa-times-circle text-danger"></i>') : 
                            '<i class="fas fa-minus-circle text-muted"></i>';
                    @endphp
                    {!! $icon !!}
                    @if($hasLimit)
                        {{ $account->loan_limit }}
                        <span class="text-muted">({{ $account->loan_limit_option == 1 ? __('settings.yes') : __('settings.no') }})</span>
                    @else
                        <span class="text-muted">{{ __('settings.noactive') }}</span>
                    @endif
                </td>
              </tr>
              @endif

           </table>
          </div>
          
            
            @if($journals->count() > 0)
            <div class="col-12">
              <hr />
               <h3>{{ __('settings.paid_old_journal') }}</h3>
            </div>
           
            <!-- form repeater -->
            <div id="formContainer" class="col-12" style="padding:10px;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('common.number') }}</th>
                            <th>{{ __('settings.amount') }}</th>
                            <th>{{ __('common.talab') }} / {{ __('common.baqi') }}</th>
                            <th>{{ __('settings.name_label') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journals as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ number_format($item['amount'], 2) }}</td>
                                <td>
                                    @if($item['options'] == 1)
                                        {{ __('settings.increase_cache') }}
                                    @elseif($item['options'] == 2)
                                        {{ __('settings.save_in_talabat') }}
                                    @elseif($item['options'] == 3)
                                        {{ __('settings.save_in_qarza') }}
                                    @else
                                        {{ __('common.unknown') }}
                                    @endif
                                </td>
                                <td>{{ $item->currencyRelation->name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /form repeater -->
            @endif


    </div>
</form>