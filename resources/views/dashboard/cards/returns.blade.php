<!-- dashboard/cards/returns.blade.php -->
<div class="step-card" id="returns">
    <a href="/return/list" class="step-link">
        <div class="step-icon-ring">
            <div class="step-icon-inner">
                <i class="fas fa-undo-alt"></i>
                <span class="step-badge" id="totalReturnsBadge">{{ number_format($returns['total_returns'] ?? 0) }}</span>
            </div>
        </div>
    </a>
    <div class="step-content-card">
        <h5 class="step-title">{{ __('buy.return') }}</h5>
        
        <!-- Stats Row -->
        <div class="stats-row">
            <span class="stat-pill done">
                <i class="fas fa-boxes"></i> {{ __('common.amount') }}: 
                <span id="totalReturnQuantity">{{ $returns['total_quantity'] ?? 0 }}</span>
            </span>
            <span class="stat-pill remained">
                <i class="fas fa-file-invoice"></i> {{ __('common.bill') }}: 
                <span id="totalReturnBills">{{ number_format($returns['total_bills'] ?? 0) }}</span>
            </span>
        </div>

        <!-- Financial Summary -->
        <div class="financial-section">
            <div class="total-row">
                <div class="total-label">{{ __('common.total') }}</div>
                <div class="total-value" style="color: #e17055;">
                    <!-- <span>$</span> -->
                    <span id="totalReturnAmount">{{ number_format($returns['total_amount'] ?? 0, 2) }}</span>
                </div>
            </div>
            
                <div class="financial-section">
                    <div class="col-12 orderDetailsCard">
                        <div class="payment-cell remained">
                            <div class="payment-icon"><i class="fas fa-users"></i></div>
                            <div class="payment-label">{{ __('dashboard.supplier') }}</div>
                            <div class="payment-value" id="totalReturnSuppliers">{{ number_format($returns['total_suppliers'] ?? 0) }}</div>
                        </div>
                        <div class="payment-cell done">
                            <div class="payment-icon"><i class="fas fa-users"></i></div>
                            <div class="payment-label">{{ __('dashboard.customer') }}</div>
                            <div class="payment-value" id="totalReturnToday">{{ number_format($returns['total_customers'] ?? 0) }}</div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>