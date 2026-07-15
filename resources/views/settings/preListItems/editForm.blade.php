<form id="preListEditForm">
<input type="hidden" name="id" value="{{ $buyPreLists[0]->id }}">
@csrf
<div class="form-body">
    <div class="row"> 
           
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <select name="category_id" class="form-control select2" style="width:100%">
                        <option value="">{{__('buy.select_category')}}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                             {{ $category->id == $buyPreLists[0]->category_id ? 'selected':''}}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div> 
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" id="name" name="name" value="{{ $buyPreLists[0]->name }}" type="text" required placeholder="{{__('common.item_name')}}" >
                    <span id="preListNameError2" class="text-danger"></span>
                </div> 
            </div>	

            <div class="col-12">
                <div id="loading2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> {{__('common.loading')}}
                </div>
            </div>

      </div>
    </div>  <!-- /form-body -->
</form>