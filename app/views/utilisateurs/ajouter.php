<div class="container fade-in">
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-user-plus"></i>
            Création d'Utilisateur
        </h1>
        <p class="subtitle">Ajouter un nouvel utilisateur à la plateforme ITParc avec toutes les informations nécessaires</p>
    </div>

    <div class="card">
        <form id="userForm" method="POST" action="">
            <!-- Messages de notification -->
            <?php if (!empty($success_message)): ?>
            <div class="notification success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
            <div class="notification error">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
            <?php endif; ?>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    Type d'Utilisateur
                </h2>
                
                <div class="form-group">
                    <label for="userType" class="form-label required">
                        <i class="fas fa-user-tag"></i>
                        Sélectionner le type d'utilisateur
                    </label>
                    <div class="input-wrapper">
                        <select id="userType" name="userType" class="form-select" required>
                            <option value="">Choisir un type d'utilisateur</option>
                            <option value="agent" <?php echo (isset($form_data['userType']) && $form_data['userType'] === 'agent') ? 'selected' : ''; ?>>Agent</option>
                            <option value="support" <?php echo (isset($form_data['userType']) && $form_data['userType'] === 'support') ? 'selected' : ''; ?>>Support</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-address-card"></i>
                    Informations Personnelles
                </h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom" class="form-label required">
                            Nom
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="nom" name="nom" class="form-input" required 
                                   placeholder="Entrez le nom de famille" 
                                   value="<?php echo isset($form_data['nom']) ? htmlspecialchars($form_data['nom']) : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prenom" class="form-label">
                            Prénom
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="prenom" name="prenom" class="form-input" 
                                   placeholder="Entrez le prénom"
                                   value="<?php echo isset($form_data['prenom']) ? htmlspecialchars($form_data['prenom']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label required">
                        Email professionnel
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-input" required 
                               placeholder="exemple@itparc.com"
                               value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group" id="numeroField">
                    <label for="numero" class="form-label">
                        Numéro de téléphone
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-phone"></i>
                        <input type="tel" id="numero" name="numero" class="form-input" 
                               placeholder="+212 6XX XXX XXX"
                               value="<?php echo isset($form_data['numero']) ? htmlspecialchars($form_data['numero']) : ''; ?>">
                    </div>
                </div>

                <!-- Champs spécifiques pour les agents -->
                <div id="agentFields" style="display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tache" class="form-label">
                                Tâche principale
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="tache" name="tache" class="form-input" 
                                       placeholder="Description de la tâche"
                                       value="<?php echo isset($form_data['tache']) ? htmlspecialchars($form_data['tache']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="post" class="form-label">
                                Poste
                            </label>
                            <div class="input-wrapper">
                                <input type="text" id="post" name="post" class="form-input" 
                                       placeholder="Titre du poste"
                                       value="<?php echo isset($form_data['post']) ? htmlspecialchars($form_data['post']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-key"></i>
                    Informations de Connexion
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label required">
                            <i class="fas fa-lock"></i>
                            Mot de passe
                        </label>
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-shield-alt"></i>
                            <input type="password" id="password" name="password" class="form-input" required 
                                   placeholder="Minimum 8 caractères">
                            <i class="password-toggle fas fa-eye" onclick="togglePassword('password')"></i>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label required">
                            <i class="fas fa-lock-open"></i>
                            Confirmer le mot de passe
                        </label>
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-check-double" id="confirmIcon"></i>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required 
                                   placeholder="Retapez le mot de passe">
                            <i class="password-toggle fas fa-eye" onclick="togglePassword('confirmPassword')"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-sitemap"></i>
                    Affectation
                </h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="service_id" class="form-label required">
                            <i class="fas fa-building"></i>
                            Service
                        </label>
                        <div class="input-wrapper">
                            <select id="service_id" name="service_id" class="form-select" required>
                                <option value="">Sélectionner un service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>" 
                                    <?php echo (isset($form_data['service_id']) && $form_data['service_id'] == $service['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($service['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="salle_id" class="form-label">
                            <i class="fas fa-door-open"></i>
                            Salle
                        </label>
                        <div class="input-wrapper">
                            <select id="salle_id" name="salle_id" class="form-select">
                                <option value="">Sélectionner une salle (optionnel)</option>
                                <?php foreach ($salles as $salle): ?>
                                <option value="<?php echo $salle['id']; ?>" 
                                    <?php echo (isset($form_data['salle_id']) && $form_data['salle_id'] == $salle['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($salle['nom'] . ' (' . $salle['occupants'] . '/' . $salle['capacite'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>