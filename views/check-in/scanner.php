<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'QR Code Scanner';
$this->params['breadcrumbs'][] = ['label' => 'Check-In', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="qr-scanner">
    <div class="text-center mb-4">
        <h1><i class="bi bi-qr-code-scan"></i> <?= Html::encode($this->title) ?></h1>
        <p class="text-muted">Scan guest QR codes for check-in</p>
    </div>

    <!-- Scanner Interface -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Camera View -->
                    <div id="scanner-container" class="mb-3">
                        <div id="video-container" class="position-relative" style="display:none;">
                            <video id="qr-video" class="w-100" style="border-radius: 10px;"></video>
                            <div class="scanner-overlay">
                                <div class="scanner-frame"></div>
                            </div>
                        </div>
                        
                        <!-- Start Button -->
                        <div id="start-button-container" class="text-center">
                            <button id="start-scan-btn" class="btn btn-lg btn-primary">
                                <i class="bi bi-camera"></i> Start Camera
                            </button>
                            <p class="text-muted mt-2">Allow camera access to scan QR codes</p>
                        </div>
                        
                        <!-- Camera Selection -->
                        <div id="camera-selection" style="display:none;" class="mb-3">
                            <label class="form-label">Select Camera:</label>
                            <select id="camera-select" class="form-select">
                                <option value="">Loading cameras...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Manual Input -->
                    <div class="text-center">
                        <button class="btn btn-outline-secondary" onclick="toggleManualInput()">
                            <i class="bi bi-keyboard"></i> Manual QR Code Entry
                        </button>
                    </div>
                    
                    <div id="manual-input" style="display:none;" class="mt-3">
                        <div class="input-group">
                            <input type="text" id="manual-qr-input" class="form-control" placeholder="Enter QR code manually">
                            <button class="btn btn-primary" onclick="processManualQr()">
                                <i class="bi bi-search"></i> Lookup
                            </button>
                        </div>
                    </div>

                    <!-- Scanner Status -->
                    <div id="scanner-status" class="alert alert-info mt-3" style="display:none;">
                        <i class="bi bi-info-circle"></i> <span id="status-message">Ready to scan...</span>
                    </div>
                </div>
            </div>

            <!-- Recent Check-Ins -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Check-Ins</h5>
                </div>
                <div class="card-body">
                    <div id="recent-checkins">
                        <p class="text-muted">No recent check-ins</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <?= Html::a('<i class="bi bi-arrow-left"></i> Back to Check-In Dashboard', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </div>
        </div>
    </div>
</div>

<!-- Guest Info Modal -->
<div class="modal fade" id="guestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guest Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="guest-info">
                <!-- Guest info will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="check-in-btn" class="btn btn-success" onclick="confirmCheckIn()">
                    <i class="bi bi-check-circle"></i> Check In
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}

.scanner-frame {
    width: 250px;
    height: 250px;
    border: 3px solid #d4a574;
    border-radius: 10px;
    box-shadow: 0 0 0 99999px rgba(0, 0, 0, 0.5);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { border-color: #d4a574; }
    50% { border-color: #8b7355; }
}

#qr-video {
    max-height: 500px;
    object-fit: cover;
}

.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
}

.card-header {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}

.recent-checkin-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

<?php
// Include jsQR library from CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js', ['position' => \yii\web\View::POS_HEAD]);

$this->registerJs(<<<JS
let video = document.getElementById('qr-video');
let canvasElement = document.createElement('canvas');
let canvas = canvasElement.getContext('2d');
let scanning = false;
let currentGuestId = null;
let recentCheckins = [];

// Start scanning
document.getElementById('start-scan-btn').addEventListener('click', function() {
    startScanning();
});

function startScanning() {
    navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'environment' } // Prefer back camera on mobile
    }).then(function(stream) {
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        
        document.getElementById('start-button-container').style.display = 'none';
        document.getElementById('video-container').style.display = 'block';
        document.getElementById('camera-selection').style.display = 'block';
        document.getElementById('scanner-status').style.display = 'block';
        
        // List available cameras
        listCameras();
        
        scanning = true;
        requestAnimationFrame(tick);
        
        updateStatus('Camera active. Position QR code within the frame.', 'info');
    }).catch(function(err) {
        console.error('Camera error:', err);
        updateStatus('Failed to access camera: ' + err.message, 'danger');
    });
}

function listCameras() {
    navigator.mediaDevices.enumerateDevices().then(function(devices) {
        let videoDevices = devices.filter(device => device.kind === 'videoinput');
        let select = document.getElementById('camera-select');
        select.innerHTML = '';
        
        videoDevices.forEach((device, index) => {
            let option = document.createElement('option');
            option.value = device.deviceId;
            option.text = device.label || 'Camera ' + (index + 1);
            select.appendChild(option);
        });
        
        select.addEventListener('change', switchCamera);
    });
}

function switchCamera() {
    let deviceId = document.getElementById('camera-select').value;
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }
    
    navigator.mediaDevices.getUserMedia({
        video: { deviceId: deviceId ? { exact: deviceId } : undefined }
    }).then(function(stream) {
        video.srcObject = stream;
        video.play();
    });
}

