<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
          <div class="table-responsive">
           <table class="table table-bordered">
              <tr>
                <th>{{ __('settings.account_type_selection') }} </th><td>{{ $account->accountType->name ?? '' }}</td>
                <th> {{ __('settings.account_name') }}</th><td>{{ $account->name ?? '' }}</td>
              </tr>

              <tr>
                <th> {{ __('settings.phone') }} </th><td>{{ $account->phone }}</td>
                <th> {{ __('settings.address') }} </th><td>{{ $account->address }}</td>
              </tr>
              <tr>
                  @if($account->account_type_id == 5)
                  <th>{{ __('settings.percentage') }}</th><td>{{ $account->percent > 0  ? $account->percent .' % ' : '' }}</td>
                  @endif

                  @if($account->account_type_id == 1)
                  <th>{{ __('settings.default_account') }}</th><td>{{ $account->is_pre_select == 1 ? {{ __('common.yes') }}:{{ __('common.no') }} }}</td>
                  @endif

                  @if($account->account_type_id == 2)
                  <th> {{ __('settings.net_salary') }}</th><td>{{ number_format($account->net_salary,2) }}  </td>
                  @endif
              </tr>

           </table>
          </div>
          
            
            @if($journals->count() > 0)
            <div class="col-12">
              <hr />
               <h3> {{ __('settings.paid_old_journal')}} </h3>
            </div>
           

                <!-- form repeater -->
                <div id="formContainer" class="col-12" style="padding:10px;">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{__('common.number')}}</th>
                            <th>{{__('settings.amount')}}</th>
                            <th>{{__('common.talab')}} / {{__('common.baqi')}}</th>
                            <th>  واحد پولی </th>
                        </tr>
                    @foreach($journals as $index => $item) <!-- Loop through the data -->
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ number_format($item['amount'],2) }}</td>
                            <td>{{ $item['options'] == 1 ? 'افزایش پول نقد' : ($item['options'] == 2 ? 'ثبت در بخش طلبات' : 'ثبت در بخش قرضه') }}</td>
                            <td>{{ $item->currencyRelation->name ?? '' }}</td>
                        </tr>
                    @endforeach
                    </table>
                    </div>
                </div>
                <!-- /form repeater -->
            @endif


    </div>

    
</form>

