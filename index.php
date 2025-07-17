<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tracking Link Creator</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <h1>Buat Link Tracking Anda</h1>
  
  <div class="input-container">
    <input type="text" id="urlInput" placeholder="Masukkan URL untuk di-track (contoh: https://example.com)" />
    <button onclick="createLink()">Buat Link</button>
  </div>
  
  <div id="result"></div>
  <div id="visits"></div>

  <script>
    // Simulasi database menggunakan localStorage untuk demo
    let trackingData = JSON.parse(localStorage.getItem('trackingData')) || {};
    let visitData = JSON.parse(localStorage.getItem('visitData')) || {};

    function validateUrl(url) {
      try {
        // Tambahkan protocol jika tidak ada
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
          url = 'https://' + url;
        }
        new URL(url);
        return url;
      } catch (e) {
        return null;
      }
    }

    function generateTrackingId() {
      return Math.random().toString(36).substr(2, 9);
    }

    function showError(message) {
      document.getElementById('result').innerHTML = `<div class="error">${message}</div>`;
    }

    function showSuccess(message) {
      document.getElementById('result').innerHTML = `<div class="success">${message}</div>`;
    }

    function showLoading(element) {
      element.innerHTML = '<span class="loading"></span>Memproses...';
    }

    async function createLink() {
      const urlInput = document.getElementById('urlInput');
      const url = urlInput.value.trim();
      
      if (!url) {
        showError('Silakan masukkan URL yang valid');
        return;
      }

      const validUrl = validateUrl(url);
      if (!validUrl) {
        showError('URL tidak valid. Pastikan format URL benar (contoh: https://example.com)');
        return;
      }

      showLoading(document.getElementById('result'));

      try {
        // Simulasi API call dengan delay
        await new Promise(resolve => setTimeout(resolve, 1000));

        const trackingId = generateTrackingId();
        const trackingLink = `${window.location.origin}/track/${trackingId}`;
        
        // Simpan data tracking
        trackingData[trackingId] = {
          originalUrl: validUrl,
          createdAt: new Date().toISOString(),
          visits: 0
        };
        
        // Inisialisasi data visit
        visitData[trackingId] = [];
        
        // Simpan ke localStorage
        localStorage.setItem('trackingData', JSON.stringify(trackingData));
        localStorage.setItem('visitData', JSON.stringify(visitData));

        // Tampilkan hasil
        document.getElementById('result').innerHTML = `
          <div class="success">
            <h3>Link Tracking Berhasil Dibuat!</h3>
            <p><strong>Original URL:</strong> ${validUrl}</p>
            <p><strong>Tracking Link:</strong> <a href="#" onclick="simulateVisit('${trackingId}')">${trackingLink}</a></p>
            <p><em>Klik link di atas untuk simulasi kunjungan</em></p>
          </div>
        `;
        
        // Tampilkan data visits
        fetchVisits(trackingId);
        
        // Clear input
        urlInput.value = '';

      } catch (error) {
        showError('Terjadi kesalahan saat membuat tracking link: ' + error.message);
      }
    }

    function simulateVisit(trackingId) {
      // Simulasi kunjungan link
      const visit = {
        ip: generateRandomIP(),
        location: generateRandomLocation(),
        timestamp: new Date().toLocaleString('id-ID'),
        userAgent: navigator.userAgent,
        referrer: document.referrer || 'Direct'
      };

      // Tambahkan visit ke data
      if (!visitData[trackingId]) {
        visitData[trackingId] = [];
      }
      visitData[trackingId].push(visit);
      
      // Update jumlah visits
      if (trackingData[trackingId]) {
        trackingData[trackingId].visits++;
      }

      // Simpan ke localStorage
      localStorage.setItem('trackingData', JSON.stringify(trackingData));
      localStorage.setItem('visitData', JSON.stringify(visitData));

      // Redirect ke URL asli (simulasi)
      const originalUrl = trackingData[trackingId]?.originalUrl;
      if (originalUrl) {
        showSuccess(`Redirecting ke: ${originalUrl}`);
        // window.open(originalUrl, '_blank'); // Uncomment untuk redirect sesungguhnya
      }

      // Update tampilan visits
      fetchVisits(trackingId);
    }

    function generateRandomIP() {
      return `${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}`;
    }

    function generateRandomLocation() {
      const locations = [
        { city: 'Jakarta', country: 'Indonesia' },
        { city: 'Surabaya', country: 'Indonesia' },
        { city: 'Bandung', country: 'Indonesia' },
        { city: 'Medan', country: 'Indonesia' },
        { city: 'Semarang', country: 'Indonesia' },
        { city: 'Singapore', country: 'Singapore' },
        { city: 'Kuala Lumpur', country: 'Malaysia' },
        { city: 'Bangkok', country: 'Thailand' }
      ];
      return locations[Math.floor(Math.random() * locations.length)];
    }

    async function fetchVisits(trackingId) {
      try {
        const visits = visitData[trackingId] || [];
        const trackingInfo = trackingData[trackingId];
        
        if (!trackingInfo) {
          document.getElementById('visits').innerHTML = '<div class="error">Data tracking tidak ditemukan</div>';
          return;
        }

        if (visits.length === 0) {
          document.getElementById('visits').innerHTML = `
            <h2>Statistik Kunjungan</h2>
            <p>Belum ada kunjungan untuk link ini.</p>
            <p><strong>Total Visits:</strong> 0</p>
          `;
          return;
        }

        let html = `
          <h2>Statistik Kunjungan</h2>
          <p><strong>Total Visits:</strong> ${visits.length}</p>
          <p><strong>URL Asli:</strong> <a href="${trackingInfo.originalUrl}" target="_blank">${trackingInfo.originalUrl}</a></p>
          <p><strong>Dibuat:</strong> ${new Date(trackingInfo.createdAt).toLocaleString('id-ID')}</p>
          <table>
            <tr>
              <th>IP Address</th>
              <th>Lokasi</th>
              <th>Waktu Kunjungan</th>
              <th>Referrer</th>
            </tr>
        `;
        
        visits.forEach(visit => {
          const location = visit.location && visit.location.city 
            ? `${visit.location.city}, ${visit.location.country}` 
            : 'Unknown';
          
          html += `
            <tr>
              <td>${visit.ip}</td>
              <td>${location}</td>
              <td>${visit.timestamp}</td>
              <td>${visit.referrer}</td>
            </tr>
          `;
        });
        
        html += '</table>';
        document.getElementById('visits').innerHTML = html;

      } catch (error) {
        document.getElementById('visits').innerHTML = `<div class="error">Error loading visits: ${error.message}</div>`;
      }
    }

    // Auto-refresh visits setiap 5 detik jika ada data
    setInterval(() => {
      const resultDiv = document.getElementById('result');
      if (resultDiv.innerHTML.includes('track/')) {
        const trackingId = resultDiv.innerHTML.match(/track\/([a-zA-Z0-9]+)/);
        if (trackingId) {
          fetchVisits(trackingId[1]);
        }
      }
    }, 5000);
  </script>
</body>
</html>