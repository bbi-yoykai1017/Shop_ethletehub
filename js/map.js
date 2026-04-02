// BẢN ĐỒ
var map = L.map('map').setView([10.762622, 106.660172], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
var marker = L.marker([10.762622, 106.660172], { draggable: true }).addTo(map);

marker.on('dragend', function () {
    let pos = marker.getLatLng();
    document.getElementById('lat').value = pos.lat;
    document.getElementById('lng').value = pos.lng;
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`)
        .then(r => r.json()).then(d => document.getElementById('address').value = d.display_name || '');
});

function searchAddress() {
    let val = document.getElementById('address').value;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${val}`)
        .then(r => r.json()).then(d => {
            if (d[0]) {
                marker.setLatLng([d[0].lat, d[0].lon]);
                map.setView([d[0].lat, d[0].lon], 16);
                document.getElementById('lat').value = d[0].lat;
                document.getElementById('lng').value = d[0].lon;
            }
        });
}