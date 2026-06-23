 <div class="dashboard-container">
        <div class="main-card">
            <div class="timeline-wrapper">
                <div class="timeline-track">

                    <!-- STEP 1: ORDERS -->
                    <div class="step-card active" id="orders">
                         @include('dashboard.cards.order')
                    </div>

                    <!-- STEP 2: BUY -->
                    <div class="step-card">
                        <div class="step-icon-ring">
                            <div class="step-icon-inner">
                                <i class="fas fa-shopping-bag"></i>
                                <span class="step-badge">856</span>
                            </div>
                        </div>
                        <div class="step-content-card">
                            <h5 class="step-title">Buy</h5>
                            <div class="stats-row">
                                <span class="stat-pill done">
                                    <i class="fas fa-check-circle"></i> Done: 8
                                </span>
                                <span class="stat-pill remained">
                                    <i class="fas fa-clock"></i> Remained: 15
                                </span>
                            </div>
                            <div class="progress-wrapper">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: 35%"></div>
                                </div>
                                <div class="progress-label">
                                    <span>Progress</span>
                                    <span>35%</span>
                                </div>
                            </div>
                            <div class="financial-section">
                                <div class="total-row">
                                    <div class="total-label">Total Amount</div>
                                    <div class="total-value"><span>$</span>123,123</div>
                                </div>
                                <div class="payment-grid">
                                    <div class="payment-cell paid">
                                        <div class="payment-icon"><i class="fas fa-check-circle"></i></div>
                                        <div class="payment-label">Paid</div>
                                        <div class="payment-value">$98,700</div>
                                    </div>
                                    <div class="payment-cell remaining">
                                        <div class="payment-icon"><i class="fas fa-clock"></i></div>
                                        <div class="payment-label">Remaining</div>
                                        <div class="payment-value">$24,423</div>
                                    </div>
                                </div>
                                <div class="balance-bar">
                                    <i class="fas fa-wallet"></i>
                                    <span class="balance-label">Balance:</span>
                                    <span class="balance-value">$74,277</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: DELIVERED -->
                    <div class="step-card">
                        <div class="step-icon-ring">
                            <div class="step-icon-inner">
                                <i class="fas fa-truck"></i>
                                <span class="step-badge">723</span>
                            </div>
                        </div>
                        <div class="step-content-card">
                            <h5 class="step-title">Delivered</h5>
                            <div class="stats-row">
                                <span class="stat-pill done">
                                    <i class="fas fa-check-circle"></i> Done: 6
                                </span>
                                <span class="stat-pill remained">
                                    <i class="fas fa-clock"></i> Remained: 9
                                </span>
                            </div>
                            <div class="progress-wrapper">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: 40%"></div>
                                </div>
                                <div class="progress-label">
                                    <span>Progress</span>
                                    <span>40%</span>
                                </div>
                            </div>
                            <div class="financial-section">
                                <div class="total-row">
                                    <div class="total-label">Total Amount</div>
                                    <div class="total-value"><span>$</span>89,456</div>
                                </div>
                                <div class="payment-grid">
                                    <div class="payment-cell paid">
                                        <div class="payment-icon"><i class="fas fa-check-circle"></i></div>
                                        <div class="payment-label">Paid</div>
                                        <div class="payment-value">$78,900</div>
                                    </div>
                                    <div class="payment-cell remaining">
                                        <div class="payment-icon"><i class="fas fa-clock"></i></div>
                                        <div class="payment-label">Remaining</div>
                                        <div class="payment-value">$10,556</div>
                                    </div>
                                </div>
                                <div class="balance-bar">
                                    <i class="fas fa-wallet"></i>
                                    <span class="balance-label">Balance:</span>
                                    <span class="balance-value">$68,344</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: RETURNED -->
                    <div class="step-card">
                        <div class="step-icon-ring">
                            <div class="step-icon-inner">
                                <i class="fas fa-undo-alt"></i>
                                <span class="step-badge">45</span>
                            </div>
                        </div>
                        <div class="step-content-card">
                            <h5 class="step-title">Returned</h5>
                            <div class="stats-row">
                                <span class="stat-pill done">
                                    <i class="fas fa-check-circle"></i> Done: 3
                                </span>
                                <span class="stat-pill remained">
                                    <i class="fas fa-clock"></i> Remained: 2
                                </span>
                            </div>
                            <div class="progress-wrapper">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: 60%"></div>
                                </div>
                                <div class="progress-label">
                                    <span>Progress</span>
                                    <span>60%</span>
                                </div>
                            </div>
                            <div class="financial-section">
                                <div class="total-row">
                                    <div class="total-label">Total Amount</div>
                                    <div class="total-value"><span>$</span>12,345</div>
                                </div>
                                <div class="payment-grid">
                                    <div class="payment-cell paid">
                                        <div class="payment-icon"><i class="fas fa-check-circle"></i></div>
                                        <div class="payment-label">Paid</div>
                                        <div class="payment-value">$9,876</div>
                                    </div>
                                    <div class="payment-cell remaining">
                                        <div class="payment-icon"><i class="fas fa-clock"></i></div>
                                        <div class="payment-label">Remaining</div>
                                        <div class="payment-value">$2,469</div>
                                    </div>
                                </div>
                                <div class="balance-bar">
                                    <i class="fas fa-wallet"></i>
                                    <span class="balance-label">Balance:</span>
                                    <span class="balance-value">$7,407</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
