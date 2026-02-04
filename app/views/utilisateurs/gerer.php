<?php
// /app/views/utilisateurs/gerer.php
?>

<div class="container">
    <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-users"></i>
                Gestion des Utilisateurs
            </h1>
            <p class="subtitle">Consulter et gérer tous les utilisateurs du système <span id="userCount">(<strong><?php echo count($users); ?></strong> Personnes)</span></p>
    </div>
    <div class="card">
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un utilisateur...">
            <select class="filter-select" id="filter-type">
                <option value="">Tous les types</option>
                <option value="Agent">Agent</option>
                <option value="Support">Support</option>
                <option value="Administrateur">Administrateur</option>
            </select>
            <select class="filter-select" id="filter-service">
                <option value="">Tous les services</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= htmlspecialchars($service['nom']) ?>"><?= htmlspecialchars($service['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" id="filter-status">
                <option value="">Tous les statuts</option>
                <option value="Actif">Actif</option>
                <option value="Suspendu">Suspendu</option>
            </select>
            <button class="btn btn-outline" id="sortDateBtn" data-sort="desc">
                <i class="fas fa-sort-amount-down"></i>
                Date (récent)
            </button>
        </div>
        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Type</th>
                        <th>Service</th>
                        <th>Date de création</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- Rempli par JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="pagination" id="paginationContainer">
            <!-- Pagination gérée par JS -->
        </div>
    </div>
</div>

<!-- Modal de modification -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal">
        <button class="close-btn" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-user-edit"></i>
                Modifier l'utilisateur
            </h2>
            <p class="modal-subtitle">Modifiez les informations de l'utilisateur ci-dessous</p>
        </div>
        <div class="modal-body">
            <div class="user-preview">
                <img id="userAvatar" src="" alt="Avatar utilisateur">
                <div class="user-preview-info">
                    <h3 id="userDisplayName"></h3>
                    <p id="userDisplayEmail"></p>
                    <div class="readonly-notice">
                        <i class="fas fa-lock"></i>
                        Email et photo générés automatiquement
                    </div>
                </div>
            </div>
            <form id="editUserForm" method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" id="editUserId">
                <input type="hidden" name="user_role" id="editUserRole">
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label class="form-label required" for="userPrenom">Prénom</label>
                        <input type="text" id="userPrenom" name="prenom" class="form-input" required>
                        <div class="validation-error" id="prenomError" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            Le prénom est requis
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="userNom">Nom</label>
                        <input type="text" id="userNom" name="nom" class="form-input" required>
                        <div class="validation-error" id="nomError" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            Le nom est requis
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required" for="userEmail">Adresse email</label>
                    <input type="email" id="userEmail" name="email" class="form-input" required>
                    <div class="form-note warning" id="emailNote" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="emailMessage"></span>
                    </div>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label class="form-label" for="userType">Type d'utilisateur</label>
                        <input type="text" id="userType" class="form-input" disabled>
                        <div class="form-note warning">
                            <i class="fas fa-info-circle"></i>
                            Le type d'utilisateur ne peut pas être modifié
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="userService">Service</label>
                        <select id="userService" name="service_id" class="form-select" required>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Section Changement de mot de passe -->
                <div class="password-section">
                    <h3 class="section-title">
                        <i class="fas fa-key"></i>
                        Changer le mot de passe
                    </h3>
                    <p class="section-subtitle">Laissez vide si vous ne souhaitez pas modifier le mot de passe</p>
                    
                    <div class="form-group">
                        <label class="form-label" for="userPassword">Nouveau mot de passe <span>(au moins 6 caractères)</span></label>
                        <input type="password" id="userPassword" name="password" class="form-input" placeholder="Saisir le nouveau mot de passe">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="userPasswordConfirm">Confirmer le mot de passe</label>
                        <input type="password" id="userPasswordConfirm" name="password_confirm" class="form-input" placeholder="Confirmer le nouveau mot de passe">
                        <div class="form-note" id="passwordMatchNote" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            Les mots de passe correspondent
                        </div>
                        <div class="form-note error" id="passwordMismatchNote" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            Les mots de passe ne correspondent pas
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="button" class="btn btn-primary" onclick="saveChanges()">
                <i class="fas fa-save"></i>
                Enregistrer
            </button>
        </div>
    </div>
