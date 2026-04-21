
    <div class="profile-container">
        <?php if (!empty($success_message)): ?>
            <script>
                // Redirection après 3 secondes
                setTimeout(function() {
                    window.location.href = '<?= RouterHelper::getFullUrl('logout') ?>';
                }, 3000);
            </script>
        <?php endif; ?>

        <div class="profile-header">
            <i class="pas pa-password"></i>
            <h2>Changer le mot de passe</h2>
            <p>Mettez à jour votre mot de passe pour renforcer la sécurité</p>
        </div>

        <div class="card motdePass-card">
            <form class="password-form" method="POST" action="<?= RouterHelper::getFullUrl('profil/mot-de-passe') ?>">
                <div class="form-group">
                    <label for="current-password">Mot de passe actuel</label>
                    <input type="password" id="current-password" name="current_password" required >
                </div>

                <div class="form-group">
                    <label for="new-password">Nouveau mot de passe</label>
                    <input type="password" id="new-password" name="new_password" required>
                    <small>Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre</small>
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm-password" name="confirm_password" required >
                    <div id="password-match" class="validation-message"></div>
                </div>

                <div class="form-group" id="other-reason-container" style="display: none;">
                    <label for="other-reason">Précisez la raison</label>
                    <textarea id="other-reason" name="other_reason" rows="3"></textarea>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
                </div>
            </form>
        </div>


