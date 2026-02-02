document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du DOM
    const profilePreview = document.getElementById('profile-preview');
    const nomInput = document.getElementById('nom');
    const prenomInput = document.getElementById('prenom');
    const nomError = document.getElementById('nom-error');
    const prenomError = document.getElementById('prenom-error');
    const displayName = document.getElementById('display-name');
    const telephoneInput = document.getElementById('telephone');
    const telephoneError = document.getElementById('telephone-error');
    const profileForm = document.getElementById('profile-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Variables pour suivre l'état de validation
    let isNomValid = true;
    let isPrenomValid = true;
    let isTelephoneValid = true;
    
    // Fonction pour mettre à jour l'avatar avec le nom et prénom
    function updateProfilePicture() {
        const nom = nomInput.value || 'Utilisateur';
        const prenom = prenomInput.value || '';
        const fullName = `${prenom} ${nom}`.trim();
        
        if (fullName) {
            profilePreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=3b82f6&color=fff`;
        }
    }
    
    // Fonction pour mettre à jour l'affichage du nom
    function updateDisplayName() {
        const nom = nomInput.value || '';
        const prenom = prenomInput.value || '';
        const fullName = `${prenom} ${nom}`.trim();
        
        if (fullName) {
            displayName.textContent = fullName;
        } else {
            displayName.textContent = 'Utilisateur';
        }
    }
    
    // Fonction pour valider le numéro de téléphone marocain
    function validateMoroccanPhoneNumber(phone) {
        // Expression régulière pour valider les formats marocains
        const regex = /^(0[5-7][0-9]{8}|(\+212)[5-7][0-9]{8})$/;
        return regex.test(phone.replace(/\s/g, '')); // Supprimer les espaces avant la validation
    }
    
    // Fonction pour valider le nom
    function validateNom() {
        const nomValue = nomInput.value.trim();
        if (nomValue === '') {
            nomError.style.display = 'block';
            nomInput.parentElement.classList.add('error');
            isNomValid = false;
            return false;
        } else {
            nomError.style.display = 'none';
            nomInput.parentElement.classList.remove('error');
            isNomValid = true;
            return true;
        }
    }
    
    // Fonction pour valider le prénom
    function validatePrenom() {
        const prenomValue = prenomInput.value.trim();
        if (prenomValue === '') {
            prenomError.style.display = 'block';
            prenomInput.parentElement.classList.add('error');
            isPrenomValid = false;
            return false;
        } else {
            prenomError.style.display = 'none';
            prenomInput.parentElement.classList.remove('error');
            isPrenomValid = true;
            return true;
        }
    }
    
    // Fonction pour valider le téléphone
    function validateTelephone() {
        if (!telephoneInput) {
            isTelephoneValid = true;
            return true; // Pas de champ téléphone pour les agents
        }
        
        const phoneValue = telephoneInput.value.trim();
        
        if (phoneValue === '') {
            telephoneError.style.display = 'none';
            telephoneInput.parentElement.classList.remove('error');
            isTelephoneValid = true;
            return true;
        }
        
        if (!validateMoroccanPhoneNumber(phoneValue)) {
            telephoneError.style.display = 'block';
            telephoneInput.parentElement.classList.add('error');
            isTelephoneValid = false;
            return false;
        } else {
            telephoneError.style.display = 'none';
            telephoneInput.parentElement.classList.remove('error');
            isTelephoneValid = true;
            return true;
        }
    }
    
    // Fonction pour mettre à jour l'état du bouton de soumission
    function updateSubmitButton() {
        if (isNomValid && isPrenomValid && isTelephoneValid) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }
    
    // Écouter les changements sur les champs nom et prénom
    nomInput.addEventListener('input', function() {
        validateNom();
        updateProfilePicture();
        updateDisplayName();
        updateSubmitButton();
    });
    
    prenomInput.addEventListener('input', function() {
        validatePrenom();
        updateProfilePicture();
        updateDisplayName();
        updateSubmitButton();
    });
    
    // Écouter les changements sur le champ téléphone (s'il existe)
    if (telephoneInput) {
        telephoneInput.addEventListener('input', function() {
            validateTelephone();
            updateSubmitButton();
        });
    }
    
    // Validation initiale au chargement de la page
    validateNom();
    validatePrenom();
    validateTelephone();
    updateSubmitButton();
    
    // Gestion du formulaire d'informations
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Valider tous les champs avant soumission
        const isNomValid = validateNom();
        const isPrenomValid = validatePrenom();
        const isTelephoneValid = validateTelephone();
        
        if (isNomValid && isPrenomValid && isTelephoneValid) {
            // Afficher la confirmation avant soumission
            confirmation(
                "Confirmer les modifications",
                "Enregistrer",
                "Êtes-vous sûr de vouloir modifier vos informations personnelles ?",
                function() {
                    // Callback de confirmation - soumettre le formulaire
                    profileForm.submit();
                },
                function() {
                    // Callback d'annulation - ne rien faire
                    console.log("Modifications annulées");
                },
                "Annuler"
            );
        } else {
            updateSubmitButton();
        }
    });
    
    // Gestion de l'annulation avec confirmation si des modifications ont été faites
    profileForm.addEventListener('reset', function() {
        const nomChanged = nomInput.value !== nomInput.defaultValue;
        const prenomChanged = prenomInput.value !== prenomInput.defaultValue;
        const telephoneChanged = telephoneInput ? telephoneInput.value !== telephoneInput.defaultValue : false;
        
        if (nomChanged || prenomChanged || telephoneChanged) {
            // Afficher la confirmation avant réinitialisation
            confirmation(
                "Annuler les modifications",
                "Oui, annuler",
                "Voulez-vous vraiment annuler les modifications ? Tous les changements seront perdus.",
                function() {
                    // Callback de confirmation - réinitialiser le formulaire
                    resetForm();
                },
                function() {
                    // Callback d'annulation - ne rien faire
                    console.log("Annulation annulée");
                },
                "Non, continuer"
            );
        } else {
            // Réinitialiser directement si aucune modification
            resetForm();
        }
    });
    
    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        // Réinitialiser les états de validation
        isNomValid = true;
        isPrenomValid = true;
        isTelephoneValid = true;
        
        // Cacher les messages d'erreur
        nomError.style.display = 'none';
        prenomError.style.display = 'none';
        if (telephoneError) telephoneError.style.display = 'none';
        
        // Retirer les classes d'erreur
        nomInput.parentElement.classList.remove('error');
        prenomInput.parentElement.classList.remove('error');
        if (telephoneInput) telephoneInput.parentElement.classList.remove('error');
        
        // Réactiver le bouton après un court délai pour laisser les valeurs se réinitialiser
        setTimeout(() => {
            updateSubmitButton();
            updateProfilePicture();
            updateDisplayName();
        }, 10);
    }
    
    // Initialiser l'affichage du nom au chargement
    updateDisplayName();
});