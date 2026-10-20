<?php
require_once 'config.php';

$locations = $pdo->query('SELECT * FROM recycling_locations')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoHub - Map</title>
<link rel="stylesheet" href="assets/css/styling.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<body>
<?php include 'navbar.php'; ?>
<h2>Lokasi Daur Ulang Terdekat</h2>
<div class="map-container">
    <div id="map"></div>
</div>


<script>
var map = L.map('map').setView([-6.200000, 106.816666], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data © OpenStreetMap contributors'
}).addTo(map);

<?php foreach($locations as $loc): ?>
L.marker([<?= $loc['latitude'] ?>, <?= $loc['longitude'] ?>])
    .addTo(map)
    .bindPopup("<b><?= addslashes($loc['name']) ?></b><br><?= addslashes($loc['address']) ?>");
<?php endforeach; ?>
// ============================
// LOKASI USER (REALTIME)
// ============================
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var userLat = position.coords.latitude;
        var userLng = position.coords.longitude;

        var userIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
            iconSize: [35, 35],
            iconAnchor: [17, 34],
            popupAnchor: [0, -30]
        });

        var userMarker = L.marker([userLat, userLng], {
            icon: userIcon
        }).addTo(map)
          .bindPopup("📍 Lokasi Kamu Sekarang")
          .openPopup();

        // Fokuskan map ke lokasi user
        map.setView([userLat, userLng], 13);
    }, function() {
        alert("Gagal mengambil lokasi kamu.");
    });
} else {
    alert("Browser kamu tidak mendukung GPS.");
}
</script>



<footer>
<p>© 2025 EcoHub</p>
</footer>
</body>
</html>
