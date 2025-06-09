<div>


    <div id="map" style="height: 100vh; width: 100%;"></div>

    <!-- Leaflet (JS/CSS) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>

    <!-- Leaflet-KMZ -->
    <script src="https://unpkg.com/leaflet-kmz@latest/dist/leaflet-kmz.js"></script>

    <script>
        var map = L.map('map', {
            preferCanvas: true
        }).setView([17.6263193, 121.734748], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 25,
            opacity: 0.90,
            attribution: 'Map data: &copy; <a href="#">OpenStreetMap</a>, <a href="#">SRTM</a> | Map style: &copy; <a href="#">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
        }).addTo(map);

        var kmz = L.kmzLayer().addTo(map);
        kmz.on('load', function(e) {
            control.addOverlay(e.layer, e.name);
        });

        // kmz.load('{{ asset('gis/tugue.kmz') }}');
        kmz.load('{{ asset('storage/gis/pipes.kml') }}');
        kmz.load('{{ asset('storage/gis/PS.kmz') }}');

        var control = L.control.layers(null, null, { collapsed:false }).addTo(map);
    </script>

</div>
