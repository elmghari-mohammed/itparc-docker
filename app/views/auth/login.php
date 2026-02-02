<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion – ITparc</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/login.css">
</head>
<body>
  <div class="background-svg">
    <svg viewBox="0 0 1000 400" preserveAspectRatio="none">
      <polyline points="0,200 250,200 300,250 700,250 750,200 1000,200" style="fill:none;stroke:#4bb6e0;stroke-width:6;opacity:0.11"></polyline>
      <circle cx="250" cy="200" r="13" fill="#b3eafd" opacity="0.15"></circle>
      <circle cx="700" cy="250" r="13" fill="#4bb6e0" opacity="0.12"></circle>
    </svg>
  </div>

  <main class="login-main">
    <section class="branding">
      <div class="branding-content">
        <div class="logo-container">
          <h1 class="logo">IT<span>PARCS</span></h1>
          <p class="slogan">
            Optimisez la gestion, maîtrisez l'intervention.<br>
            <span class="mission">Votre parc, votre expertise, notre technologie.</span>
          </p>
        </div>
      </div>
    </section>

    <section class="login-card">
      <h2>Connexion sécurisée</h2>
      <?php if (!empty($error)): ?>
        <div style="color:red; margin-bottom:15px;"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form class="login-form" method="post">
        <div class="form-group">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="password-container">
            <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
            <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Afficher le mot de passe">
              <span class="eye-icon eye-closed"></span>
            </button>
          </div>
        </div>
        <div class="form-options">
          <label><input type="checkbox" name="remember"> Se souvenir de moi</label>
          <a href="#">Mot de passe oublié ?</a>
        </div>
        <button type="submit" class="btn-login">Se connecter</button>
      </form>
      <div class="login-footer">
        <p>Besoin d'aide ? <a href="mailto:support@techmaintain.com">Contactez le support</a></p>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <p>© 2025 Tech Maintain. Tous droits réservés. | <a href="#">Mentions légales</a></p>
  </footer>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleButton = document.querySelector('.toggle-password');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.innerHTML = '<span class="eye-icon eye-open"></span>';
        toggleButton.setAttribute('aria-label', 'Masquer le mot de passe');
      } else {
        passwordInput.type = 'password';
        toggleButton.innerHTML = '<span class="eye-icon eye-closed"></span>';
        toggleButton.setAttribute('aria-label', 'Afficher le mot de passe');
      }
    }
  </script>  
</body>
</html>