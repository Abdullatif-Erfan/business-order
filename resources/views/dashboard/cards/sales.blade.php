    <a href="/sales" class="step-link">
        <div class="step-icon-ring">
            <div class="step-icon-inner">
                <i class="fas fa-chart-line"></i>
                <span class="step-badge" id="totalSalesBadge">{{ number_format($sales['total_sales'] ?? 0) }}</span>
            </div>
        </div>
    </a>
    <div class="step-content-card">
        <h5 class="step-title">{{ __('dashboard.sales') }}</h5>
        <div class="stats-row">
            <span class="stat-pill done">
                <i class="fas fa-check-circle"></i> {{ __('dashboard.fully_paid') }}: 
                <span id="salesFullyPaid">{{ number_format($sales['fully_paid'] ?? 0) }}</span>
            </span>
            <span class="stat-pill remained">
                <i class="fas fa-clock"></i> {{ __('dashboard.partial_paid') }}: 
                <span id="salesPartialPaid">{{ number_format($sales['partial_paid'] ?? 0) }}</span>
            </span>
        </div>
        
        <!-- Payment Progress -->
        <div class="progress-wrapper">
            <div class="progress-track">
                <div class="progress-fill" id="salesProgressFill" style="width: {{ $sales['progress_percentage'] ?? 0 }}%"></div>
            </div>
            <div class="progress-label">
                <span>{{ __('dashboard.payment_progress') }}</span>
                <span id="salesProgressPercentage">{{ $sales['progress_percentage'] ?? 0 }}%</span>
            </div>
        </div>

        <!-- Clearance Progress -->
        <div class="progress-wrapper" style="margin-top: 8px;">
            <div class="progress-track">
                <div class="progress-fill" id="clearanceProgressFill" style="width: {{ $sales['clearance_percentage'] ?? 0 }}%; background: linear-gradient(90deg, #6c5ce7, #a29bfe);"></div>
            </div>
            <div class="progress-label">
                <span>{{ __('dashboard.clearance_progress') }}</span>
                <span id="clearancePercentage">{{ $sales['clearance_percentage'] ?? 0 }}%</span>
            </div>
        </div>

        <div class="financial-section">
            <div class="total-row">
                <div class="total-label">{{ __('dashboard.total_amount') }}</div>
                <div class="total-value">
                    <span>$</span>
                    <span id="totalSalesAmount">{{ number_format($sales['total_amount'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="payment-grid">
                <div class="payment-cell paid">
                    <div class="payment-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="payment-label">{{ __('dashboard.paid') }}</div>
                    <div class="payment-value" id="totalSalesPaid">
                        ${{ number_format($sales['total_paid'] ?? 0, 2) }}
                    </div>
                </div>
                <div class="payment-cell remaining">
                    <div class="payment-icon"><i class="fas fa-clock"></i></div>
                    <div class="payment-label">{{ __('dashboard.remained') }}</div>
                    <div class="payment-value" id="totalSalesRemained">
                        ${{ number_format($sales['total_remained'] ?? 0, 2) }}
                    </div>
                </div>
            </div>
            
            <!-- Clearance Status -->
            <div class="clearance-status" style="display: flex; justify-content: space-between; padding: 8px 0; border-top: 1px solid #eee; margin-top: 8px;">
                <span>
                    <i class="fas fa-check-circle" style="color: #00b894;"></i>
                    {{ __('dashboard.cleared') }}: 
                    <strong id="salesCleared">{{ number_format($sales['cleared'] ?? 0) }}</strong>
                </span>
                <span>
                    <i class="fas fa-times-circle" style="color: #e17055;"></i>
                    {{ __('dashboard.not_cleared') }}: 
                    <strong id="salesNotCleared">{{ number_format($sales['not_cleared'] ?? 0) }}</strong>
                </span>
            </div>

            <div class="balance-bar">
                <i class="fas fa-wallet"></i>
                <span class="balance-label">{{ __('dashboard.balance') }}:</span>
                <span class="balance-value" id="salesBalance">
                    ${{ number_format(($sales['total_paid'] ?? 0) - ($sales['total_remained'] ?? 0), 2) }}
                </span>
            </div>
        </div>
    </div>