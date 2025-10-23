<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Scanner Presensi Kegiatan Mahasiswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #121212;
            padding: 5px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .scanner-container {
            position: relative;
            width: 100%;
            max-height: 650px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        video {
            width: 100%;
            border: 3px solid #28a745;
            border-radius: 10px;
            background-color: #000;
        }

        .scan-region-highlight {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 200px;
            border: 4px dashed rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            display: none;
        }

        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: rgba(255, 0, 0, 0.7);
            animation: scan 2s infinite linear;
            display: none;
        }

        @keyframes scan {
            0% {
                top: 0;
            }

            100% {
                top: 100%;
            }
        }

        .controls {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            flex-wrap: unset;
            gap: 10px;
        }

        .btn {
            min-width: 50px;
        }

        .camera-select {
            margin: 0px 0;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .scanner-status {
            margin-top: 0px;
            font-weight: bold;
            color: #28a745;
        }

        .scanner-status.error {
            color: #dc3545;
        }

        .scanner-guide {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px auto;
            max-width: 500px;
            text-align: left;
            border-left: 4px solid #28a745;
        }

        .permission-prompt {
            background-color: #fff3cd;
            border-radius: 8px;
            padding: 15px;
            margin: 15px auto;
            max-width: 500px;
            text-align: center;
            border-left: 4px solid #ffc107;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.7.6/dist/quagga.min.js"></script>
    <script src="https://unpkg.com/@zxing/browser@latest"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container" style="!important--bs-gutter-x: 10.5rem;">
        <h4><b>Scanner Presensi Kegiatan </b><br>Nama Kegiatan </h4>
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
            <p><i class="bi bi-info-circle-fill"></i> <strong>Klik Petunjuk Penggunaan:</strong></p>
        </button>
        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
            <div class="accordion-body scanner-guide">
                <ol>
                    <li>Pilih kamera yang akan digunakan</li>
                    <li>Tekan tombol <strong>Start Scan</strong></li>
                    <li>Arahkan kamera ke QR Code/Barcode peserta</li>
                    <li>Tunggu hingga terdeteksi secara otomatis</li>
                </ol>
            </div>
        </div>

        <!-- Permission Prompt (hidden by default) -->
        <div id="permissionPrompt" class="permission-prompt" style="display: none;">
            <h5><i class="bi bi-camera-video-off-fill"></i> Izin Kamera Diperlukan</h5>
            <p>Untuk menggunakan scanner, Anda perlu memberikan izin akses kamera.</p>
            <button id="requestPermissionBtn" class="btn btn-primary">
                <i class="bi bi-camera-fill"></i> Berikan Izin Kamera
            </button>
        </div>

        <div class="camera-select">
            <select id="cameraSelect" class="form-select">
                <option value="">Pilih Kamera</option>
            </select>
        </div>

        <div class="scanner-container">
            <video id="video" autoplay muted playsinline></video>
            <div class="scan-region-highlight" id="scanRegionHighlight"></div>
            <div class="scan-line" id="scanLine"></div>
        </div>

        <div id="scannerStatus" class="scanner-status">Scanner siap</div>

        <div class="controls">
            <button id="startButton" class="btn btn-success">
                <i class="bi bi-play-fill"></i> Start Scan
            </button>
            <button id="stopButton" class="btn btn-danger" disabled>
                <i class="bi bi-stop-fill"></i> Stop Scan
            </button>
            <button id="mirrorButton" class="btn btn-secondary">
                <i class="bi bi-arrow-left-right"></i> Mirror: OFF
            </button>
            <button id="flashButton" class="btn btn-warning">
                <i class="bi bi-lightning-fill"></i> Flash: OFF
            </button>
        </div>

        <audio id="beep" src="<?= base_url('file/beep.mp3') ?>" preload="auto"></audio>
        <audio id="errorSound" src="<?= base_url('file/beep.mp3') ?>" preload="auto"></audio>
    </div>

    <script>
        const video = document.getElementById('video');
        const startBtn = document.getElementById('startButton');
        const stopBtn = document.getElementById('stopButton');
        const mirrorBtn = document.getElementById('mirrorButton');
        const flashBtn = document.getElementById('flashButton');
        const cameraSelect = document.getElementById('cameraSelect');
        const scanRegionHighlight = document.getElementById('scanRegionHighlight');
        const scanLine = document.getElementById('scanLine');
        const scannerStatus = document.getElementById('scannerStatus');
        const beep = document.getElementById('beep');
        const errorSound = document.getElementById('errorSound');
        const permissionPrompt = document.getElementById('permissionPrompt');
        const requestPermissionBtn = document.getElementById('requestPermissionBtn');

        let scanning = false;
        let currentStream = null;
        let mirrorOn = false;
        let flashOn = false;
        let selectedDeviceId = null;
        let codeReader = null;
        let quaggaActive = false;
        let scanTimeout = null;
        let cameraPermissionGranted = false;

        // Fungsi untuk memeriksa status permission kamera
        async function checkCameraPermission() {
            try {
                // Permissions API hanya tersedia di Chrome dan beberapa browser modern
                if (navigator.permissions) {
                    const permissionStatus = await navigator.permissions.query({
                        name: 'camera'
                    });
                    updatePermissionUI(permissionStatus.state);

                    permissionStatus.onchange = () => {
                        updatePermissionUI(permissionStatus.state);
                    };
                } else {
                    // Fallback untuk browser yang tidak mendukung Permissions API
                    permissionPrompt.style.display = 'block';
                }
            } catch (error) {
                console.error('Error checking camera permission:', error);
                permissionPrompt.style.display = 'block';
            }
        }

        function updatePermissionUI(state) {
            if (state === 'granted') {
                cameraPermissionGranted = true;
                permissionPrompt.style.display = 'none';
                getCameras();
            } else {
                cameraPermissionGranted = false;
                permissionPrompt.style.display = 'block';
            }
        }

        // Fungsi untuk meminta izin kamera
        async function requestCameraPermission() {
            try {
                // Coba akses kamera untuk memicu permintaan izin
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });

                // Jika berhasil, hentikan stream dan perbarui UI
                stream.getTracks().forEach(track => track.stop());
                cameraPermissionGranted = true;
                permissionPrompt.style.display = 'none';
                getCameras();

                showNotification("Izin kamera diberikan", 'success');
            } catch (error) {
                console.error('Error requesting camera permission:', error);
                showNotification("Gagal mendapatkan izin kamera", 'error');

                // Tampilkan petunjuk untuk mengubah izin secara manual
                if (error.name === 'NotAllowedError') {
                    scannerStatus.textContent = "Izin kamera ditolak. Silakan ubah di pengaturan browser.";
                    scannerStatus.classList.add('error');
                }
            }
        }

        // Inisialisasi ZXing code reader
        function initZXing() {
            codeReader = new ZXingBrowser.BrowserMultiFormatReader();
        }

        function showNotification(title, icon = 'success', timer = 1500) {
            Swal.fire({
                position: "top-end",
                icon: icon,
                title: title,
                showConfirmButton: false,
                timer: timer,
                toast: true,
                background: '#0c7369',
                color: 'white',
                width: '400px'
            });
        }

        async function postToController(code) {
            try {
                // Tampilkan status scanning
                scannerStatus.textContent = "Memproses data...";
                scannerStatus.classList.add('error');

                const response = await fetch('<?= base_url('nama_url_aksi') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `qr_data=${encodeURIComponent(code)}`
                });

                const result = await response.json();

                if (result.status === 'success') {
                    beep.play().catch(() => {});
                    showNotification(result.message, 'success');
                } else {
                    errorSound.play().catch(() => {});
                    showNotification(result.message, 'error', 3000);
                }

                // Reset status setelah 2 detik
                setTimeout(() => {
                    scannerStatus.textContent = "Scanner aktif";
                    scannerStatus.classList.remove('error');
                }, 2000);

            } catch (e) {
                console.error(e);
                errorSound.play().catch(() => {});
                showNotification('Gagal mengirim ke server', 'error', 3000);
                scannerStatus.textContent = "Error: Gagal mengirim data";
                scannerStatus.classList.add('error');
            }
        }

        async function getCameras() {
            try {
                scannerStatus.textContent = "Mendeteksi kamera...";
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');

                cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';
                videoDevices.forEach(device => {
                    const label = device.label || `Kamera ${cameraSelect.length + 1}`;
                    cameraSelect.innerHTML += `<option value="${device.deviceId}">${label}</option>`;
                });

                scannerStatus.textContent = "Pilih kamera untuk memulai";

                // Jika hanya ada 1 kamera, pilih otomatis
                if (videoDevices.length === 1) {
                    cameraSelect.value = videoDevices[0].deviceId;
                    selectedDeviceId = videoDevices[0].deviceId;
                }
            } catch (err) {
                console.error(err);
                scannerStatus.textContent = "Error: Gagal mengakses kamera";
                scannerStatus.classList.add('error');
                showNotification('Gagal mengakses daftar kamera', 'error', 3000);
            }
        }

        function toggleMirrorEffect() {
            mirrorOn = !mirrorOn;
            video.style.transform = mirrorOn ? 'scaleX(-1)' : 'scaleX(1)';
            mirrorBtn.innerHTML = `<i class="bi bi-arrow-left-right"></i> Mirror: ${mirrorOn ? 'ON' : 'OFF'}`;
        }

        async function toggleFlash() {
            if (currentStream) {
                const track = currentStream.getVideoTracks()[0];
                try {
                    const capabilities = track.getCapabilities();
                    if (capabilities.torch) {
                        flashOn = !flashOn;
                        await track.applyConstraints({
                            advanced: [{
                                torch: flashOn
                            }]
                        });
                        flashBtn.innerHTML = `<i class="bi bi-lightning-fill"></i> Flash: ${flashOn ? 'ON' : 'OFF'}`;
                    } else {
                        showNotification("Perangkat tidak mendukung flash", 'warning');
                    }
                } catch (err) {
                    console.error("Error toggling flash:", err);
                    showNotification("Gagal mengaktifkan flash", 'error', 3000);
                }
            }
        }

        function startScannerVisualFeedback() {
            scanRegionHighlight.style.display = 'block';
            scanLine.style.display = 'block';
            scannerStatus.textContent = "Scanner aktif";
            scannerStatus.classList.remove('error');
        }

        function stopScannerVisualFeedback() {
            scanRegionHighlight.style.display = 'none';
            scanLine.style.display = 'none';
            scannerStatus.textContent = "Scanner dihentikan";
        }

        async function startScan() {
            if (scanning) return;

            // Periksa izin kamera sebelum memulai
            if (!cameraPermissionGranted) {
                showNotification("Izin kamera diperlukan untuk memulai scanner", 'warning');
                permissionPrompt.style.display = 'block';
                return;
            }

            selectedDeviceId = cameraSelect.value;
            if (!selectedDeviceId) {
                showNotification("Silakan pilih kamera terlebih dahulu", 'warning');
                return;
            }

            try {
                // Setup video stream
                const constraints = {
                    audio: false,
                    video: {
                        deviceId: {
                            exact: selectedDeviceId
                        },
                        width: {
                            ideal: 720
                        },
                        height: {
                            ideal: 720
                        }
                    }
                };

                currentStream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = currentStream;

                // Setup UI
                scanning = true;
                startBtn.disabled = true;
                stopBtn.disabled = false;
                flashBtn.disabled = false;
                mirrorBtn.disabled = false;
                cameraSelect.disabled = true;

                startScannerVisualFeedback();

                // Inisialisasi scanner untuk QR Code dan Barcode
                initZXing();
                codeReader.decodeFromVideoDevice(selectedDeviceId, video, (result, err) => {
                    if (result) {
                        handleScanResult(result.text);
                    }
                });

                // Inisialisasi Quagga untuk Barcode
                startQuagga();
                quaggaActive = true;

                showNotification("Scanner berhasil diaktifkan", 'success');

            } catch (err) {
                console.error("Error starting scan:", err);
                scanning = false;
                scannerStatus.textContent = "Error: Gagal memulai scanner";
                scannerStatus.classList.add('error');

                if (err.name === 'NotAllowedError') {
                    showNotification('Izin kamera ditolak. Silakan berikan izin kamera.', 'error', 3000);
                    permissionPrompt.style.display = 'block';
                    cameraPermissionGranted = false;
                } else {
                    showNotification('Gagal mengakses kamera', 'error', 3000);
                }

                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
            }
        }

        function stopScan() {
            if (!scanning) return;

            scanning = false;
            isWaiting = false;
            flashOn = false;

            // Hentikan semua scanner
            if (codeReader) {
                codeReader = null;
                codeReader = new ZXingBrowser.BrowserMultiFormatReader();
            }

            if (quaggaActive) {
                Quagga.stop();
                quaggaActive = false;
            }

            // Hentikan video stream
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }

            video.srcObject = null;

            // Reset UI
            startBtn.disabled = false;
            stopBtn.disabled = true;
            flashBtn.disabled = true;
            mirrorBtn.disabled = true;
            cameraSelect.disabled = false;

            stopScannerVisualFeedback();
            scanRegionHighlight.style.display = 'none';
            detectionBox = null;

            showNotification("Scanner dihentikan", 'info');
        }

        function startQuagga() {
            Quagga.init({
                inputStream: {
                    type: "LiveStream",
                    target: video,
                    constraints: {
                        deviceId: selectedDeviceId,
                        width: 1280,
                        height: 720,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    // readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
                    readers: ["code_128_reader"]
                },
                locate: true,
                debug: {
                    drawBoundingBox: false,
                    showFrequency: false,
                    drawScanline: false,
                    showPattern: false
                }
            }, function(err) {
                if (err) {
                    console.error("Quagga init error:", err);
                    showNotification("Gagal memulai barcode scanner", 'warning');
                    return;
                }

                Quagga.start();
                Quagga.onDetected(data => {
                    if (data.codeResult) {
                        handleScanResult(data.codeResult.code);
                    }
                });
            });
        }

        function handleScanResult(code) {
            // Debounce scan - hanya proses 1x dalam 2 detik
            if (scanTimeout) {
                return;
            }

            // Validasi kode
            if (!code || code.trim() === '') {
                return;
            }

            console.log("Kode terdeteksi:", code);
            postToController(code);

            // Set timeout untuk mencegah scan berulang
            scanTimeout = setTimeout(() => {
                scanTimeout = null;
            }, 2000);
        }

        // Event listeners
        startBtn.addEventListener('click', startScan);
        stopBtn.addEventListener('click', stopScan);
        mirrorBtn.addEventListener('click', toggleMirrorEffect);
        flashBtn.addEventListener('click', toggleFlash);
        requestPermissionBtn.addEventListener('click', requestCameraPermission);

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            initZXing();
            checkCameraPermission();

            // Deteksi perubahan kamera
            cameraSelect.addEventListener('change', () => {
                if (scanning) {
                    stopScan();
                }
            });
        });

        // Tangani ketika halaman ditutup/tidak aktif
        window.addEventListener('beforeunload', () => {
            stopScan();
        });
    </script>
</body>

</html>