</div>



<script>
    // Données PHP injectées en JS
let allUsers = <?php echo json_encode($users); ?>;
let services = <?php echo json_encode($services); ?>;
let filteredUsers = [...allUsers];
let currentPage = 1;
const usersPerPage = 6;
let currentSort = 'desc';
let currentUserId = null;

// Configuration des backgrounds pour avatars
const avatarBackgrounds = {
    'Agent': '3b82f6',
    'Support': 'f59e0b',
    'Administrateur': 'ef4444'
};

// Pour gérer la confirmation et la soumission des formulaires
let pendingForm = null;

function generateAvatar(prenom, nom, type) {
    const background = avatarBackgrounds[type] || '64748b';
    return `https://ui-avatars.com/api/?name=${prenom}+${nom}&background=${background}&color=fff`;
}

function processUsers() {
    allUsers = allUsers.map(user => ({
        ...user,
        fullName: `${user.prenom} ${user.nom}`,
        avatar: generateAvatar(user.prenom, user.nom, user.type)
    }));
    filteredUsers = [...allUsers];
}

document.addEventListener('DOMContentLoaded', () => {
    processUsers();
    applyFilters();
    setupPasswordValidation();
    setupFormValidation();
    setupEmailValidation(); // ← Nouvelle fonction pour la validation email
});

// =============================
// VALIDATION EMAIL
// =============================

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function checkEmailAvailability(email, currentEmail) {
    if (email === currentEmail) {
        return { available: true, message: 'Email actuel' };
    }
    
    // Vérification côté client (la vraie vérification se fera côté serveur)
    const allEmails = allUsers.map(user => user.email.toLowerCase());
    if (allEmails.includes(email.toLowerCase())) {
        return { available: false, message: 'Cet email est déjà utilisé' };
    }
    
    return { available: true, message: 'Email disponible' };
}

function setupEmailValidation() {
    const emailInput = document.getElementById('userEmail');
    const emailNote = document.getElementById('emailNote');
    const emailMessage = document.getElementById('emailMessage');
    let currentUserEmail = '';
    
    emailInput.addEventListener('blur', function() {
        const email = emailInput.value.trim();
        const originalEmail = document.getElementById('userDisplayEmail').textContent;
        
        if (!email) {
            emailInput.classList.add('error');
            emailNote.style.display = 'block';
            emailMessage.textContent = 'L\'email est requis';
            emailNote.className = 'form-note error';
            return;
        }
        
        if (!validateEmail(email)) {
            emailInput.classList.add('error');
            emailNote.style.display = 'block';
            emailMessage.textContent = 'Format d\'email invalide';
            emailNote.className = 'form-note error';
            return;
        }
        
        const checkResult = checkEmailAvailability(email, originalEmail);
        if (!checkResult.available) {
            emailInput.classList.add('error');
            emailNote.style.display = 'block';
            emailMessage.textContent = checkResult.message;
            emailNote.className = 'form-note error';
        } else {
            emailInput.classList.remove('error');
            emailNote.style.display = 'block';
            emailMessage.textContent = checkResult.message;
            emailNote.className = 'form-note success';
        }
    });
    
    emailInput.addEventListener('input', function() {
        emailInput.classList.remove('error');
        emailNote.style.display = 'none';
    });
}

// =============================
// VALIDATION FORMULAIRE EXISTANTE
// =============================

