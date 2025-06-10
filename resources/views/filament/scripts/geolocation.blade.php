<div>
    <script>
        function initLocationPicker($wire) {
            if (!navigator.geolocation) {
                console.warn("Geolocation is not supported.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    $wire.set('data.location', { lat: lat, lng: lng });
                    $wire.set('data.lat', lat);
                    $wire.set('data.lng', lng);
                },
                (error) => {
                    console.error("Geolocation error:", error);
                }
            );
        }
    </script>
</div>
