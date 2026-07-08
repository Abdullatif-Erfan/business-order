<!-- dashboard/cards/buy.blade.php -->
    <a href="/boughtList" class="step-link">
        <div class="step-icon-ring">
            <div class="step-icon-inner">
                <i class="fas fa-shopping-bag"></i>
                <span class="step-badge" id="totalBoughtBadge">{{ number_format($bought['total_bought'] ?? 0) }}</span>
            </div>
        </div>
    </a>
    <div class="step-content-card">
        <h5 class="step-title">{{ __('dashboard.buy') }}</h5>
        <div class="stats-row">
            <span class="stat-pill done">
                <i class="fas fa-check-circle"></i> {{ __('dashboard.fully_paid') }}: 
                <span id="totalFullyPaid">{{ number_format($bought['fully_paid'] ?? 0) }}</span>
            </span>
            <span class="stat-pill remained">
                <i class="fas fa-clock"></i> {{ __('dashboard.partial_paid') }}: 
                <span id="totalPartialPaid">{{ number_format($bought['partial_paid'] ?? 0) }}</span>
            </span>
        </div>
        <div class="progress-wrapper">
            <div class="progress-track">
                <div class="progress-fill" id="buyProgressFill" style="width: {{ $bought['progress_percentage'] ?? 0 }}%"></div>
            </div>
            <div class="progress-label">
                <span>{{ __('dashboard.payment_progress') }}</span>
                <span id="buyProgressPercentage">{{ $bought['progress_percentage'] ?? 0 }}%</span>
            </div>
        </div>

        <div class="financial-section">
            <div class="total-row">
                <div class="total-label">{{ __('dashboard.total_amount') }}</div>
                <div class="total-value">
                    <span>$</span>
                    <span id="totalBoughtAmount">{{ number_format($bought['total_amount'] ?? 0, 2) }}</span>
                </div>
            </div>
            <div class="payment-grid">
                <div class="payment-cell paid">
                    <div class="payment-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="payment-label">{{ __('dashboard.paid') }}</div>
                    <div class="payment-value" id="totalBoughtPaid">
                        ${{ number_format($bought['total_paid'] ?? 0, 2) }}
                    </div>
                </div>
                <div class="payment-cell remaining">
                    <div class="payment-icon"><i class="fas fa-clock"></i></div>
                    <div class="payment-label">{{ __('dashboard.remained') }}</div>
                    <div class="payment-value" id="totalBoughtRemained">
                        ${{ number_format($bought['total_remained'] ?? 0, 2) }}
                    </div>
                </div>
            </div>
            <div class="balance-bar">
                <i class="fas fa-wallet"></i>
                <span class="balance-label">{{ __('dashboard.balance') }}:</span>
                <span class="balance-value" id="boughtBalance">
                    ${{ number_format(($bought['total_paid'] ?? 0) - ($bought['total_remained'] ?? 0), 2) }}
                </span>
            </div>
        </div>
    </div>