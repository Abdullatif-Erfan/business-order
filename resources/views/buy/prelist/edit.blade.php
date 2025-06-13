<form  id="updatePreListForm">
<input type="hidden" name="id" value="{{ $buyPreLists[0]->id }}">
<input type="hidden" name="branch_id" value="{{ $branchs->first()->id }}">
@csrf
<div class="form-body">
    <div class="row"> 
           
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    <input class="form-control" id="name" name="name" value="{{ $buyPreLists[0]->name }}" type="text" required placeholder="{{__('common.item_name')}}" >
                    <span id="nameError2" class="text-danger"></span>
                </div> 
            </div>	

            @if(session('package_type') == 4)
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group col-12">
                    <input type="file" name="image" accept="jpg,png,jpeg" class="form-control">
                    <span id="imageError2" class="text-danger"></span>
                </div>
            </div>
            @endif

            <div class="col-12">
                <div id="loading2" style="display:none; text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>

      </div>
    </div>  <!-- /form-body -->
</form>