function tick() {
    if (!scanning) return;
    
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        
        let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        let code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert',
        });
        
        if (code) {
            onQrCodeDetected(code.data);
            return; // Stop scanning temporarily
        }
    }
    
    requestAnimationFrame(tick);
}

function onQrCodeDetected(qrData) {
    console.log('QR Code detected:', qrData);
    scanning = false; // Pause scanning
    updateStatus('QR Code detected! Processing...', 'success');
    
    // Beep sound (optional)
    playBeep();
    
    // Process QR code
    processQrCode(qrData);
}

function processQrCode(qrData) {
    $.ajax({
        url: '" . Url::to(['scan']) . "',
        method: 'GET',
        data: { qr: qrData },
        success: function(response) {
            if (response.success) {
                showGuestInfo(response.guest);
            } else {
                updateStatus(response.message || 'Invalid QR code', 'danger');
                setTimeout(() => {
                    scanning = true;
                    requestAnimationFrame(tick);
                }, 2000);
            }
        },
        error: function() {
            updateStatus('Error processing QR code', 'danger');
            setTimeout(() => {
                scanning = true;
                requestAnimationFrame(tick);
            }, 2000);
        }
    });
}

function showGuestInfo(guest) {
    currentGuestId = guest.id;
    
    let html = '<div class="text-center">';
    html += '<h4>' + escapeHtml(guest.name) + '</h4>';
    html += '<p class="text-muted">' + (guest.email || 'No email') + '</p>';
    html += '<p class="text-muted">' + (guest.phone || 'No phone') + '</p>';
    
    if (guest.checked_in_at) {
        html += '<div class="alert alert-warning">';
        html += '<i class="bi bi-exclamation-triangle"></i> Already checked in at<br>';
        html += '<strong>' + new Date(guest.checked_in_at * 1000).toLocaleString() + '</strong>';
        html += '</div>';
        document.getElementById('check-in-btn').style.display = 'none';
    } else {
        html += '<div class="alert alert-info">';
        html += '<i class="bi bi-info-circle"></i> Ready to check in';
        html += '</div>';
        document.getElementById('check-in-btn').style.display = 'block';
    }
    
    html += '</div>';
    
    document.getElementById('guest-info').innerHTML = html;
    
    let modal = new bootstrap.Modal(document.getElementById('guestModal'));
    modal.show();
    
    // Resume scanning when modal is closed
    document.getElementById('guestModal').addEventListener('hidden.bs.modal', function () {
        scanning = true;
        requestAnimationFrame(tick);
    }, { once: true });
}

function confirmCheckIn() {
    if (!currentGuestId) return;
    
    $.ajax({
        url: '" . Url::to(['check-in']) . "',
        method: 'POST',
        data: {
            guest_id: currentGuestId,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('guestModal')).hide();
                updateStatus('✓ ' + response.message, 'success');
                addToRecentCheckins(response.guest);
                playSuccessSound();
                
                setTimeout(() => {
                    scanning = true;
                    updateStatus('Ready to scan next guest...', 'info');
                    requestAnimationFrame(tick);
                }, 2000);
            } else {
                updateStatus(response.message, 'danger');
            }
        },
        error: function() {
            updateStatus('Error checking in guest', 'danger');
        }
    });
}

function updateStatus(message, type) {
    let statusDiv = document.getElementById('scanner-status');
    statusDiv.className = 'alert alert-' + type + ' mt-3';
    document.getElementById('status-message').textContent = message;
    statusDiv.style.display = 'block';
}

function addToRecentCheckins(guest) {
    recentCheckins.unshift({
        name: guest.name,
        time: new Date().toLocaleTimeString()
    });
    
    if (recentCheckins.length > 5) recentCheckins.pop();
    
    let html = '';
    recentCheckins.forEach(item => {
        html += '<div class="recent-checkin-item">';
        html += '<strong>' + escapeHtml(item.name) + '</strong><br>';
        html += '<small class="text-muted">' + item.time + '</small>';
        html += '</div>';
    });
    
    document.getElementById('recent-checkins').innerHTML = html;
}

function toggleManualInput() {
    let manualInput = document.getElementById('manual-input');
    manualInput.style.display = manualInput.style.display === 'none' ? 'block' : 'none';
}

function processManualQr() {
    let qrCode = document.getElementById('manual-qr-input').value.trim();
    if (!qrCode) {
        alert('Please enter a QR code');
        return;
    }
    
    processQrCode(qrCode);
}

function escapeHtml(text) {
    let div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function playBeep() {
    // Simple beep using Web Audio API
    try {
        let audioContext = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = audioContext.createOscillator();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.connect(audioContext.destination);
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.1);
    } catch(e) {
        console.log('Audio not supported');
    }
}

function playSuccessSound() {
    try {
        let audioContext = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator = audioContext.createOscillator();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(1200, audioContext.currentTime);
        oscillator.connect(audioContext.destination);
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.15);
    } catch(e) {
        console.log('Audio not supported');
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }
});
JS
);
?>
