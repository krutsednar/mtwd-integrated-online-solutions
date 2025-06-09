<x-filament-panels::page>
    <span class="text-xl"><b>Metropolitan Tuguegarao Water District: </b><i>Sourcing Water, Shaping Lives</i></span>


    <x-filament::button
        href="https://filamentphp.com"
        id="toggleMarkersBtn"
        style="width: 200px;"
    >
        Hide Job Orders
    </x-filament::button>


    <div id="map" style="height: 90vh;" class="w-full"></div>

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

        var joIcon = L.icon({
            iconUrl: '{{ asset('images/wrench.svg') }}',
            iconSize: [20, 20],
        });

        // Array to hold all markers
        var markers = [];

        const jobOrders = @json($jobOrders);

        jobOrders.forEach(order => {
            if (order.lat && order.lng) {
                let marker = L.marker([order.lat, order.lng], {icon: joIcon})
                    .bindPopup(`<b>JO No.: ${order.jo_number}</b>
                    <br>Date Requested: ${order.date_requested}
                    <br>Account Number: ${order.account_number}
                    <br>Registered Name: ${order.registered_name}
                    <br>Type: ${order.jobOrderCode?.description ?? 'N/A'}
                    <br>Division Concerned: ${order.jobOrderCode?.division?.name ?? 'N/A'}
                    <br>Status: ${order.status}
                    `)
                    .addTo(map);
                markers.push(marker);
            }
        });

        // Toggle button logic
        let markersVisible = true;
        const toggleBtn = document.getElementById('toggleMarkersBtn');

        toggleBtn.addEventListener('click', () => {
            if (markersVisible) {
                markers.forEach(marker => map.removeLayer(marker));
                toggleBtn.textContent = 'Show Job Orders';
                markersVisible = false;
            } else {
                markers.forEach(marker => map.addLayer(marker));
                toggleBtn.textContent = 'Hide Job Orders';
                markersVisible = true;
            }
        });
    </script>
    <style>
        .leaflet-container {
            z-index: 1;
        }
    </style>
</x-filament-panels::page>
