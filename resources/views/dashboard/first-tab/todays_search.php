<div class="col-12">
    <form action="{{ route('home') }}" method="POST" id="myForm">
        @csrf
        <div class="row">

            <div class="col-md-3 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="currency_id">
                    <option value="{{ $currency_id }}">{{ $currency_name }}</option>
                    <option value=""> -- انتخاب پول -- </option>
                    @foreach($currency as $key => $val)
                        <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="year">
                    <option value="{{ $year }}">{{ $year }}</option>
                    <option value="">-- انتخاب سال --</option>
                    @for($i = 1400; $i <= 1440; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="month">
                    <option value="{{ $month }}">{{ show_this_month($month) }}</option>
                    <option value="">-- انتخاب ماه --</option>
                    <option value="100">همه</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ show_this_month($i) }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <select class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" aria-hidden="true" name="day">
                    <option value="{{ $day }}">{{ intval($day) === 100 ? "همه" : $day }}</option>
                    <option value="">-- انتخاب روز --</option>
                    <option value="100">همه</option>
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
