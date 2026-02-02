<?php
// confirmation.php : Composant confirmation réutilisable avec bouton Annuler personnalisable
?>

<!-- Confirmation Overlay Standard -->
<div class="confirmation-overlay" id="confirmationOverlay">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <div class="confirmation-icon" id="confirmationIcon">!</div>
            <h2 class="confirmation-title" id="confirmationTitle"></h2>
        </div>
        <div class="confirmation-body">            
            <div class="more-Details" id="moreDetails"></div>
            <div class="confirmation-actions">
                <button class="confirmation-btn btn-cancel" id="cancelActionBtn">Annuler</button>
                <button class="confirmation-btn btn-confirm" id="confirmActionBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ... (identique à ton style existant) ... */
:root {
    --light: #f8fafc;
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --accent: #8b5cf6;
    --accent-light: #a78bfa;
    --danger: #ef4444;
    --warning: #f59e0b;
    --white: #ffffff;
    --secondary: #64748b;
    --border: #e2e8f0;
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --dark: #1e293b;
}
.confirmation-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,.5); display: flex; justify-content: center; align-items: center;
    z-index: 1000; opacity: 0; visibility: hidden; transition: var(--transition);
}
.confirmation-overlay.active { opacity: 1; visibility: visible; }
.confirmation-card {
    background-color: var(--white); border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg); width: 100%; max-width: 450px; overflow: hidden;
    transform: translateY(20px) scale(0.95); transition: var(--transition);
    animation: pulse 0.5s ease-in-out;
}
.confirmation-overlay.active .confirmation-card { transform: translateY(0) scale(1);}
.confirmation-header {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--white); padding: 20px; text-align: center; position: relative;
}
.confirmation-header.danger { background: linear-gradient(135deg, var(--danger), #dc2626);}
.confirmation-header.warning { background: linear-gradient(135deg, var(--warning), #d97706);}
.confirmation-icon {
    width: 60px; height: 60px; background-color: var(--white); border-radius: 50%;
    display: flex; justify-content: center; align-items: center;
    margin: 0 auto 15px; color: var(--primary); font-size: 28px; font-weight: bold;
}
.confirmation-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 5px;}
.confirmation-body { padding: 25px; color: var(--dark);}
.more-Details {
    display: flex; align-items: center; justify-content: center; margin-bottom: 20px;
    padding: 12px; background-color: var(--light); border-radius: 8px;
}
.confirmation-actions { display: flex; gap: 12px;}
.confirmation-btn {
    flex: 1; padding: 12px 20px; border: none; border-radius: 8px;
    font-weight: 600; cursor: pointer; transition: var(--transition); font-size: 1rem;
}
.btn-cancel { background-color: var(--light); color: var(--secondary);}
.btn-cancel:hover { background-color: var(--border);}
.btn-confirm {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--white); box-shadow: 0 4px 6px rgba(59,130,246,0.3);
}
.btn-confirm:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-2px); box-shadow: 0 6px 8px rgba(59,130,246,0.4);
}
@keyframes pulse {0% { transform: scale(1);} 50% { transform: scale(1.02);} 100% { transform: scale(1);}}
@media (max-width: 500px) {
    .confirmation-card { max-width: 90%; }
    .confirmation-actions { flex-direction: column;}
}
</style>

<script>
/**
 * confirmation(text, okText = "Confirmer", details = null, acceptCallback = null, cancelCallback = null, cancelText = "Annuler")
 * @param {string} text         - Le texte principal (H2)
 * @param {string} okText       - Texte du bouton Confirmer/OK
 * @param {string|null} details - HTML dans #moreDetails (optionnel)
 * @param {function|null} acceptCallback
 * @param {function|null} cancelCallback
 * @param {string} cancelText   - Texte du bouton Annuler/Fermer/Non/Retour...
 */
(function(global){
    let acceptCb = null, cancelCb = null;
    function confirmation(text, okText = "Confirmer", details = null, acceptCallback = null, cancelCallback = null, cancelText = "Annuler") {
        const overlay = document.getElementById('confirmationOverlay');
        const header = overlay.querySelector('.confirmation-header');
        const icon = overlay.querySelector('#confirmationIcon');
        const title = overlay.querySelector('#confirmationTitle');
        const moreDetails = overlay.querySelector('#moreDetails');
        const confirmBtn = overlay.querySelector('#confirmActionBtn');
        const cancelBtn = overlay.querySelector('#cancelActionBtn');

        // Reset header style
        header.className = "confirmation-header";
        icon.innerHTML = "!";

        // Texte principal
        title.textContent = text || "";

        // Détails (HTML)
        if (details) {
            moreDetails.innerHTML = details;
            moreDetails.style.display = "flex";
        } else {
            moreDetails.innerHTML = "";
            moreDetails.style.display = "none";
        }

        // Bouton "Confirmer/OK"
        confirmBtn.textContent = okText || "Confirmer";
        if (okText === "OK") confirmBtn.classList.add("btn-ok");
        else confirmBtn.classList.remove("btn-ok");

        // Bouton Annuler/Fermer/Retour
        cancelBtn.textContent = cancelText || "Annuler";

        acceptCb = typeof acceptCallback === "function" ? acceptCallback : null;
        cancelCb = typeof cancelCallback === "function" ? cancelCallback : null;

        // Show
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Events
        confirmBtn.onclick = function(){
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
            if (acceptCb) acceptCb();
        };
        cancelBtn.onclick = function(){
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
            if (cancelCb) cancelCb();
        };
    }
    global.confirmation = confirmation;
})(window);
</script>