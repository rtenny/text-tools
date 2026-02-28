<script>
    lucide.createIcons();

    // Auto-fade success and info messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successAlerts = document.querySelectorAll('.alert-success, .alert-info');

        successAlerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease-out';

            setTimeout(function() {
                alert.style.opacity = '0';

                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>