function setupFormValidation() {
    const prenomInput = document.getElementById('userPrenom');
    const nomInput = document.getElementById('userNom');
    
    prenomInput.addEventListener('blur', () => validateField(prenomInput, 'prenomError', 'Le prénom est requis'));
    nomInput.addEventListener('blur', () => validateField(nomInput, 'nomError', 'Le nom est requis'));
    
    prenomInput.addEventListener('input', () => clearFieldError(prenomInput, 'prenomError'));
    nomInput.addEventListener('input', () => clearFieldError(nomInput, 'nomError'));
}

function validateField(input, errorId, errorMessage) {
    const errorDiv = document.getElementById(errorId);
    if (!input.value.trim()) {
        input.classList.add('error');
        errorDiv.style.display = 'block';
        errorDiv.textContent = errorMessage;
        return false;
    }
    return true;
}

function clearFieldError(input, errorId) {
    const errorDiv = document.getElementById(errorId);
    input.classList.remove('error');
    errorDiv.style.display = 'none';
}

// =============================
// VALIDATION MOT DE PASSE EXISTANTE
// =============================

function setupPasswordValidation() {
    const password = document.getElementById('userPassword');
    const confirmPassword = document.getElementById('userPasswordConfirm');
    const matchNote = document.getElementById('passwordMatchNote');
    const mismatchNote = document.getElementById('passwordMismatchNote');

    function validatePasswords() {
        const pass1 = password.value;
        const pass2 = confirmPassword.value;

        if (!pass1 && !pass2) {
            matchNote.style.display = 'none';
            mismatchNote.style.display = 'none';
            return true;
        }

        if (!pass1 && pass2) {
            matchNote.style.display = 'none';
            mismatchNote.style.display = 'block';
            mismatchNote.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez saisir le mot de passe d\'abord';
            return false;
        }

        if (pass1 && !pass2) {
            matchNote.style.display = 'none';
            mismatchNote.style.display = 'none';
            return true;
        }

        if (pass1 && pass2) {
            if (pass1.length < 6) {
                matchNote.style.display = 'none';
                mismatchNote.style.display = 'block';
                mismatchNote.innerHTML = '<i class="fas fa-exclamation-circle"></i> Le mot de passe doit contenir au moins 6 caractères';
                return false;
            }
            
            if (pass1 === pass2) {
                matchNote.style.display = 'block';
                mismatchNote.style.display = 'none';
                return true;
            } else {
                matchNote.style.display = 'none';
                mismatchNote.style.display = 'block';
                mismatchNote.innerHTML = '<i class="fas fa-exclamation-circle"></i> Les mots de passe ne correspondent pas';
                return false;
            }
        }

        return true;
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);
}

// =============================
// FILTRES ET AFFICHAGE EXISTANTS
// =============================

function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const type = document.getElementById('filter-type').value;
    const service = document.getElementById('filter-service').value;
    const status = document.getElementById('filter-status').value;

    filteredUsers = allUsers.filter(user => {
        const fullName = `${user.prenom} ${user.nom}`.toLowerCase();
        return (
            (fullName.includes(search) || user.email.toLowerCase().includes(search)) &&
            (!type || user.type === type) &&
            (!service || user.service === service) &&
            (!status || user.status === status)
        );
    });

    sortUsers(currentSort);
    currentPage = 1;
    renderUsers();
    renderPagination();
}

function sortUsers(order) {
    filteredUsers.sort((a, b) => {
        const dateA = new Date(a.created);
        const dateB = new Date(b.created);
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });
    currentSort = order;
    const btn = document.getElementById('sortDateBtn');
    if (order === 'asc') {
        btn.innerHTML = '<i class="fas fa-sort-amount-up"></i> Date (ancien)';
    } else {
        btn.innerHTML = '<i class="fas fa-sort-amount-down"></i> Date (récent)';
    }
}

