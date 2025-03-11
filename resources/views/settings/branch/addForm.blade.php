
<form id="branchForm">
   @csrf
   <div class="row">
    <div class="form-group col-md-6">
            <label for="name">نام شعبه</label>
            <input type="text" class="form-control" name="name" required placeholder="نام را بنویسید">
            <span id="bNameError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="responsible">مسؤل شعبه</label>
            <input type="text" class="form-control" name="responsible" required placeholder="نام مسؤل را بنویسید">
            <span id="bResponsibleError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address">شماره تماس</label>
            <input type="text" class="form-control" name="phone" required placeholder="شماره تماس / واتساپ">
            <span id="bPhoneError" class="text-danger"></span>
        </div>
        <div class="form-group col-md-6">
            <label for="address">ایمیل آدرس</label>
            <input type="email" class="form-control" name="email"  placeholder="ایمیل آدرس را بنویسید">
            <span id="bEmailError" class="text-danger"></span>
        </div>

        <div class="form-group col-md-12">
            <label for="address">آدرس دفتر</label>
            <input type="text" class="form-control" name="address" required placeholder="آدرس دفتر رابنویسید">
            <span id="bAddressError" class="text-danger"></span>
        </div>
   </div>

</form>

