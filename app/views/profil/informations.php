<div class="profile-container">
    <div class="profile-header">
        <h2>Informations du profil</h2>
        <p>Gérez vos informations personnelles</p>
    </div>

    <div class="card profile-nformations">
        <div class="profile-picture-section">
            <div class="profile-picture">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($form_data['prenom'] . ' ' . $form_data['nom']) ?>&background=3b82f6&color=fff" alt="Photo de profil" id="profile-preview">
                <div class="name-display">
                    <h2 id="display-name"><?= htmlspecialchars($form_data['prenom'] . ' ' . $form_data['nom']) ?></h2>
                </div>
            </div>
        </div>

        <form class="profile-form" id="profile-form" action="<?= RouterHelper::getFullUrl('/profil/informations') ?>" method="POST" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($form_data['nom'] ?? '') ?>" required>
                    <div class="error-message" id="nom-error">Le nom est requis</div>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($form_data['prenom'] ?? '') ?>" required>
                    <div class="error-message" id="prenom-error">Le prénom est requis</div>
                </div>
            </div>

            <?php if ($_SESSION['role'] !== 'agent'): ?>
            <div class="form-group">
                <label for="telephone">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Ex: 0612345678 ou +212612345678" value="<?= htmlspecialchars($form_data['numero'] ?? '') ?>">
                <div class="error-message" id="telephone-error">Format invalide. Utilisez 0 suivi de 9 chiffres ou +212 suivi de 9 chiffres.</div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="role">Rôle</label>
                <input type="text" id="role" value="<?= htmlspecialchars(ucfirst($_SESSION['role'])) ?>" disabled>
                <small>Le rôle ne peut pas être modifié depuis cette interface</small>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" disabled>
                <small>L'adresse email ne peut pas être modifiée</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="submit-btn">Enregistrer les modifications</button>
                <button type="reset" class="btn btn-outline">Annuler</button>
            </div>
        </form>
    </div>
</div>


 