function renderUsers() {
    const tbody = document.getElementById('userTableBody');
    const start = (currentPage - 1) * usersPerPage;
    const pageUsers = filteredUsers.slice(start, start + usersPerPage);

    if (pageUsers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>Aucun utilisateur trouvé.</p>
                </td>
            </tr>
        `;
        document.getElementById('userCount').innerHTML = '(<strong>0</strong> Personnes)';
        return;
    }

    tbody.innerHTML = pageUsers.map(user => {
        const badgeClass = 'badge badge-' + user.type.toLowerCase();
        const statusClass = user.status === 'Actif' ? 'status-good' : 'status-danger';
        return `
            <tr>
                <td>
                    <div class="user-info">
                        <img src="${user.avatar}" alt="Avatar" class="user-avatar">
                        <div>
                            <div class="user-name">${user.fullName}</div>
                            <div class="user-email">${user.email}</div>
                        </div>
                    </div>
                </td>
                <td><span class="${badgeClass}">${user.type}</span></td>
                <td>${user.service}</td>
                <td>${new Date(user.created).toLocaleDateString('fr-FR')}</td>
                <td><span class="${statusClass}">${user.status}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="icon-btn edit" title="Modifier" data-id="${user.id}" data-role="${user.type}" data-service="${user.service}">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <!-- Formulaire pour Supprimer avec Card de Confirmation -->
                        <form method="POST" style="display:inline;" data-form-type="delete" data-user-id="${user.id}" data-user-name="${user.prenom}" data-user-lastname="${user.nom}">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="${user.id}">
                            <input type="hidden" name="user_role" value="${user.type}">
                            <button type="button" class="icon-btn delete confirm-btn" title="Supprimer" data-action="supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        
                        <!-- Formulaire pour Bloquer/Débloquer avec Card de Confirmation -->
                        <form method="POST" style="display:inline;" data-form-type="block" data-user-id="${user.id}" data-user-name="${user.prenom}" data-user-lastname="${user.nom}" data-user-status="${user.status}">
                            <input type="hidden" name="action" value="block">
                            <input type="hidden" name="user_id" value="${user.id}">
                            <input type="hidden" name="user_role" value="${user.type}">
                            <button type="button" class="icon-btn ${user.status === 'Actif' ? 'block' : 'debloquer'} confirm-btn" title="${user.status === 'Actif' ? 'Bloquer' : 'Débloquer'}" data-action="${user.status === 'Actif' ? 'bloquer' : 'débloquer'}">
                                <i class="fas ${user.status === 'Actif' ? 'fa-ban' : 'fa-unlock'}"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('userCount').innerHTML = `(<strong>${filteredUsers.length}</strong> Personnes)`;
    attachEventListeners();
    attachConfirmationListeners();
}

// =============================
// CONFIRMATION EXISTANTE
// =============================

function attachConfirmationListeners() {
    document.querySelectorAll('.confirm-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const form = this.closest('form');
            const action = this.getAttribute('data-action');
            const userId = form.getAttribute('data-user-id');
            const userName = form.getAttribute('data-user-name');
            const userLastname = form.getAttribute('data-user-lastname');

            const user = {
                id: userId,
                prenom: userName,
                nom: userLastname
            };

            pendingForm = form;

            // Prépare le détail utilisateur pour la confirmation
            const details = `<div class="user-avatar" id="confirmUserAvatar">${user.prenom.charAt(0)}</div>
                            <div class="user-name" id="confirmUserName">${user.prenom} ${user.nom}</div>`;

            // Définition du texte et style selon l'action
            let actionText = "";
            let btnText = "Confirmer";
            let cancelText = "Annuler";
            if (action === 'supprimer') {
                actionText = "supprimer";
                btnText = "Supprimer";
                cancelText = "Fermer";
            } else if (action === 'bloquer') {
                actionText = "bloquer";
                btnText = "Bloquer";
                cancelText = "Annuler";
            } else if (action === 'débloquer') {
                actionText = "débloquer";
                btnText = "Débloquer";
                cancelText = "Annuler";
            }

            confirmation(
                `Êtes-vous sûr de vouloir ${actionText} cet utilisateur ?`,
                btnText,
                details,
                function() { // Accept (Confirmer)
                    if (pendingForm) pendingForm.submit();
                },
                function() { // Cancel (Annuler)
                    pendingForm = null;
                },
                cancelText // <-- texte du bouton Annuler/Fermer/Retour...
            );
        });
    });
}

