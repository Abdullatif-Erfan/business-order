<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #dbf3ff; color: #3f7cc7; font-size: 18px;">
                        <i class="fa fas fa-shopping-cart"></i>
                    </span>
                    <div>
                        <small class="text-muted">فروشات</small>
                        <h5 class="mb-1"><b><a href="#"><?php if(intval($currency_id) == 1) { 
                            echo number_format($todays_sold_income[0]['total_sales'] ?? "0"); } else { echo '0'; } ?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #dbf3ff; color: #3f7cc7; font-size: 18px;">
                        <i class="far fa-money-bill-alt"></i>
                    </span>
                    <div>
                        <small class="text-muted">دریافت فروشات</small>
                        <h5 class="mb-1"><b><a href="#"><?php if(intval($currency_id) == 1) { 
                            echo number_format($todays_sold_income[0]['cur_pay'] ?? "0"); } else { echo '0'; } ?></a></b></h5>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #dbf3ff; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-donate"></i>
                    </span>
                    <div>
                        <small class="text-muted">طلب فروشات</small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($todays_sold_income[0]['remained'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #dbf3ff; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-money-check-alt"></i>
                    </span>
                    <div>
                        <small class="text-muted"> مفاد فروشات</small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($todays_sold_income[0]['profit'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- second row -->
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #ebd997; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-dolly-flatbed"></i>
                    </span>
                    <div>
                        <small class="text-muted"> خریداری </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($getTodaysBoughtMedicine[0]['total_bought']+$getTodaysBoughtItems[0]['total_bought'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #ebd997; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    <div>
                        <small class="text-muted">  پرداخت  خرید </small>
                        <h5 class="mb-1"><b><a href="#">
                            <?=number_format($getTodaysBoughtMedicine[0]['cur_pay']+$getTodaysBoughtItems[0]['cur_pay'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #ebd997; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-download"></i>
                    </span>
                    <div>
                        <small class="text-muted"> قرضه خرید </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($getTodaysBoughtMedicine[0]['remained']+$getTodaysBoughtItems[0]['remained'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #ebd997; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-download"></i>
                    </span>
                    <div>
                        <small class="text-muted">  مصارف ترانسپورت </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($getTodaysBoughtMedicine[0]['trans_spend']+$getTodaysBoughtItems[0]['trans_spend'])?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- third row -->
        
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center" >
                    <span class="stamp stamp-md  ml-3" style="background-color: #b7c652; color: #3f7cc7; font-size: 18px;">
                        <i class="fa fa-arrow-circle-down" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted">  ورود به خزانه </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['khazana_income'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #b7c652; color: #3f7cc7; font-size: 18px;">
                        <i class="fa fa-arrow-circle-up" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted">  خروج از خزانه </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['khazana_outcome'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center" >
                    <span class="stamp stamp-md ml-3" style="background-color: #b7c652; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-arrow-down" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted">  ورود به بانک </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['banks_income'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color: #b7c652; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-arrow-up" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted">  خروج از بانک </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['banks_outcome'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- fourth row -->
        <div class="col-sm-6 col-lg-6">
            <div class="card p-3" style="border: 1px solid #83d31a;background: linear-gradient(45deg, #ffffff, #f0ffd1)">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-success ml-3">
                        <i class="fas fa-arrow-down" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted"> مجموع آمد نقد </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['total_incomes'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-6">
            <div class="card p-3" style="border: 1px solid #d39d1a;background: linear-gradient(45deg, #ffffff, #f9edde)">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md  ml-3" style="background-color: #d3b33b; color: #3f7cc7; font-size: 18px;">
                        <i class="fas fa-arrow-up" style="color: #fff;"></i>
                    </span>
                    <div>
                        <small class="text-muted"> مجموع رفت نقد   </small>
                        <h5 class="mb-1"><b><a href="#"><?=number_format($cashIncomeOutcome[0]['total_spend'],2)?></a></b></h5>
                    </div>
                </div>
            </div>
         </div>


    </div>
</div>