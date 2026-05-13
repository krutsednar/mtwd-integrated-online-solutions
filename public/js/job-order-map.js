document.addEventListener('DOMContentLoaded', function () {
    // Wait for the Filament map to be initialized
    // The map instance should be accessible globally by mapId 'jobOrders'

    // Access the Leaflet map by ID
    const map = window.FilamentGoogleMaps?.maps?.jobOrders;
    if (!map) {
        console.warn('Map instance not found');
        return;
    }

    // Load GeoJSON from public/gis/SAMPLE.json
    fetch('/gis/SAMPLE.json')
        .then(response => response.json())
        .then(geojsonData => {
            // Add GeoJSON layer to the map
            L.geoJSON(geojsonData, {
                style: function (feature) {
                    return {
                        color: 'blue',
                        weight: 2,
                        fillOpacity: 0.3,
                    };
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties && feature.properties.name) {
                        layer.bindPopup(feature.properties.name);
                    }
                }
            }).addTo(map);
        })
        .catch(error => console.error('Error loading GeoJSON:', error));
});
