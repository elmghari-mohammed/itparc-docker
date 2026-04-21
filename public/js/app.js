// JavaScript pour la page d'ajout d'équipement

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('equipmentForm');
    const cancelButton = document.querySelector('.btn-secondary');
    
    // Validation du formulaire avant soumission
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
    
    // Gestion du bouton annuler
    cancelButton.addEventListener('click', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir annuler? Toutes les données saisies seront perdues.')) {
            e.preventDefault();
        }
    });
    
    // Validation en temps réel
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
    
    // Set minimum date for warranty to today
    const today = new Date().toISOString().split('T')[0];
    const warrantyInput = document.getElementById('garantieFin');
    if (warrantyInput) {
        warrantyInput.setAttribute('min', today);
    }
});

// Valider un champ spécifique
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    const fieldName = field.name;
    
    // Supprimer les erreurs précédentes
    clearFieldError(field);
    
    // Validation selon le type de champ
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'Ce champ est obligatoire');
        return false;
    }
    
    switch (fieldName) {
        case 'serialNumber':
            if (value && value.length < 5) {
                showFieldError(field, 'Le numéro de série doit contenir au moins 5 caractères');
                return false;
            }
            break;
            
        case 'garantieFin':
            if (value) {
                const selectedDate = new Date(value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate <= today) {
                    showFieldError(field, 'La date doit être dans le futur');
                    return false;
                }
            }
            break;
    }
    
    return true;
}

// Afficher une erreur pour un champ
function showFieldError(field, message) {
    field.classList.add('error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

// Effacer les erreurs d'un champ
function clearFieldError(e) {
    const field = e.target || e;
    field.classList.remove('error');
    
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Valider tout le formulaire
function validateForm() {
    let isValid = true;
    const fields = document.querySelectorAll('#equipmentForm [required]');
    
    fields.forEach(field => {
        if (!validateField({ target: field })) {
            isValid = false;
        }
    });
    
    return isValid;
}