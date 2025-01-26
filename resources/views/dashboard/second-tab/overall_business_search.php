<div class="col-12">
    <form id="myForm" action="home" method="post">
        <div class="row">

            <div class="col-md-5 col-sm-4 col-xs-6">
                <select 
                    class="form-control mt-1 mb-1"
                    style="width: 100%; border:1px solid #ddd !important;" 
                    aria-hidden="true" 
                    name="currency_id">
                    <option value="<?= $currency_id ?>"><?= $currency_name ?></option>
                    <option value="">-- انتخاب پول --</option>
                    <?php foreach ($currency as $key => $val): ?>
                        <option value="<?= $val['id'] ?>"><?= $val['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-5 col-sm-4 col-xs-6">
                <select 
                    class="form-control mt-1 mb-1" 
                    style="width: 100%; border:1px solid #ddd !important;" 
                    aria-hidden="true" 
                    name="year">
                    <option value="<?= $year ?>"><?= $year ?></option>
                    <option value="">-- انتخاب سال --</option>
                    <?php for ($i = 1400; $i <= 1440; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-2 col-sm-4 col-xs-6">
                <button 
                    class="btn mybtn search_btn form-control" 
                    style="margin-top: 5px;">
                    <i class="fa fa-search"></i>
                </button>
            </div>

        </div>
    </form>
</div>
