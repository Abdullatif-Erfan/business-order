 <div class="dashboard-container">
        <div class="main-card">

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="dots-loader" id="filter-loader" style="display:none;">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

            <div class="timeline-wrapper">
                <div class="timeline-track">

                    <!-- STEP 1: ORDERS -->
                    <div class="step-card" id="orders">
                         @include('dashboard.cards.order')
                    </div>

                    <!-- STEP 2: BUY -->
                    <div class="step-card" id="buy">
                        @include('dashboard.cards.buy')
                    </div>

                    <!-- STEP 3: DELIVERED / SALES -->
                    <div class="step-card" id="sales">
                        @include('dashboard.cards.sales')
                    </div>

                    <!-- STEP 4: RETURNED -->
                    <div class="step-card" id="returns">
                        @include('dashboard.cards.returns')
                    </div>

                </div>
            </div>
        </div>
    </div>
