<?php
$typesPersonnels = $data['typesPersonnels'] ?? [];
$typesNonPersonnels = $data['typesNonPersonnels'] ?? [];
$roles = $data['roles'] ?? [];
$services = $data['services'] ?? [];
$salles = $data['salles'] ?? [];
$error_message = $data['error_message'] ?? '';
$success_message = $data['success_message'] ?? '';
$form_data = $data['form_data'] ?? [];
?>

<div class="dashboard-header">
    <h1 class="dashboard-title">
        <i class="fas fa-plus-circle"></i>
        Ajouter Un Équipement
    </h1>
    <p class="subtitle">Enregistrez un nouvel équipement et assignez-le selon son type</p>
</div>

<!-- Messages d'alerte -->
<?php if ($success_message): ?>
<div id="successMessage" class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span><?= htmlspecialchars($success_message) ?></span>
</div>
<?php endif; ?>

<?php if ($error_message): ?>
<div id="errorMessage" class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <span id="errorText"><?= htmlspecialchars($error_message) ?></span>
</div>
<?php endif; ?>

<div class="form-container">
    <form id="equipmentForm" action="<?= RouterHelper::getFullUrl('equipements/ajouter') ?>" method="POST">
        <div class="form-grid">
            <!-- Section Information Équipement -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-desktop"></i>
                    Informations de l'Équipement
                </h3>
                
                <div class="form-group">
                    <label class="form-label" for="serialNumber">
                        <i class="fas fa-barcode"></i>
                        Numéro de Série
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="serialNumber" 
                        name="serialNumber" 
                        class="form-input" 
                        placeholder="Ex: SN123456789"
                        value="<?= htmlspecialchars($form_data['serialNumber'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="marque">
                        <i class="fas fa-tag"></i>
                        Marque
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="marque" 
                        name="marque" 
                        class="form-input" 
                        placeholder="Ex: Dell, HP, Canon..."
                        value="<?= htmlspecialchars($form_data['marque'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="modele">
                        <i class="fas fa-cog"></i>
                        Modèle
                        <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="modele" 
                        name="modele" 
                        class="form-input" 
                        placeholder="Ex: Latitude 5520, LaserJet Pro..."
                        value="<?= htmlspecialchars($form_data['modele'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">
                        <i class="fas fa-desktop"></i>
                        Type d'Équipement
                        <span class="required">*</span>
                    </label>
                    <select id="type" name="type" class="form-select" required onchange="toggleAssignmentFields()">
                        <option value="">Sélectionnez un type</option>
                        
                        <!-- Types personnels -->
                        <optgroup label="Équipements Personnels">
                            <?php foreach ($typesPersonnels as $type): ?>
                            <option value="<?= $type['id'] ?>" 
                                data-personnel="1"
                                <?= (isset($form_data['type']) && $form_data['type'] == $type['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </optgroup>
                        
                        <!-- Types non personnels -->
                        <optgroup label="Équipements Partagés">
                            <?php foreach ($typesNonPersonnels as $type): ?>
                            <option value="<?= $type['id'] ?>" 
                                data-personnel="0"
                                <?= (isset($form_data['type']) && $form_data['type'] == $type['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- Section Assignation Utilisateur (visible seulement pour types personnels) -->
            <div id="userAssignmentSection" class="form-section" style="display: none;">
                <h3 class="section-title">
                    <i class="fas fa-user-tag"></i>
                    Assignation à l'Utilisateur
                </h3>

                <div class="form-group">
                    <label class="form-label" for="user_role">
                        <i class="fas fa-user-tag"></i>
                        Rôle de l'Utilisateur
                        <span class="required">*</span>
                    </label>
                    <select id="user_role" name="user_role" class="form-select">
                        <option value="">Sélectionnez un rôle</option>
                        <?php foreach ($roles as $roleValue => $roleLabel): ?>
                        <option value="<?= $roleValue ?>" 
                            <?= (isset($form_data['user_role']) && $form_data['user_role'] == $roleValue) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($roleLabel) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="user_id">
                        <i class="fas fa-user"></i>
                        Utilisateur
                        <span class="required">*</span>
                    </label>
                    <select id="user_id" name="user_id" class="form-select">
                        <option value="">Sélectionnez d'abord un rôle</option>
                    </select>
                </div>

                <!-- Affichage automatique des informations utilisateur -->
                <div id="userInfo" class="user-info-card" style="display: none;">
                    <h4>Informations de l'utilisateur sélectionné:</h4>
                    <div class="user-details">
                        <div class="user-detail-item">
                            <i class="fas fa-id-card"></i>
                            <strong>Matricule:</strong> <span id="userMatricule">-</span>
                        </div>
                        <div class="user-detail-item">
                            <i class="fas fa-user"></i>
                            <strong>Nom complet:</strong> <span id="userName">-</span>
                        </div>
                        <div class="user-detail-item">
                            <i class="fas fa-envelope"></i>
                            <strong>Email:</strong> <span id="userEmail">-</span>
                        </div>
                        <div class="user-detail-item">
                            <i class="fas fa-computer"></i>
                            <strong>Service:</strong> <span id="userService">-</span>
                        </div>
                        <div class="user-detail-item">
                            <i class="fas fa-door-open"></i>
                            <strong>Salle:</strong> <span id="userSalle">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Emplacement (visible seulement pour types non personnels) -->
            <div id="locationAssignmentSection" class="form-section" style="display: none;">
                <h3 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Emplacement de l'Équipement
                </h3>
                
                <div class="form-group">
                    <label class="form-label" for="service_id">
                        <i class="fas fa-building"></i>
                        Service
                        <span class="required">*</span>
                    </label>
                    <select id="service_id" name="service_id" class="form-select">
                        <option value="">Sélectionnez un service</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>" 
                            <?= (isset($form_data['service_id']) && $form_data['service_id'] == $service['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="salle_id">
                        <i class="fas fa-door-open"></i>
                        Salle
                        <span class="required">*</span>
                    </label>
                    <select id="salle_id" name="salle_id" class="form-select">
                        <option value="">Sélectionnez une salle</option>
                        <?php foreach ($salles as $salle): ?>
                        <option value="<?= $salle['id'] ?>" 
                            <?= (isset($form_data['salle_id']) && $form_data['salle_id'] == $salle['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($salle['nom']) ?> (<?= htmlspecialchars($salle['numero']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Section Dates et Détails -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Dates et Détails
                </h3>

                <div class="form-group">
                    <label class="form-label" for="garantieFin">
                        <i class="fas fa-calendar-alt"></i>
                        Fin de Garantie
                    </label>
                    <div class="date-input-group">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input 
                            type="date" 
                            id="garantieFin" 
                            name="garantieFin" 
                            class="form-input"
                            value="<?= htmlspecialchars($form_data['garantieFin'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="serviceFin">
                        <i class="fas fa-calendar-times"></i>
                        Fin de Service
                    </label>
                    <div class="date-input-group">
                        <i class="fas fa-calendar-alt date-icon"></i>
                        <input 
                            type="date" 
                            id="serviceFin" 
                            name="serviceFin" 
                            class="form-input"
                            value="<?= htmlspecialchars($form_data['serviceFin'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="details">
                        <i class="fas fa-info-circle"></i>
                        Détails supplémentaires
                    </label>
                    <textarea 
                        id="details" 
                        name="details" 
                        class="form-textarea" 
                        placeholder="Ajoutez des informations supplémentaires sur cet équipement (état, configuration, localisation, etc.)"
                    ><?= htmlspecialchars($form_data['details'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelButton">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Enregistrer l'Équipement
            </button>
        </div>
    </form>
</div>

<style>
.form-section {
    background: var(--card-bg);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid var(--border-color);
}

.section-title {
    color: var(--primary);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
    font-size: 1.2rem;
}

.user-info-card {
    background: var(--success-light);
    border: 1px solid var(--success);
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.user-info-card h4 {
    margin: 0 0 10px 0;
    color: var(--success-dark);
    font-size: 1rem;
}

.user-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.user-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.user-detail-item i {
    color: var(--primary);
    width: 16px;
}

@media (max-width: 768px) {
    .user-details {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Fonction pour basculer entre les sections d'assignation
function toggleAssignmentFields() {
    const typeSelect = document.getElementById('type');
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    const isPersonnel = selectedOption.getAttribute('data-personnel') === '1';
    
    const userSection = document.getElementById('userAssignmentSection');
    const locationSection = document.getElementById('locationAssignmentSection');
    
    if (isPersonnel) {
        userSection.style.display = 'block';
        locationSection.style.display = 'none';
        // Rendre obligatoire les champs utilisateur
        document.getElementById('user_role').required = true;
        document.getElementById('user_id').required = true;
        // Rendre optionnels les champs emplacement
        document.getElementById('service_id').required = false;
        document.getElementById('salle_id').required = false;
    } else {
        userSection.style.display = 'none';
        locationSection.style.display = 'block';
        // Rendre optionnels les champs utilisateur
        document.getElementById('user_role').required = false;
        document.getElementById('user_id').required = false;
        // Rendre obligatoire les champs emplacement
        document.getElementById('service_id').required = true;
        document.getElementById('salle_id').required = true;
    }
}

// Fonction pour charger les utilisateurs par rôle
function loadUsersByRole(role) {
    const userSelect = document.getElementById('user_id');
    const userInfo = document.getElementById('userInfo');
    
    if (!role) {
        userSelect.innerHTML = '<option value="">Sélectionnez d\'abord un rôle</option>';
        userInfo.style.display = 'none';
        return;
    }
    
    // Afficher un indicateur de chargement
    userSelect.innerHTML = '<option value="">Chargement...</option>';
    userSelect.disabled = true;
    userInfo.style.display = 'none';
    
    // Requête AJAX pour récupérer les utilisateurs
    fetch(`<?= RouterHelper::getFullUrl('equipements/ajouter') ?>?ajax=users&role=${role}`)
        .then(response => response.json())
        .then(users => {
            userSelect.innerHTML = '<option value="">Sélectionnez un utilisateur</option>';
            
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.id} - ${user.nom} ${user.prenom}`;
                option.setAttribute('data-email', user.email);
                option.setAttribute('data-service', user.service_nom || 'Non spécifié');
                option.setAttribute('data-salle', user.salle_nom ? `${user.salle_nom} (${user.salle_numero})` : 'Non spécifié');
                userSelect.appendChild(option);
            });
            
            userSelect.disabled = false;
            
            // Pré-sélectionner si une valeur existe
            const savedUserId = '<?= $form_data['user_id'] ?? '' ?>';
            if (savedUserId) {
                userSelect.value = savedUserId;
                updateUserInfo(savedUserId, role);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            userSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            userSelect.disabled = false;
        });
}

// Fonction pour mettre à jour les informations de l'utilisateur
function updateUserInfo(userId, userRole) {
    const userInfo = document.getElementById('userInfo');
    const selectedOption = document.querySelector(`#user_id option[value="${userId}"]`);
    
    if (selectedOption && userId) {
        document.getElementById('userMatricule').textContent = userId;
        document.getElementById('userName').textContent = selectedOption.textContent.replace(`${userId} - `, '');
        document.getElementById('userEmail').textContent = selectedOption.getAttribute('data-email');
        document.getElementById('userService').textContent = selectedOption.getAttribute('data-service');
        document.getElementById('userSalle').textContent = selectedOption.getAttribute('data-salle');
        userInfo.style.display = 'block';
    } else {
        userInfo.style.display = 'none';
    }
}

// Événement lorsque le rôle change
document.getElementById('user_role').addEventListener('change', function() {
    loadUsersByRole(this.value);
    document.getElementById('userInfo').style.display = 'none';
});

// Événement lorsque l'utilisateur change
document.getElementById('user_id').addEventListener('change', function() {
    const userRole = document.getElementById('user_role').value;
    updateUserInfo(this.value, userRole);
});

// Fonction pour afficher les messages
function showMessage(type, text, duration = 4000) {
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    if (successMessage) successMessage.style.display = 'none';
    if (errorMessage) errorMessage.style.display = 'none';
    
    if (type === 'success' && successMessage) {
        successMessage.querySelector('span').textContent = text;
        successMessage.style.display = 'flex';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        if (duration > 0) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, duration);
        }
    } else if (type === 'error' && errorMessage) {
        errorMessage.querySelector('#errorText').textContent = text;
        errorMessage.style.display = 'flex';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        if (duration > 0) {
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, duration);
        }
    }
}

// Fonction pour réinitialiser le formulaire
function resetForm() {
    document.getElementById('equipmentForm').reset();
    document.getElementById('userInfo').style.display = 'none';
    document.getElementById('userAssignmentSection').style.display = 'none';
    document.getElementById('locationAssignmentSection').style.display = 'none';
    document.querySelectorAll('.field-error').forEach(el => el.remove());
    document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
        input.style.borderColor = '';
    });
}

// Cancel button handler
document.getElementById('cancelButton').addEventListener('click', function() {
    confirmation(
        'Êtes-vous sûr de vouloir annuler ? Toutes les données saisies seront perdues.',
        'Oui, annuler',
        null,
        function() {
            resetForm();
        },
        null,
        'Continuer'
    );
});

// Validation en temps réel
const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
inputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.nextElementSibling && this.nextElementSibling.classList.contains('field-error')) {
            this.nextElementSibling.remove();
        }
        
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.style.borderColor = 'var(--danger)';
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.color = 'var(--danger)';
            errorDiv.style.fontSize = '0.85rem';
            errorDiv.style.marginTop = '4px';
            errorDiv.textContent = 'Ce champ est obligatoire';
            this.parentNode.appendChild(errorDiv);
        } else {
            this.style.borderColor = '';
        }
    });

    input.addEventListener('focus', function() {
        this.style.borderColor = 'var(--primary)';
        if (this.nextElementSibling && this.nextElementSibling.classList.contains('field-error')) {
            this.nextElementSibling.remove();
        }
    });
});

// Auto-grow textarea
const textarea = document.getElementById('details');
if (textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 200) + 'px';
    });
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const savedRole = '<?= $form_data['user_role'] ?? '' ?>';
    
    // Initialiser l'affichage des sections selon le type sélectionné
    toggleAssignmentFields();
    
    if (savedRole) {
        loadUsersByRole(savedRole);
    }
    
    const garantieFin = document.getElementById('garantieFin');
    if (garantieFin) {
        garantieFin.setAttribute('min', today);
    }
    
    // Afficher automatiquement les messages s'ils existent
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    if (successMessage && successMessage.textContent.trim() !== '') {
        showMessage('success', successMessage.querySelector('span').textContent, 5000);
    }
    
    if (errorMessage && errorMessage.querySelector('#errorText').textContent.trim() !== '') {
        showMessage('error', errorMessage.querySelector('#errorText').textContent, 0);
    }
    
    // Pré-sélectionner l'utilisateur si les données existent
    const savedUserId = '<?= isset($form_data['user_id']) ? $form_data['user_id'] : '' ?>';
    if (savedUserId && savedRole) {
        // Attendre que le select soit chargé
        setTimeout(() => {
            const userSelect = document.getElementById('user_id');
            if (userSelect) {
                userSelect.value = savedUserId;
                updateUserInfo(savedUserId, savedRole);
            }
        }, 500);
    }
});
</script>