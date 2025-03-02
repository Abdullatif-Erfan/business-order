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
                'value' => number_format($data['todays_sold_income']['total_price'] ?? 0),
            ],
            [
                'icon' => 'far fa-money-bill-alt',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'دریافت فروشات',
                'value' => number_format($data['todays_sold_income']['cur_pay'] ?? 0),
            ],
            [
                'icon' => 'fas fa-donate',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'طلب فروشات',
                'value' => number_format($data['todays_sold_income']['remained'] ?? 0),
            ],
            [
                'icon' => 'fas fa-money-check-alt',
                'bgColor' => '#dbf3ff',
                'textColor' => '#3f7cc7',
                'title' => 'مفاد فروشات',
                'value' => number_format($data['todays_sold_income']['profit'] ?? 0),
            ],

            // ------------------- Belongs to BUY ---------------------------------
            [
                'icon' => 'fas fa-dolly-flatbed',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'خریداری',
                'value' => number_format($data['getTodaysBoughtItems']['total_price'] ?? 0),
            ],
            [
                'icon' => 'fas fa-hand-holding-usd',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'پرداخت خرید',
                'value' => number_format($data['getTodaysBoughtItems']['cur_pay'] ?? 0),
            ],
            [
                'icon' => 'fas fa-download',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'قرضه خرید',
                'value' => number_format($data['getTodaysBoughtItems']['remained'] ?? 0),
            ],
            [
                'icon' => 'fas fa-download',
                'bgColor' => '#ebd997',
                'textColor' => '#3f7cc7',
                'title' => 'مصارف ترانسپورت',
                'value' => number_format($data['getTodaysBoughtItems']['trans_spend'] ?? 0),
            ],
           
            // ---------------- belongs to Income and Outcome --------------------
            [
                'icon' => 'fas fa-arrow-up',
                'bgColor' => '#6eafd9',
                'textColor' => '#fff',
                'title' => 'عواید ',
                'value' => number_format($data['cashIncomeOutcome']['total_income'] ?? 0),
                'style' => 'border: 1px solid #afc7f8;background: linear-gradient(45deg, #ffffff, #d6efff)',
            ],
            [
                'icon' => 'fas fa-arrow-down',
                'bgColor' => '#e6aaaa',
                'textColor' => '#fff',
                'title' => 'مصارف ',
                'value' => number_format($data['cashIncomeOutcome']['total_expense'] ?? 0),
                'style' => 'border: 1px solid #e7c99c;background: linear-gradient(45deg, #ffffff, #ffd9d9)',
            ],
            [
                'icon' => 'fas fa-arrow-down',
                'bgColor' => '#83d31a',
                'textColor' => '#fff',
                'title' => 'مجموع آمد نقد ',
                'value' => number_format($data['cashIncomeOutcome']['total_incomes'] ?? 0),
                'style' => 'border: 1px solid #83d31a;background: linear-gradient(45deg, #ffffff, #f0ffd1)',
            ],
            [
                'icon' => 'fas fa-arrow-up',
                'bgColor' => '#d3b33b',
                'textColor' => '#fff',
                'title' => 'مجموع رفت نقد ',
                'value' => number_format($data['cashIncomeOutcome']['total_outcomes'] ?? 0),
                'style' => 'border: 1px solid #d39d1a;background: linear-gradient(45deg, #ffffff, #f9edde)',
            ],
        ];
        foreach ($cards as $index => $card) {
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
