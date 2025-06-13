<form id="warehouseForm">
    @csrf
    <input type="hidden" id="warehouseId">

    <div class="form-group">
        <label for="name">{{ __('settings.warehouse_name') }}</label>
        <input type="text" class="form-control" name="name" required placeholder="{{ __('settings.enter_warehouse_name') }}">
        <span id="wHnameError" class="text-danger"></span>
    </div>

    <div class="form-group">
        <label for="branch_id">{{ __('settings.related_branch') }}</label>
        <select class="form-control select2" id="branch_id" required style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id">
            @foreach($branchs as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>
        <span id="branchError" class="text-danger"></span>
    </div>

    <div class="form-group">
        <label for="responsible">{{ __('settings.responsible_person') }}</label>
        <input type="text" class="form-control" name="responsible" required placeholder="{{ __('settings.enter_responsible_name') }}">
        <span id="responsibleError" class="text-danger"></span>
    </div>

    <div class="form-group">
        <label for="address">{{ __('settings.address') }}</label>
        <input type="text" class="form-control" name="address" required placeholder="{{ __('settings.enter_address') }}">
        <span id="addressError" class="text-danger"></span>
    </div>
</form>
