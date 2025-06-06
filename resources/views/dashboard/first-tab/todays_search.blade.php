<div class="col-12">
    <form action="{{ route('home.search') }}" method="POST" id="firstTabSearch">
        @csrf
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-6">
               <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="currency_id">
                    <option value="{{ $data['currency_id'] }}"> {{ $data['currency_name'] }}</option>
                    <option value=""> -- {{ __('dashboard.search_currecny') }} -- </option>
                    @foreach($data['currency'] as $key => $val)
                        <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="year">
                    <option value="{{ $data['year'] }}">{{ $data['year'] }}</option>
                    <option value="">-- {{ __('dashboard.search_year') }}  --</option>
                    @for($i = 1400; $i <= 1440; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="month">
                    <option value="{{ $data['month'] }}">{{ $data['month'] == 100 ? 'همه': $data['month']  }}</option>
                    <option value="">--  {{ __('dashboard.search_month') }} --</option>
                    <option value="100">{{ __('dashboard.search_all') }}</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="day">
                    <option value="{{ $data['day'] }}">{{ intval($data['day']) == 100 ? 'همه' : intval($data['day']) }}</option>
                    <option value="">--  {{ __('dashboard.search_day') }} --</option>
                    <option value="100">{{ __('dashboard.search_all') }}</option>
                    @for($i = 1; $i <= 31; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <button class="btn mybtn search_btn form-control" style="margin-top:5px">
                    <i class="fa fa-search"></i>
                </button>
            </div>

        </div>
    </form>
</div>
