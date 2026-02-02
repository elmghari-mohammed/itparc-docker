document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleCircle = document.getElementById('sidebarToggleCircle');
    
    // Toggle sidebar open/close
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        const icon = sidebarToggleCircle.querySelector('i');
        icon.style.transform = sidebar.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
    }

    // Event listener pour le bouton de toggle
    sidebarToggleCircle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });

    // Fermer la sidebar si on clique à l'extérieur
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) &&
            !sidebarToggleCircle.contains(event.target) &&
            sidebar.classList.contains('open')) {
            toggleSidebar();
        }
    });

    // Gestion du hover et des effets visuels
    sidebar.addEventListener('mouseenter', function(event) {
        if (event.target.classList.contains('menu-item') && 
            !event.target.classList.contains('active')) {
            event.target.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
        }
    });

    sidebar.addEventListener('mouseleave', function(event) {
        if (event.target.classList.contains('menu-item') && 
            !event.target.classList.contains('active')) {
            event.target.style.backgroundColor = '';
        }
    });

    // Gestion du clic sur les liens
    sidebar.addEventListener('click', function(event) {
        const menuItem = event.target.closest('.menu-item');
        if (menuItem && menuItem.tagName === 'A') {
            // Optionnel: ajouter un effet de loading
            menuItem.classList.add('loading');
            
            // Fermer la sidebar sur mobile après clic
            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    toggleSidebar();
                }, 100);
            }
        }
    });

    // Gérer le responsive
    function handleResize() {
        if (window.innerWidth > 768 && sidebar.classList.contains('open')) {
            // Sur desktop, garder la sidebar ouverte
        } else if (window.innerWidth <= 768) {
            // Sur mobile, fermer par défaut
            sidebar.classList.remove('open');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Appeler une fois au chargement
});