<x-filament-panels::page>
    <span class="text-xl"><b>Metropolitan Tuguegarao Water District: </b><i>Sourcing Water, Shaping Lives</i></span>
    <x-filament::button
        href="https://filamentphp.com"
        id="toggleMarkersBtn"
        style="width: 200px;"
    >
        Hide Job Orders
    </x-filament::button>
    <span class="font-bold text-m">Ongoing JOs: {{ $jobOrders->count() }}</span>

    <div id="map" style="height: 90vh;" class="w-full"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
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
        kmz.load('{{ asset('storage/gis/WaterMeters.kmz') }}');

        var control = L.control.layers(null, null, { collapsed:false }).addTo(map);

        var joIcon = L.icon({
            iconUrl: '{{ asset('images/wrench.svg') }}',
            iconSize: [20, 20],
        });

        var markers = [];

        const jobOrders = @json($jobOrders);

        jobOrders.forEach(order => {
            if (order.lat && order.lng) {
                const previous = order.previous_descriptions?.length
                    ? order.previous_descriptions.join(', ')
                    : 'None';
                let marker = L.marker([order.lat, order.lng], { icon: joIcon })
                    .on('click', function () {
                        fetch(`/executive/job-order/${order.id}`)
                            .then(res => res.json())
                            .then(data => {
                                const previous = data.previous_descriptions?.length
                                    ? data.previous_descriptions.join(', ')
                                    : 'None';

                                marker.bindPopup(`
                                    <b>JO No.:</b> ${data.jo_number}<br>
                                    <b>Date Requested:</b> ${data.date_requested}<br>
                                    <b>Requested By:</b> ${data.requested_by}<br>
                                    <b>Account Number:</b> ${data.account_number}<br>
                                    <b>Registered Name:</b> ${data.registered_name}<br>
                                    <b>Address:</b> ${data.address}<br>
                                    <b>Type:</b> ${data.jobOrderCode?.description ?? 'N/A'}<br>
                                    <b>Division Concerned:</b> ${data.jobOrderCode?.division?.name ?? 'N/A'}<br>
                                    <b>Status:</b> ${data.status}<br>
                                    <b>-------------------</b><br>
                                    <b>Total Job Orders:</b> ${data.total ?? 1}<br>
                                    <b>Previous Job Orders:</b> ${previous}
                                `).openPopup();
                            });
                    })
                    .addTo(map);

                markers.push(marker);
            }
        });

        // Accounts Marker
        var accountIcon = L.icon({
            iconUrl: '{{ asset('images/account.svg') }}',
            iconSize: [20, 20],
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
