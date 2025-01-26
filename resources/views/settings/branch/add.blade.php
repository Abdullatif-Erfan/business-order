<!-- Button to trigger the modal -->
<button type="button" name="add" class="btn btn-primary btn-sm m-l-10 m-b-10" data-toggle="modal" data-target="#addModal">
    <span class="btn-label"> <i class="fa fa-plus"></i> </span>
    ثبت جدید
</button>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ثبت جدید</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form id="branchForm">
                    <div class="form-group">
                        <label for="name">نام شعبه</label>
                        <input type="text" class="form-control" id="name" required placeholder="نام را وارد کنید">
                        <span id="nameError" class="text-danger"></span>
                    </div>
                </form>
                <div id="loading" style="display:none;" class="text-center">
                    <i class="fa fa-spinner fa-spin"></i> در حال بارگذاری...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">بستن</button>
                <button type="button" class="btn btn-success btn-sm m-r-10" id="submitBtn">ثبت</button>
            </div>
        </div>
    </div>
</div>
