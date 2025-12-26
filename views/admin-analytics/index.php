<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $stats array */
/* @var $rsvpTrend array */
/* @var $invitationStats array */
/* @var $recentRsvps app\models\Rsvp[] */

$this->title = 'Analytics Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="admin-analytics-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="bi bi-file-earmark-excel"></i> Export Excel', ['export-excel'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> Export PDF', ['export-pdf'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary analytics-card">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">
                        <i class="bi bi-envelope-heart"></i>
                    </div>
                    <h3 class="mb-1"><?= $stats['total_invitations'] ?></h3>
                    <p class="text-muted mb-0">Total Invitations</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-info analytics-card">
                <div class="card-body text-center">
                    <div class="display-4 text-info mb-2">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="mb-1"><?= $stats['total_guests'] ?></h3>
                    <p class="text-muted mb-0">Total Guests</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-success analytics-card">
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-2">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h3 class="mb-1"><?= $stats['total_rsvps'] ?></h3>
                    <p class="text-muted mb-0">Total Responses</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-warning analytics-card">
                <div class="card-body text-center">
                    <div class="display-4 text-warning mb-2">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3 class="mb-1"><?= $stats['response_rate'] ?>%</h3>
                    <p class="text-muted mb-0">Response Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- RSVP Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> RSVP Trend (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="rsvpTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Attendance Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Attendance Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-success"></i> Attending</span>
                            <strong><?= $stats['attending'] ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-danger"></i> Not Attending</span>
                            <strong><?= $stats['not_attending'] ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-circle-fill text-warning"></i> Maybe</span>
                            <strong><?= $stats['maybe'] ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invitation Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Invitation Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invitation</th>
                                    <th class="text-center">Total Guests</th>
                                    <th class="text-center">Responses</th>
                                    <th class="text-center">Attending</th>
                                    <th class="text-center">Not Attending</th>
                                    <th class="text-center">Maybe</th>
                                    <th class="text-center">Response Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invitationStats as $stat): ?>
                                <tr>
                                    <td><strong><?= Html::encode($stat['name']) ?></strong></td>
                                    <td class="text-center"><?= $stat['total_guests'] ?></td>
                                    <td class="text-center"><?= $stat['rsvp_count'] ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?= $stat['attending'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger"><?= $stat['not_attending'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning"><?= $stat['maybe'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $rate = $stat['total_guests'] > 0 
                                            ? round(($stat['rsvp_count'] / $stat['total_guests']) * 100, 1) 
                                            : 0;
                                        $badgeClass = $rate >= 75 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= $rate ?>%</span>
                                    </td>
                                    <td>
                                        <?= Html::a('<i class="bi bi-bar-chart-line"></i> Details', ['invitation', 'id' => $stat['id'] ?? '#'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent RSVPs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent RSVPs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Invitation</th>
                                    <th>Attendance</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentRsvps as $rsvp): ?>
                                <tr>
                                    <td><?= Html::encode($rsvp->guest->name ?? 'Unknown') ?></td>
                                    <td><?= Html::encode($rsvp->invitation->title ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = $rsvp->attendance === 'yes' ? 'success' : ($rsvp->attendance === 'no' ? 'danger' : 'warning');
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($rsvp->attendance) ?></span>
                                    </td>
                                    <td><?= Html::encode($rsvp->message ?: '-') ?></td>
                                    <td><?= date('M d, Y H:i', $rsvp->created_at) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare data for charts
$trendDates = array_column($rsvpTrend, 'date');
$trendCounts = array_column($rsvpTrend, 'count');

$js = <<<JS
// RSVP Trend Chart
const trendCtx = document.getElementById('rsvpTrendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($trendDates) ?>,
        datasets: [{
            label: 'RSVPs',
            data: <?= json_encode($trendCounts) ?>,
            borderColor: 'rgb(13, 110, 253)',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Attendance Distribution Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Attending', 'Not Attending', 'Maybe'],
        datasets: [{
            data: [<?= $stats['attending'] ?>, <?= $stats['not_attending'] ?>, <?= $stats['maybe'] ?>],
            backgroundColor: [
                'rgba(25, 135, 84, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)'
            ],
            borderColor: [
                'rgb(25, 135, 84)',
                'rgb(220, 53, 69)',
                'rgb(255, 193, 7)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

$css = <<<CSS
.analytics-card {
    transition: all 0.3s ease;
}

.analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    font-weight: 600;
}
CSS;

$this->registerCss($css);
?>
