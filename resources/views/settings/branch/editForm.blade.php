
<form id="editBranchForm">
   @csrf
   <input type="hidden" name="id" value="{{ $branch->id ?? 0 }}">
   <div class="row">
    <div class="form-group col-md-6">
            <label for="name">نام شعبه</label>
            <input type="text" class="form-control" name="name" value="{{ $branch->name ?? '' }}" required placeholder="نام را بنویسید">
            <span id="bNameError2" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="responsible">مسؤل شعبه</label>
            <input type="text" class="form-control" name="responsible" value="{{ $branch->responsible ?? '' }}" required placeholder="نام مسؤل را بنویسید">
            <span id="bResponsibleError2" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address">شماره تماس</label>
            <input type="text" class="form-control" name="phone" required value="{{ $branch->phone ?? '' }}" placeholder="شماره تماس / واتساپ">
            <span id="bPhoneError2" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address">ایمیل آدرس</label>
            <input type="email" class="form-control" name="email" value="{{ $branch->email ?? '' }}"  placeholder="ایمیل آدرس را بنویسید">
            <span id="bEmailError2" class="text-danger"></span>
        </div>

        <div class="form-group col-md-12">
            <label for="address">آدرس دفتر</label>
            <input type="text" class="form-control" name="address" value="{{ $branch->address ?? '' }}" required placeholder="آدرس دفتر رابنویسید">
            <span id="bAddressError2" class="text-danger"></span>
        </div>
   </div>

</form>

