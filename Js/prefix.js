document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('userSidebar');
    const userButton = document.getElementById('userButton');

    if (userButton && sidebar) {
        userButton.addEventListener('click', toggleUserSidebar);
    }

    function toggleUserSidebar() {
        sidebar.classList.toggle('visible');
    }
});