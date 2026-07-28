<div class="container-fluid">
        <input type="hidden" name="id" value="{{ $return->id }}">
        <input type="hidden" name="billno" value="{{ $return->billno }}">
        <input type="hidden" id="limit" value="{{ $return->remaining_amount }}">

        <div class="row">
            <div class="col-md-12">

                <!-- Return Details Table (Readonly Info) -->
                <table class="table table-bordered">
                    <tr>
                        <th width="20%" style="background:#f8f9fa;">{{ __('buy.return_number') }}</th>
                        <td width="30%"><strong>{{ $return->return_number }}</strong></td>
                        <th style="background:#f8f9fa;">{{ __('common.bill') }}</th>
                        <td>BUY_{{ $return->billno }}</td>
                    </tr>
                    <tr>
                        <th style="background:#f8f9fa;">{{ __('order.supplier_name') }}</th>
                        <td>{{ $return->supplier->name ?? '-' }}</td>
                        <th style="background:#f8f9fa;">{{ __('common.return_date') }}</th>
                        <td>{{ $return->return_date ? $return->return_date->format('Y-m-d') : '-' }}</td>
                    </tr>
                    <tr>
                        <th style="background:#f8f9fa;">{{ __('sales.quantity') }}</th>
                        <td><strong>{{ number_format($return->quantity, 2) }}</strong></td>
                        <th style="background:#f8f9fa;">{{ __('sales.item') }}</th>
                        <td>{{ $return->preList->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th style="background:#f8f9fa;">{{ __('common.total_price') }}</th>
                        <td><strong>{{ number_format($return->total, 2) }}</strong></td>
                        <th style="background:#f8f9fa;">{{ __('common.user') }}</th>
                        <td>{{ $return->user_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th style="background:#f8f9fa;">{{ __('buy.paid_amount') }}</th>
                        <td><strong>{{ number_format($return->paid_amount, 2) }}</strong></td>
                        <th style="background:#f8f9fa;">{{ __('buy.remaining_amount') }}</th>
                        <td> {{ number_format($return->remaining_amount, 2) }}</td>
                    </tr>
                </table>

                <!-- Editable Fields - Inside Form -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="paid_amount">{{ __('buy.payment_price') }} <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control" name="paid_amount" id="paid_amount" 
                                   placeholder="{{ __('common.amount') }}" min="0" max="{{$return->remaining_amount}}" required>
                        </div>
                        <small>{{__('common.max_payable')}}: {{$return->remaining_amount}} </small>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_type">{{ __('common.payment_type') }} <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="payment_type" id="payment_type" required>
                                <option value=""> --- {{__('common.options')}} ---</option>
                                <option value="1">{{ __('common.cash') }}</option>
                                <option value="2">{{ __('common.loan') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payer_account_id">{{ __('journal.payer_account') }} <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="payer_account_id" id="payer_account_id" required>
                                <!-- <option value=""> --- {{__('order.supplier_selection')}} ---</option> -->
                                    <option value="{{ $return->supplier_account_id }}">{{ $return->supplier->name ?? '-' }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="receiver_account_id">{{ __('journal.receiver_account') }} <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="receiver_account_id" id="receiver_account_id" required>
                                <option value=""> --- {{__('buy.company_account')}} ---</option>
                                @foreach($ownBanks as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>