<a href="/orders" class="step-link">
    <div class="step-icon-ring">
        <div class="step-icon-inner">
            <i class="fas fa-shopping-cart"></i>
            <span class="step-badge" id="totalOrdersBadge">{{ number_format($orders['total_orders'] ?? 0) }}</span>
        </div>
    </div>
</a>
<div class="step-content-card">
    <h5 class="step-title">{{__('order.orders')}}</h5>
    <div class="stats-row">
        <span class="stat-pill done" id="doneStats">
            <i class="fas fa-check-circle"></i> {{ __('order.done') }}: <span id="totalCompleted">{{ number_format($orders['total_completed'] ?? 0) }}</span>
        </span>
        <span class="stat-pill remained" id="remainedStats">
            <i class="fas fa-clock"></i> {{ __('order.remained') }}: <span id="totalRemained">
                {{ number_format($orders['total_new'] ?? 0)}}</span>
        </span>
    </div>
    <div class="progress-wrapper">
        <div class="progress-track">
            <div class="progress-fill" id="progressFill" style="width: {{ $orders['progress_percentage'] ?? 0 }}%"></div>
        </div>
        <div class="progress-label">
            <span>جریان پیشرفت </span>
            <span id="progressPercentage">{{ $orders['progress_percentage'] ?? 0 }}%</span>
        </div>
    </div>

    <div class="financial-section">
        <div class="col-12 orderDetailsCard">
            <div class="payment-cell paid">
                <div class="payment-label">{{ __('order.draft') }}</div>
                <div class="payment-value" id="totalDraft">{{ number_format($orders['total_draft'] ?? 0) }}</div>
            </div>
            <div class="payment-cell remained">
                <div class="payment-label remained">{{ __('order.new') }}</div>
                <div class="payment-value" id="totalNew">{{ number_format($orders['total_new'] ?? 0) }}</div>
            </div>
            <div class="payment-cell ">
                <div class="payment-label">{{ __('order.cancelled') }}</div>
                <div class="payment-value" id="totalCancelled">{{ number_format($orders['total_cancelled'] ?? 0) }}</div>
            </div>
            <div class="payment-cell done">
                <div class="payment-label done">{{ __('order.completed') }}</div>
                <div class="payment-value" id="totalCompletedValue">{{ number_format($orders['total_completed'] ?? 0) }}</div>
            </div>
        </div>
    </div>
</div>