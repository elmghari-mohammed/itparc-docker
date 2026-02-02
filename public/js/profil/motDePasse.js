
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new-password');
            const confirmPassword = document.getElementById('confirm-password');
            const passwordMatch = document.getElementById('password-match');
            const reasonSelect = document.getElementById('reason');
            const otherReasonContainer = document.getElementById('other-reason-container');
            const passwordForm = document.querySelector('.password-form');
            
            // Vérification de la correspondance des mots de passe
            function checkPasswordMatch() {
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value === confirmPassword.value) {
                        passwordMatch.textContent = 'Les mots de passe correspondent';
                        passwordMatch.className = 'validation-message valid';
                    } else {
                        passwordMatch.textContent = 'Les mots de passe ne correspondent pas';
                        passwordMatch.className = 'validation-message invalid';
                    }
                } else {
                    passwordMatch.textContent = '';
                }
            }
            
            newPassword.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            // Affichage/masquage du champ "autre raison"
            reasonSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    otherReasonContainer.style.display = 'block';
                } else {
                    otherReasonContainer.style.display = 'none';
                }
            });
            
            // Validation du formulaire de mot de passe
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Vérification de la correspondance des mots de passe
                if (newPassword.value !== confirmPassword.value) {
                    alert('Les mots de passe ne correspondent pas');
                    return;
                }
                
                // Vérification de la force du mot de passe
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                if (!passwordRegex.test(newPassword.value)) {
                    alert('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre');
                    return;
                }
                
                // Vérification de la raison
                if (!reasonSelect.value) {
                    alert('Veuillez sélectionner une raison pour le changement de mot de passe');
                    return;
                }
                
                if (reasonSelect.value === 'other' && !document.getElementById('other-reason').value) {
                    alert('Veuillez préciser la raison du changement de mot de passe');
                    return;
                }
                
                alert('Mot de passe modifié avec succès!');
                passwordForm.reset();
                otherReasonContainer.style.display = 'none';
                passwordMatch.textContent = '';
            });
        });
 