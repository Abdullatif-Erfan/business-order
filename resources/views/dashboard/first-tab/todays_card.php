<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="row">

        <!-- Card Component -->
        <?php
        $cards = [
            [
                'icon' => 'fas fa-shopping-cart',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'فروشات',
                'value' => intval($data['currency_id']) === 1 ? number_format($todays_sold_income[0]['total_sales'] ?? 0) : '0',
            ],
            [
                'icon' => 'far fa-money-bill-alt',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'دریافت فروشات',
                'value' => intval($data['currency_id']) === 1 ? number_format($todays_sold_income[0]['cur_pay'] ?? 0) : '0',
            ],
            [
                'icon' => 'fas fa-donate',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'طلب فروشات',
                'value' => number_format($todays_sold_income[0]['remained'] ?? 0),
            ],
            [
                'icon' => 'fas fa-money-check-alt',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'مفاد فروشات',
                'value' => number_format($todays_sold_income[0]['profit'] ?? 0),
            ],
            [
                'icon' => 'fas fa-dolly-flatbed',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'خریداری',
                'value' => number_format(($getTodaysBoughtMedicine[0]['total_bought'] ?? 0) + ($getTodaysBoughtItems[0]['total_bought'] ?? 0)),
            ],
            [
                'icon' => 'fas fa-hand-holding-usd',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'پرداخت خرید',
                'value' => number_format(($getTodaysBoughtMedicine[0]['cur_pay'] ?? 0) + ($getTodaysBoughtItems[0]['cur_pay'] ?? 0)),
            ],
            [
                'icon' => 'fas fa-download',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'قرضه خرید',
                'value' => number_format(($getTodaysBoughtMedicine[0]['remained'] ?? 0) + ($getTodaysBoughtItems[0]['remained'] ?? 0)),
            ],
            [
                'icon' => 'fas fa-download',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'مصارف ترانسپورت',
                'value' => number_format(($getTodaysBoughtMedicine[0]['trans_spend'] ?? 0) + ($getTodaysBoughtItems[0]['trans_spend'] ?? 0)),
            ],
            [
                'icon' => 'fa fa-arrow-circle-down',
                'bgColor' => '#b7c652',
                'textColor' => '#3f7cc7',
                'title' => 'ورود به خزانه',
                'value' => number_format($cashIncomeOutcome[0]['khazana_income'] ?? 0, 2),
            ],
            [
                'icon' => 'fa fa-arrow-circle-up',
                'bgColor' => '#b7c652',
                'textColor' => '#3f7cc7',
                'title' => 'خروج از خزانه',
                'value' => number_format($cashIncomeOutcome[0]['khazana_outcome'] ?? 0, 2),
            ],
            [
                'icon' => 'fas fa-arrow-down',
                'bgColor' => '#b7c652',
                'textColor' => '#3f7cc7',
                'title' => 'ورود به بانک',
                'value' => number_format($cashIncomeOutcome[0]['banks_income'] ?? 0, 2),
            ],
            [
                'icon' => 'fas fa-arrow-up',
                'bgColor' => '#b7c652',
                'textColor' => '#3f7cc7',
                'title' => 'خروج از بانک',
                'value' => number_format($cashIncomeOutcome[0]['banks_outcome'] ?? 0, 2),
            ],
            [
                'icon' => 'fas fa-arrow-down',
                'bgColor' => '#83d31a',
                'textColor' => '#fff',
                'title' => 'مجموع آمد نقد',
                'value' => number_format($cashIncomeOutcome[0]['total_incomes'] ?? 0, 2),
                'style' => 'border: 1px solid #83d31a;background: linear-gradient(45deg, #ffffff, #f0ffd1)',
            ],
            [
                'icon' => 'fas fa-arrow-up',
                'bgColor' => '#d3b33b',
                'textColor' => '#fff',
                'title' => 'مجموع رفت نقد',
                'value' => number_format($cashIncomeOutcome[0]['total_spend'] ?? 0, 2),
                'style' => 'border: 1px solid #d39d1a;background: linear-gradient(45deg, #ffffff, #f9edde)',
            ],
        ];

        foreach ($cards as $card) {
        ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card p-3" style="<?= $card['style'] ?? '' ?>">
                    <div class="d-flex align-items-center">
                        <span class="stamp stamp-md ml-3" style="background-color: <?= $card['bgColor'] ?>; color: <?= $card['textColor'] ?>; font-size: 18px;">
                            <i class="<?= $card['icon'] ?>"></i>
                        </span>
                        <div>
                            <small class="text-muted"><?= $card['title'] ?></small>
                            <h5 class="mb-1"><b><a href="#"><?= $card['value'] ?></a></b></h5>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
</div>
