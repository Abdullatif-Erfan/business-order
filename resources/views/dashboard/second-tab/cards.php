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
                            <h4 class="card-title"><?php
                            /**
                             * سرمایه شرکت  = موجودی گدام + پول موجود
                             * موجودی گدام = مبلغ گدام ثبت شده  - اجناس فروش شده
                             * پول موجود  = دریافت ها - پرداخت 
                             * طلبات + شود 
                             * قرضه منفی شود
                             * === در نهایت =====
                             *  سرمایه شرکت  = پول نقد + طلبات + اجناس موجود - قرضه
                             */
                            $cache_balance  = $secondTabData[0]['banks_income'] - $secondTabData[0]['banks_outcome'];
                            $warehouse_price = $secondTabData[0]['total_stock'] - $secondTabData[0]['total_sales'];
                            $total_asset = (($cache_balance +  $warehouse_price + $secondTabData[0]['total_talab']) - $secondTabData[0]['total_loan']);

                            // $total_assets = $cache_balance + $secondTabData[0]['total_talab'] - $secondTabData[0]['total_loan']; 
                            echo number_format($total_asset,2);
                            // echo number_format($total_assets,2);
                            
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
                            <h4 class="card-title"><?=number_format($cache_balance,2)?></h4>
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
                            <h4 class="card-title"><?=number_format($secondTabData[0]['profit'],2)?></h4>
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
                            <h4 class="card-title"><?php
                            echo    number_format($secondTabData[0]['total_talab'],2)?></h4>
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
                            <h4 class="card-title"><?=number_format($secondTabData[0]['total_loan'],2)?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <?php $final_result =  $secondTabData[0]['total_talab'] - $secondTabData[0]['total_loan']; ?>
        <?php if(intval($final_result) < 0) { ?>
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
                            <h4 class="card-title"><?=number_format($final_result,2)?></h4>
                            <small>شرکت باقی است</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
       <?php } else if(intval($final_result) > 0) { ?>
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
                            <h4 class="card-title"><?=number_format($final_result,2)?></h4>
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
                            <h4 class="card-title"><?=number_format($final_result,2)?></h4>
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
        
