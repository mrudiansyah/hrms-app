

<?php $__env->startSection('Contents'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Register Participant</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Scan QR Code Peserta</h3>
                    </div>
                    <div class="box-body">
                        <p>Arahkan QR Code peserta ke kamera. Scan pertama mencatat check-in, scan berikutnya mencatat check-out.</p>
                        <div class="form-group">
                            <video id="qr-camera" class="img-responsive center-block" autoplay muted playsinline
                                style="max-height:320px; width:100%; background:#222; display:none;"></video>
                        </div>
                        <div class="form-group">
                            <button id="start-camera" type="button" class="btn btn-primary">
                                <i class="fa fa-camera"></i> Mulai Kamera
                            </button>
                            <button id="stop-camera" type="button" class="btn btn-default" style="display:none;">
                                <i class="fa fa-stop"></i> Hentikan Kamera
                            </button>
                        </div>
                        <div id="scan-message" class="alert" role="status" aria-live="polite" style="display:none;"></div>

                        <hr>
                        <form id="manual-scan-form">
                            <div class="form-group">
                                <label for="entrant-code">Input entrant_code</label>
                                <div class="input-group">
                                    <input id="entrant-code" type="text" class="form-control" maxlength="32"
                                        autocomplete="off" placeholder="Scan dengan kamera atau barcode scanner" required>
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-success">Proses</button>
                                    </span>
                                </div>
                                <small class="text-muted">Input ini juga dapat digunakan dengan barcode scanner USB atau bila kamera tidak tersedia.</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Riwayat Scan Peserta</h3>
                        <div class="box-tools pull-right">
                            &nbsp;
                        </div>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped" id="scan-history">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Event</th>
                                    <th>Kode Peserta</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="scan-history-body">
                                <?php $__empty_1 = true; $__currentLoopData = $scannedAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr id="attendance-<?php echo e($attendance->id); ?>">
                                        <td><?php echo e($attendance->entrant_name); ?></td>
                                        <td><?php echo e($attendance->event_name); ?></td>
                                        <td><?php echo e($attendance->entrant_code); ?></td>
                                        <td><?php echo e(date('H:i', strtotime($attendance->checkin))); ?></td>
                                        <td><?php echo e(date('H:i', strtotime($attendance->checkout)) ?: '-'); ?></td>
                                        <td>
                                            <?php if((int) $attendance->status === 2): ?>
                                                <span class="label label-default">Check-out</span>
                                            <?php else: ?>
                                                <span class="label label-success">Check-in</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="no-scan-row">
                                        <td colspan="6" class="text-center">Belum ada peserta yang scan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    (function () {
        var video = document.getElementById('qr-camera');
        var startButton = document.getElementById('start-camera');
        var stopButton = document.getElementById('stop-camera');
        var messageBox = document.getElementById('scan-message');
        var manualForm = document.getElementById('manual-scan-form');
        var codeInput = document.getElementById('entrant-code');
        var cameraStream = null;
        var detector = null;
        var scanning = false;
        var processing = false;
        var lastDetectedCode = '';
        var lastSeenAt = 0;
        var csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;

        function showMessage(text, success) {
            messageBox.textContent = text;
            messageBox.className = success ? 'alert alert-success' : 'alert alert-danger';
            messageBox.style.display = 'block';
        }

        function addCell(row, value) {
            var cell = document.createElement('td');
            cell.textContent = value || '-';
            row.appendChild(cell);
            return cell;
        }

        function updateHistory(attendance) {
            var body = document.getElementById('scan-history-body');
            var emptyRow = document.getElementById('no-scan-row');
            if (emptyRow) {
                emptyRow.remove();
            }

            var row = document.getElementById('attendance-' + attendance.id);
            if (!row) {
                row = document.createElement('tr');
                row.id = 'attendance-' + attendance.id;
                addCell(row, attendance.entrant_name);
                addCell(row, attendance.event_name);
                addCell(row, attendance.entrant_code);
                addCell(row, attendance.checkin);
                addCell(row, attendance.checkout);
                row.appendChild(document.createElement('td'));
                body.insertBefore(row, body.firstChild);
            } else {
                row.cells[0].textContent = attendance.entrant_name;
                row.cells[1].textContent = attendance.event_name;
                row.cells[2].textContent = attendance.entrant_code;
                row.cells[3].textContent = attendance.checkin;
                row.cells[4].textContent = attendance.checkout || '-';
            }

            var statusCell = row.cells[5];
            statusCell.textContent = '';
            var statusLabel = document.createElement('span');
            statusLabel.className = attendance.status === 2 ? 'label label-default' : 'label label-success';
            statusLabel.textContent = attendance.status === 2 ? 'Check-out' : 'Check-in';
            statusCell.appendChild(statusLabel);

            while (body.rows.length > 100) {
                body.deleteRow(body.rows.length - 1);
            }
        }

        async function registerScan(code) {
            if (processing || !code) {
                return;
            }

            processing = true;
            try {
                var response = await fetch('/Event/ScanQR', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ entrant_code: code.trim() })
                });
                var result = await response.json();
                if (!response.ok) {
                    throw new Error(result.message || 'Scan gagal diproses.');
                }

                updateHistory(result.attendance);
                showMessage(result.message + ' ' + result.attendance.entrant_name + ' — ' + result.attendance.event_name, true);
                codeInput.value = '';
            } catch (error) {
                showMessage(error.message || 'Terjadi kesalahan saat memproses scan.', false);
            } finally {
                processing = false;
            }
        }

        async function scanFrame() {
            if (!scanning) {
                return;
            }

            try {
                var codes = await detector.detect(video);
                if (codes.length) {
                    var code = codes[0].rawValue.trim();
                    lastSeenAt = Date.now();
                    if (code && code !== lastDetectedCode) {
                        lastDetectedCode = code;
                        await registerScan(code);
                    }
                } else if (lastDetectedCode && Date.now() - lastSeenAt > 1200) {
                    lastDetectedCode = '';
                }
            } catch (error) {
                showMessage('Gagal membaca QR Code dari kamera.', false);
                stopCamera();
                return;
            }

            if (scanning) {
                window.setTimeout(scanFrame, 250);
            }
        }

        async function startCamera() {
            if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showMessage('Kamera memerlukan browser yang mendukung kamera dan koneksi HTTPS (atau localhost). Gunakan input entrant_code sebagai alternatif.', false);
                return;
            }
            if (!('BarcodeDetector' in window)) {
                showMessage('Browser ini belum mendukung pemindaian QR kamera. Gunakan input entrant_code atau browser Chromium terbaru.', false);
                return;
            }

            try {
                detector = new BarcodeDetector({ formats: ['qr_code'] });
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: { facingMode: { ideal: 'environment' } }
                });
                video.srcObject = cameraStream;
                video.style.display = 'block';
                await video.play();
                scanning = true;
                startButton.style.display = 'none';
                stopButton.style.display = 'inline-block';
                showMessage('Kamera aktif. Arahkan QR Code peserta ke kamera.', true);
                scanFrame();
            } catch (error) {
                stopCamera();
                showMessage('Kamera tidak dapat dibuka. Izinkan akses kamera pada browser atau gunakan input entrant_code.', false);
            }
        }

        function stopCamera() {
            scanning = false;
            if (cameraStream) {
                cameraStream.getTracks().forEach(function (track) {
                    track.stop();
                });
                cameraStream = null;
            }
            video.srcObject = null;
            video.style.display = 'none';
            startButton.style.display = 'inline-block';
            stopButton.style.display = 'none';
        }

        startButton.addEventListener('click', startCamera);
        stopButton.addEventListener('click', stopCamera);
        manualForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var code = codeInput.value.trim();
            if (code) {
                registerScan(code);
            }
        });
        window.addEventListener('beforeunload', stopCamera);
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/event/scan_qr.blade.php ENDPATH**/ ?>