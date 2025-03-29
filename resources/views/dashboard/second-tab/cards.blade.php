<div class="col-md-12 col-sm-12 col-xs-12">
   <div class="row m-t-10">

   
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="fas fa-money-check-alt text-info"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">سرمایه شرکت</p>
                            <h4 class="card-title">
                            <?php
                            /**
                             * === در نهایت =====
                             *  سرمایه شرکت  = پول نقد + طلبات + اجناس موجود - قرضه
                             */
                            /**
                             * total_assets = total_warehouse_value + total_cache_income(recieved-paid) + total_talabat - (total_warhouse_wastage + total_loan)
                             */
                            $total_talabat = $secondTab['talabat'] + $secondTab['cache_recieved'];
                            $total_loan = $secondTab['loans'] + $secondTab['cache_paid'];
                            $loan_talabat_balance =  $total_talabat - $total_loan;

                            $total_cache = $secondTab['total_income'] - $secondTab['total_outcome'];
                            $total_warehouse_value = $secondTab['total_warehouse_value'] - $secondTab['total_warehouse_wastage'];
                            $total_assets = $total_warehouse_value +  $total_cache + $loan_talabat_balance;
                            echo number_format($total_assets);
                            
                            ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                        <i class="fas fa-hand-holding-usd text-info"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"> پول نقد شرکت</p>
                            <h4 class="card-title"><?=number_format($total_cache)?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"> مفاد فروشات </p>
                            <h4 class="card-title"><?=number_format($secondTab['sold_profits'])?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="fas fa-sort-amount-up text-info"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"> طلبات </p>
                            <h4 class="card-title"><?=number_format($total_talabat)?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   

    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body ">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="fas fa-sort-amount-down text-info"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category">قرضه</p>
                            <h4 class="card-title"><?=number_format($total_loan)?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
        <?php if(intval($loan_talabat_balance) < 0) { ?>
            <div class="col-sm-6 col-md-4">
            <div class="card card-stats card-round" style="border: 1px solid #ff6600;background: linear-gradient(45deg, #ffffff, #ffd1d1)">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center" style="background:transparent !important">
                            <i class="fas fa-balance-scale text-primary" style="color:#d30505 !important"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                            <p class="card-category"> بیلانس طلبات و قرضه </p>
                            <h4 class="card-title"><?=number_format($loan_talabat_balance)?></h4>
                            <small>شرکت باقی است</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
       <?php } else if(intval($loan_talabat_balance) > 0) { ?>
        <div class="col-sm-6 col-md-4">

        <div class="card card-stats card-round" style="border: 1px solid #83d31a;background: linear-gradient(45deg, #ffffff, #f0ffd1)">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center" style="background:transparent !important">
                            <i class="fas fa-balance-scale text-primary" style="color: #fff;"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                        <p class="card-category"> بیلانس طلبات و قرضه </p>
                            <h4 class="card-title"><?=number_format($loan_talabat_balance)?></h4>
                            <small>شرکت طلب است</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
       <?php } else  { ?>
        <div class="col-sm-6 col-md-4">

        <div class="card card-stats card-round" style="border: 1px solid #83d31a;background: linear-gradient(45deg, #ffffff, #f0ffd1)">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center" style="background:transparent !important">
                            <i class="fas fa-balance-scale text-primary" style="color: #fff;"></i>
                        </div>
                    </div>
                    <div class="col-7 col-stats">
                        <div class="numbers">
                        <p class="card-category"> بیلانس طلبات و قرضه </p>
                            <h4 class="card-title"><?=number_format($loan_talabat_balance)?></h4>
                            <small>  تصفیه است</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
       <?php } ?>



</div>
</div>
        
