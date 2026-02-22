const express = require('express');
const path = require('path');
const app = express();
const port = process.env.PORT || 3000;

// Statik dosyaları serve et
app.use(express.static(path.join(__dirname, './')));

// Ana sayfayı yönlendir
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

app.listen(port, () => {
    console.log(`Server ${port} portunda çalışıyor`);
});
