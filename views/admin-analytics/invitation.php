<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $stats array */
/* @var $rsvpTimeline array */
/* @var $pendingGuests app\models\Guest[] */

$this->title = 'Analytics: ' . $invitation->title;
$this->params['breadcrumbs'][] = ['label' => 'Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="admin-analytics-invitation">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back to Dashboard', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> Export PDF', ['export-pdf', 'id' => $invitation->id], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="card border-info analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-info mb-1"><i class="bi bi-people"></i></div>
                    <h4 class="mb-1"><?= $stats['total_guests'] ?></h4>
                    <small class="text-muted">Total Guests</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card border-success analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-success mb-1"><i class="bi bi-check-circle"></i></div>
                    <h4 class="mb-1"><?= $stats['total_rsvps'] ?></h4>
                    <small class="text-muted">Responses</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card border-success analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-success mb-1"><i class="bi bi-hand-thumbs-up"></i></div>
                    <h4 class="mb-1"><?= $stats['attending'] ?></h4>
                    <small class="text-muted">Attending</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card border-danger analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-danger mb-1"><i class="bi bi-hand-thumbs-down"></i></div>
                    <h4 class="mb-1"><?= $stats['not_attending'] ?></h4>
                    <small class="text-muted">Not Attending</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card border-warning analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-warning mb-1"><i class="bi bi-question-circle"></i></div>
                    <h4 class="mb-1"><?= $stats['maybe'] ?></h4>
                    <small class="text-muted">Maybe</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <div class="card border-secondary analytics-card">
                <div class="card-body text-center p-3">
                    <div class="h2 text-secondary mb-1"><i class="bi bi-hourglass"></i></div>
                    <h4 class="mb-1"><?= $stats['pending'] ?></h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Rate Progress -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Response Rate: <?= $stats['response_rate'] ?>%</h5>
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $stats['response_rate'] ?>%" 
                             aria-valuenow="<?= $stats['response_rate'] ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= $stats['response_rate'] ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> RSVP Timeline</h5>
                </div>
                <div class="card-body">
                    <canvas id="rsvpTimelineChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Guests -->
    <?php if (!empty($pendingGuests)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pending Responses (<?= count($pendingGuests) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Guest Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingGuests as $index => $guest): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= Html::encode($guest->name) ?></strong></td>
                                    <td><?= Html::encode($guest->email ?: '-') ?></td>
                                    <td><?= Html::encode($guest->phone ?: '-') ?></td>
                                    <td>
                                        <?= Html::a('<i class="bi bi-send"></i> Send Reminder', '#', ['class' => 'btn btn-sm btn-outline-primary', 'onclick' => 'alert("Reminder feature coming soon!"); return false;']) ?>
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
    <?php endif; ?>
</div>

<?php
// Prepare timeline data
$timelineData = [];
foreach ($rsvpTimeline as $item) {
    if (!isset($timelineData[$item['date']])) {
        $timelineData[$item['date']] = ['yes' => 0, 'no' => 0, 'maybe' => 0];
    }
    $timelineData[$item['date']][$item['attendance']] = (int)$item['count'];
}

$dates = array_keys($timelineData);
$yesData = [];
$noData = [];
$maybeData = [];

foreach ($dates as $date) {
    $yesData[] = $timelineData[$date]['yes'];
    $noData[] = $timelineData[$date]['no'];
    $maybeData[] = $timelineData[$date]['maybe'];
}

$js = <<<JS
// RSVP Timeline Chart
const timelineCtx = document.getElementById('rsvpTimelineChart').getContext('2d');
new Chart(timelineCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [
            {
                label: 'Attending',
                data: <?= json_encode($yesData) ?>,
                borderColor: 'rgb(25, 135, 84)',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Not Attending',
                data: <?= json_encode($noData) ?>,
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Maybe',
                data: <?= json_encode($maybeData) ?>,
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
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
CSS;

$this->registerCss($css);
?>
