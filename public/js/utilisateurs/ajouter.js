        // Gestion de la sélection du type d'utilisateur
        const userTypeSelect = document.getElementById('userType');
        const numeroField = document.getElementById('numeroField');
        const numeroInput = document.getElementById('numero');

        userTypeSelect.addEventListener('change', function() {
            const userType = this.value;
            
            // Animation pour les changements
            numeroField.style.transition = 'all 0.3s ease';
            
            // Réinitialiser
            numeroField.classList.add('hidden');
            numeroInput.removeAttribute('required');
            
            // Afficher selon le type
            switch(userType) {
                case 'technicien':
                case 'support':
                    setTimeout(() => {
                        numeroField.classList.remove('hidden');
                        numeroInput.setAttribute('required', 'required');
                    }, 150);
                    break;
            }
        });

        // Validation de force du mot de passe
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const passwordMatch = document.getElementById('passwordMatch');
        const passwordMismatch = document.getElementById('passwordMismatch');
        const confirmIcon = document.getElementById('confirmIcon');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/) && password.match(/[^a-zA-Z0-9]/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            strengthBar.className = 'strength-bar';
            
            if (strength <= 25) strengthBar.classList.add('strength-weak');
            else if (strength <= 50) strengthBar.classList.add('strength-medium');
            else strengthBar.classList.add('strength-strong');
            
            // Vérifier la correspondance des mots de passe
            checkPasswordMatch();
        });

        // Vérification de la correspondance des mots de passe
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length === 0) {
                passwordMatch.classList.remove('show-indicator');
                passwordMismatch.classList.remove('show-indicator');
                confirmIcon.style.color = '';
                return;
            }
            
            if (password === confirmPassword) {
                passwordMatch.classList.add('show-indicator');
                passwordMismatch.classList.remove('show-indicator');
                confirmIcon.style.color = 'var(--success)';
            } else {
                passwordMatch.classList.remove('show-indicator');
                passwordMismatch.classList.add('show-indicator');
                confirmIcon.style.color = 'var(--danger)';
            }
        }

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
            }
        }

        // Validation côté client améliorée
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const errorNotification = document.getElementById('errorNotification');
            const errorMessage = document.getElementById('errorMessage');
            const successNotification = document.getElementById('successNotification');
            
            // Réinitialiser les notifications
            errorNotification.style.display = 'none';
            successNotification.style.display = 'none';
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                errorMessage.textContent = 'Les mots de passe ne correspondent pas.';
                errorNotification.style.display = 'flex';
                errorNotification.scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            const userType = userTypeSelect.value;
            if (!userType) {
                e.preventDefault();
                errorMessage.textContent = 'Veuillez sélectionner un type d\'utilisateur.';
                errorNotification.style.display = 'flex';
                errorNotification.scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            if ((userType === 'technicien' || userType === 'support') && !numeroInput.value) {
                e.preventDefault();
                errorMessage.textContent = 'Le numéro de téléphone est requis pour ce type d\'utilisateur.';
                errorNotification.style.display = 'flex';
                errorNotification.scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            // Simulation de succès (remplacer par la vraie soumission)
            e.preventDefault();
            successNotification.style.display = 'flex';
            successNotification.scrollIntoView({ behavior: 'smooth' });
            
            // Réinitialiser le formulaire après 3 secondes
            setTimeout(() => {
                this.reset();
                successNotification.style.display = 'none';
                strengthBar.style.width = '0%';
                passwordMatch.classList.remove('show-indicator');
                passwordMismatch.classList.remove('show-indicator');
                confirmIcon.style.color = '';
            }, 3000);
        });

        // Fonctionnalité d'annulation améliorée
        document.querySelector('.btn-outline').addEventListener('click', function() {
            if (confirm('Voulez-vous vraiment annuler? Toutes les modifications seront perdues.')) {
                // Animation de sortie
                document.querySelector('.container').style.opacity = '0';
                document.querySelector('.container').style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    window.location.href = 'liste_utilisateurs.html';
                }, 300);
            }
        });

        // Animation d'entrée pour les sections
        const sections = document.querySelectorAll('.form-section');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                section.style.transition = 'all 0.6s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 200 * index);
        });

        // Validation en temps réel de l'email
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            if (email && !email.includes('@')) {
                this.style.borderColor = 'var(--danger)';
                this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
            } else {
                this.style.borderColor = 'var(--border)';
                this.style.boxShadow = 'none';
            }
        });

        // Auto-formatting pour le numéro de téléphone
        numeroInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.startsWith('212')) {
                value = '+' + value.substring(0, 3) + ' ' + value.substring(3);
            } else if (value.startsWith('0')) {
                value = '+212 ' + value.substring(1);
            }
            this.value = value;
        });