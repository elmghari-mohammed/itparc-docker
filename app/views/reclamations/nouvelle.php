<?php
// /app/views/reclamations/nouvelle.php

// Récupérer les données passées par le contrôleur
$materiels = $data['materiels'] ?? [];
$error_message = $data['error_message'] ?? '';
$success_message = $data['success_message'] ?? '';
$form_data = $data['form_data'] ?? [];
?>

<style>
    /* Variables CSS identiques à demande */
    :root {
        --primary: #3b82f6;
        --primary-dark: #2563eb;
        --secondary: #64748b;
        --dark: #1e293b;
        --dark-light: #334155;
        --light: #f8fafc;
        --light-secondary: #f1f5f9;
        --white: #ffffff;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --border: #e2e8f0;
        --border-radius: 8px;
        --transition: all 0.3s ease;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Page nouvelle réclamation */
    .page-nouvelle-reclamation {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }

    /* Dashboard header */
    .dashboard-header {
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .subtitle {
        color: var(--secondary);
        font-size: 16px;
        max-width: 600px;
    }

    /* Form container */
    .form-container {
        background: var(--white);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        padding: 25px;
        animation: fadeInUp 0.6s ease;
    }

    /* Form grid */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    /* Form groups */
    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark);
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .required {
        color: var(--danger);
    }

    /* Form inputs */
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border);
        border-radius: var(--border-radius);
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        transition: var(--transition);
        background-color: var(--white);
    }

    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-select:disabled {
        background-color: var(--light-secondary);
        color: var(--secondary);
        cursor: not-allowed;
    }

    /* Select styling */
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
    }

    /* Form actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        border: none;
        border-radius: var(--border-radius);
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-primary {
        background-color: var(--primary);
        color: var(--white);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    .btn-secondary {
        background-color: var(--white);
        color: var(--secondary);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background-color: var(--light-secondary);
        border-color: var(--secondary);
    }

    /* Field error styling */
    .field-error {
        color: var(--danger);
        font-size: 0.85rem;
        margin-top: 4px;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .dashboard-title {
            font-size: 24px;
        }
        
        .form-container {
            padding: 20px;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Message de confirmation */
    #confirmation {
        display: none;
        text-align: center;
        padding: 20px;
        background-color: var(--success);
        color: white;
        border-radius: var(--border-radius);
        margin-top: 20px;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<div class="dashboard-header">
    <h1 class="dashboard-title">
        <i class="fas fa-exclamation-circle"></i>
        Nouvelle Réclamation
    </h1>
    <p class="subtitle">Remplissez le formulaire ci-dessous pour soumettre une réclamation</p>
</div>

<div class="form-container">
    <form id="reclamationForm" action="<?= RouterHelper::getFullUrl('reclamations/nouvelle') ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="materiel_id">
                    <i class="fas fa-laptop"></i>
                    Matériel concerné
                    <span class="required">*</span>
                </label>
                <select id="materiel_id" name="materiel_id" class="form-select" required>
                    <option value="">Sélectionnez un matériel</option>
                    <?php foreach ($materiels as $materiel): ?>
                    <option value="<?= $materiel['id'] ?>" 
                        <?= (isset($form_data['materiel_id']) && $form_data['materiel_id'] == $materiel['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($materiel['nom_complet']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group full-width">
                <label class="form-label" for="motif">
                    <i class="fas fa-comment"></i>
                    Description du problème
                    <span class="required">*</span>
                </label>
                <textarea 
                    id="motif" 
                    name="motif" 
                    class="form-textarea" 
                    placeholder="Décrivez le problème que vous rencontrez avec votre matériel..."
                    rows="4"
                    required
                ><?= htmlspecialchars($form_data['motif'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancelButton">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
                Soumettre la Réclamation
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reclamationForm');
    const cancelButton = document.getElementById('cancelButton');
    
    // Gestion de l'annulation
    cancelButton.addEventListener('click', function() {
        confirmation(
            'Êtes-vous sûr de vouloir annuler? Toutes les données saisies seront perdues.',
            'Oui, annuler',
            null,
            function() {
                window.location.href = '<?= RouterHelper::getFullUrl('dashboard') ?>';
            },
            null,
            'Continuer'
        );
    });
    
    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation basique
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = 'var(--danger)';
                
                // Ajouter un message d'erreur
                if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('field-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error';
                    errorDiv.textContent = 'Ce champ est obligatoire';
                    field.parentNode.appendChild(errorDiv);
                }
            } else {
                field.style.borderColor = '';
                // Supprimer les messages d'erreur
                if (field.nextElementSibling && field.nextElementSibling.classList.contains('field-error')) {
                    field.nextElementSibling.remove();
                }
            }
        });
        
        if (!isValid) {
            notify.error('Erreur', 'Veuillez remplir tous les champs obligatoires.', 5000);
            return;
        }
        
        // Confirmation avant soumission
        confirmation(
            'Êtes-vous sûr de vouloir soumettre cette réclamation?',
            'Oui, soumettre',
            null,
            function() {
                form.submit();
            },
            null,
            'Annuler'
        );
    });
    
    // Afficher automatiquement les messages s'ils existent
    <?php if (!empty($success_message)): ?>
    notify.success('Succès', '<?= addslashes($success_message) ?>', 5000);
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
    notify.error('Erreur', '<?= addslashes($error_message) ?>', 0);
    <?php endif; ?>
});
</script>