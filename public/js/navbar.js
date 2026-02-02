document.addEventListener('DOMContentLoaded', function() {
    const notifIcons = document.querySelectorAll('.nav-icons i');
    notifIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            alert("Action sur " + icon.className);
        });
    });
});
