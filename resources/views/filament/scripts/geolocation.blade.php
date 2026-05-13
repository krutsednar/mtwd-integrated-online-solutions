<script>
    function initLocationPicker($wire) {
        document.addEventListener("livewire:load", () => {
            if (!navigator.geolocation) {
                console.warn("Geolocation is not supported.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Update this based on your actual form field structure
                    $wire.set('location', { lat, lng });
                    $wire.set('lat', lat);
                    $wire.set('lng', lng);
                },
                (error) => {
                    console.error("Geolocation error:", error);
                }
            );
        });
    }
</script>
