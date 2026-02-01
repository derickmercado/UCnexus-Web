<?php
// pages/dashboard.php
?>
<section id="dashboard" class="tab-content active">
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3>Total Rooms</h3>
            <p class="stat-number">24</p>
            <small>Active rooms</small>
        </div>
        <div class="stat-card">
            <h3>Scheduled Classes</h3>
            <p class="stat-number">18</p>
            <small>This week</small>
        </div>
        <div class="stat-card">
            <h3>Pending Tasks</h3>
            <p class="stat-number">5</p>
            <small>Awaiting action</small>
        </div>
        <div class="stat-card">
            <h3>System Status</h3>
            <p class="stat-number">✓</p>
            <small>All systems normal</small>
        </div>
    </div>

    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="?page=schedule" class="btn-action">View Schedule</a>
            <a href="?page=ai-helper" class="btn-action">Ask AI Helper</a>
        </div>
    </div>
</section>
