
document.addEventListener('DOMContentLoaded', () => {
    // Toggle menu
    document.querySelectorAll('.dots-button').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;

            // Close others
            document.querySelectorAll('.dropdown-content').forEach(drop => {
                if (drop !== menu) drop.style.display = 'none';
            });

            // Toggle current
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });
    });

    // Close on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-content').forEach(drop => {
            drop.style.display = 'none';
        });
    });
});

