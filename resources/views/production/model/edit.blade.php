<form id="updateModelForm">
    <input type="hidden" name="id" value="{{ $modelList[0]->id }}">
    @csrf
    <div class="form-body">
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="form-group">
                    <input class="form-control" id="name" name="name"
                           value="{{ $modelList[0]->name }}"
                           type="text" required
                           placeholder="{{__('common.item_name')}}">
                    <span id="nameError2" class="text-danger"></span>
                </div>

                <div class="col-12">
                    <div id="loading2" style="display:none; text-align: center;">
                        <i class="fa fa-spinner fa-spin"></i> {{__('common.loading')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