function renderPagination() {
    const container = document.getElementById('paginationContainer');
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    if (currentPage > 1) {
        html += `<button class="pagination-btn" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
    } else {
        html += `<span class="pagination-btn" style="opacity:0.5;cursor:not-allowed;"><i class="fas fa-chevron-left"></i></span>`;
    }

    const start = Math.max(1, currentPage - 1);
    const end = Math.min(totalPages, start + 2);
    for (let i = start; i <= end; i++) {
        html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
    }

    if (currentPage < totalPages) {
        html += `<button class="pagination-btn" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
    } else {
        html += `<span class="pagination-btn" style="opacity:0.5;cursor:not-allowed;"><i class="fas fa-chevron-right"></i></span>`;
    }

    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    renderUsers();
    renderPagination();
}

// =============================
// MODAL ET GESTION UTILISATEUR
// =============================

function attachEventListeners() {
    document.querySelectorAll('.icon-btn.edit').forEach(btn => {
        btn.onclick = () => {
            const id = btn.getAttribute('data-id');
            const user = allUsers.find(u => u.id == id);
            currentUserId = id;
            loadUserData(user);
            openModal();
        };
    });
}

function openModal() {
    document.getElementById('editUserModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('editUserModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('userPassword').value = '';
    document.getElementById('userPasswordConfirm').value = '';
    document.getElementById('passwordMatchNote').style.display = 'none';
    document.getElementById('passwordMismatchNote').style.display = 'none';
    clearFieldError(document.getElementById('userPrenom'), 'prenomError');
    clearFieldError(document.getElementById('userNom'), 'nomError');
    
    // Réinitialiser aussi l'email
    const emailNote = document.getElementById('emailNote');
    emailNote.style.display = 'none';
    document.getElementById('userEmail').classList.remove('error');
}

function loadUserData(user) {
    document.getElementById('userDisplayName').textContent = user.fullName;
    document.getElementById('userDisplayEmail').textContent = user.email;
    document.getElementById('userAvatar').src = user.avatar;
    document.getElementById('userPrenom').value = user.prenom;
    document.getElementById('userNom').value = user.nom;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userType').value = user.type;
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserRole').value = user.type;
    
    const serviceSelect = document.getElementById('userService');
    const currentService = services.find(s => s.nom === user.service);
    if (currentService) {
        serviceSelect.value = currentService.id;
    }
    
    const prenomInput = document.getElementById('userPrenom');
    const nomInput = document.getElementById('userNom');
    
    function updateUserPreview() {
        const prenom = prenomInput.value.trim();
        const nom = nomInput.value.trim();
        const type = document.getElementById('userType').value;
        
        if (prenom && nom) {
            const fullName = `${prenom} ${nom}`;
            const avatar = generateAvatar(prenom, nom, type);
            document.getElementById('userDisplayName').textContent = fullName;
            document.getElementById('userAvatar').src = avatar;
        }
    }
    
    prenomInput.addEventListener('input', updateUserPreview);
    nomInput.addEventListener('input', updateUserPreview);
}

// =============================
// SAUVEGARDE MODIFIÉE POUR EMAIL
// =============================

// =============================
// SAUVEGARDE MODIFIÉE POUR EMAIL
// =============================

function saveChanges() {
    const prenom = document.getElementById('userPrenom').value.trim();
    const nom = document.getElementById('userNom').value.trim();
    const email = document.getElementById('userEmail').value.trim();
    const password = document.getElementById('userPassword').value;
    const confirmPassword = document.getElementById('userPasswordConfirm').value;
    
    let hasErrors = false;
    
    // Validation des champs requis
    if (!validateField(document.getElementById('userPrenom'), 'prenomError', 'Le prénom est requis')) {
        hasErrors = true;
    }
    if (!validateField(document.getElementById('userNom'), 'nomError', 'Le nom est requis')) {
        hasErrors = true;
    }
    
    // Validation email
    if (!email) {
        document.getElementById('userEmail').classList.add('error');
        document.getElementById('emailNote').style.display = 'block';
        document.getElementById('emailMessage').textContent = 'L\'email est requis';
        document.getElementById('emailNote').className = 'form-note error';
        hasErrors = true;
    } else if (!validateEmail(email)) {
        document.getElementById('userEmail').classList.add('error');
        document.getElementById('emailNote').style.display = 'block';
        document.getElementById('emailMessage').textContent = 'Format d\'email invalide';
        document.getElementById('emailNote').className = 'form-note error';
        hasErrors = true;
    }
    
    // Validation mot de passe
    const mismatchNote = document.getElementById('passwordMismatchNote');
    const matchNote = document.getElementById('passwordMatchNote');
    
    if (password || confirmPassword) {
        if (password !== confirmPassword) {
            mismatchNote.style.display = 'block';
            matchNote.style.display = 'none';
            return;
        }
        if (password.length < 6) {
            mismatchNote.style.display = 'block';
            mismatchNote.innerHTML = '<i class="fas fa-exclamation-circle"></i> Le mot de passe doit contenir au moins 6 caractères';
            matchNote.style.display = 'none';
            return;
        }
        // Si tout est bon, afficher le message de correspondance
        mismatchNote.style.display = 'none';
        matchNote.style.display = 'block';
    } else {
        // Si aucun mot de passe n'est saisi, cacher les messages
        mismatchNote.style.display = 'none';
        matchNote.style.display = 'none';
    }
    
    if (hasErrors) {
        return;
    }
    
    document.getElementById('editUserForm').submit();
}

// =============================
// ÉVÉNEMENTS EXISTANTS
// =============================

document.getElementById('searchInput').addEventListener('input', () => {
    applyFilters();
    if (document.getElementById('searchInput').value.trim()) {
        notify.info('Recherche', `${filteredUsers.length} utilisateur(s) trouvé(s)`, 1500);
    }
});

document.getElementById('filter-type').addEventListener('change', applyFilters);
document.getElementById('filter-service').addEventListener('change', applyFilters);
document.getElementById('filter-status').addEventListener('change', applyFilters);

document.getElementById('sortDateBtn').addEventListener('click', () => {
    currentSort = currentSort === 'desc' ? 'asc' : 'desc';
    sortUsers(currentSort);
    renderUsers();
    renderPagination();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (document.getElementById('editUserModal').classList.contains('active')) {
            closeModal();
        }
        if (document.getElementById('confirmationOverlay').classList.contains('active')) {
            document.getElementById('confirmationOverlay').classList.remove('active');
            document.body.style.overflow = 'auto';
            pendingForm = null;
        }
    }
    if (e.ctrlKey && e.key === 's' && document.getElementById('editUserModal').classList.contains('active')) {
        e.preventDefault();
        saveChanges();
    }
});

window.addEventListener('online', () => {
    notify.success('Connexion rétablie', 'Vous êtes de nouveau en ligne');
});

window.addEventListener('offline', () => {
    notify.warning('Connexion perdue', 'Vous travaillez actuellement hors ligne');
});

let hasUnsavedChanges = false;

['userPrenom', 'userNom', 'userEmail', 'userService', 'userPassword', 'userPasswordConfirm'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', () => {
            hasUnsavedChanges = true;
        });
    }
});

const originalSaveChanges = saveChanges;
saveChanges = function() {
    hasUnsavedChanges = false;
    originalSaveChanges();
};
</script>