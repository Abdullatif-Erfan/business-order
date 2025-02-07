<form  id="updatePreListForm">
<input type="hidden" name="id" value="{{ $buyPreLists[0]->id }}">
@csrf
<div class="form-body">
    <div class="row"> 
           <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group col-12">
                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="branch_id" name="branch_id"  > 
                        @if ($branchs->count() > 1)
                            <option value="{{ $buyPreLists[0]->branch_id }}">{{ $buyPreLists[0]->branchRelation->name }}</option>
                            <option value="">--- انتخاب شعبه ---</option>
                        @endif
                        @foreach ($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <span id="branchIdError2" class="text-danger"></span>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    <input class="form-control" id="name" name="name" value="{{ $buyPreLists[0]->name }}" type="text" required placeholder="نام جنس" >
                    <span id="nameError2" class="text-danger"></span>
                </div> 
            </div>	

        <div class="col-12">
            <div id="loading2" style="display:none; text-align: center;">
                <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
            </div>
        </div>

    </div>
    </div>  <!-- /form-body -->
</form>