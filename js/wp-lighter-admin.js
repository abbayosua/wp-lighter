document.addEventListener('DOMContentLoaded', function() {
    const checkAllButton = document.getElementById('wp-lighter-check-all');
    const uncheckAllButton = document.getElementById('wp-lighter-uncheck-all');
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="wp_lighter_options"]');

    if (checkAllButton) {
        checkAllButton.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }

    if (uncheckAllButton) {
        uncheckAllButton.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    }
});