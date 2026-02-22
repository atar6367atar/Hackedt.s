const express = require('express');
const path = require('path');
const app = express();
const port = process.env.PORT || 3000;

// Tüm dosyaları olduğu gibi serve et
app.use(express.static(path.join(__dirname)));

// Tüm yönlendirmeleri index.html'e yönlendir (SPA desteği)
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Server ${port} portunda çalışıyor`);
});
