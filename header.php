<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<header class="header hidden-header">
  <!-- Side Menu caché -->
  <div id="side-menu" class="side-menu hidden">
    <ul>
      <!-- Lien Jeux et dropdown Classement en mobile -->
      <li><a href="/mes-jeux.php">Jeux</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle">Classement ▾</a>
        <ul class="dropdown-menu">
          <li><a href="/devine-le-film/leaderboard.php">Devine sans le titre</a></li>
          <li><a href="/devine-le-film-emoji/leaderboard.php">Devine avec emojis</a></li>
          <li><a href="/DevineInfini/leaderboard-infini.php">Devine Infini</a></li>
        </ul>
      </li>
    </ul>
    <div class="auth-links">
      <?php if(!empty($_SESSION['user'])): ?>
        <a href="/mon_compte.php" class="btn btn--primary">Mon compte</a>
        <a href="/auth/logout.php" class="btn btn--secondary">
          <span>Déconnexion</span>
        </a>
      <?php else: ?>
        <button id="auth-btn-mobile" class="btn btn--primary">Connexion / Inscription</button>
      <?php endif; ?>
    </div>
  </div>

  <div class="logo">
    <a href="/">
      <img src="/images/polcorn-game.png" alt="POLCORN" class="logo-img">
    </a>
  </div>
  <!-- Burger menu (visible mobile uniquement) -->
  <div class="burger-menu" id="burger-menu">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <nav class="nav">
    <ul class="nav-links">
      <li><a href="/mes-jeux.php">Jeux</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle">Classement ▾</a>
        <ul class="dropdown-menu">
          <li><a href="/devine-le-film/leaderboard.php">Devine sans le titre</a></li>
          <li><a href="/devine-le-film-emoji/leaderboard.php">Devine avec emojis</a></li>
          <li><a href="/DevineInfini/leaderboard-infini.php">Devine Infini</a></li>
        </ul>
      </li>
    </ul>

    <div class="auth-links">
      <?php if(!empty($_SESSION['user'])): ?>
        <a href="/mon_compte.php" class="btn btn--primary">Mon compte</a>
        <a href="/auth/logout.php" class="btn btn--secondary">
          <span>Déconnexion</span>
        </a>
      <?php else: ?>
        <button id="auth-btn" class="btn btn--primary">Connexion / Inscription</button>
      <?php endif; ?>
    </div>
  </nav>
</header>

<!-- === Modal d’authentification === -->
<div id="auth-modal" class="modal-overlay hidden">
  <div class="modal">
    <button class="modal-close">&times;</button>
    <h2>Connexion / Inscription</h2>
    <!-- Google OAuth -->
    <a href="/auth/google.php" class="btn btn--google">
      <img src="/images/google-icon.svg" alt="" class="icon"> Continuer avec Google
    </a>
    <hr>
    <!-- Onglets Connexion / Inscription -->
    <div class="form-tabs">
      <button id="tab-login" class="active">Connexion</button>
      <button id="tab-register">Inscription</button>
    </div>
    <!-- Formulaire Connexion -->
    <form id="login-form" class="auth-form" action="/auth/login.php" method="POST">
      <input type="email" name="email" placeholder="Votre e-mail" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <button type="submit" class="btn btn--primary">Se connecter</button>
    </form>
    <!-- Formulaire Inscription -->
    <form id="register-form" class="auth-form hidden" action="/auth/register.php" method="POST">
      <input type="email" name="email" placeholder="Votre e-mail" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <input type="password" name="password_confirm" placeholder="Confirmer mot de passe" required>
      <button type="submit" class="btn btn--primary">S’inscrire</button>
    </form>
  </div>
</div>

<!-- === Scripts header === -->
<script>
  // Ouvre la modal
  document.getElementById('auth-btn')?.addEventListener('click', () => {
    document.getElementById('auth-modal').classList.remove('hidden');
  });
  document.getElementById('auth-btn-mobile')?.addEventListener('click', () => {
    document.getElementById('auth-modal').classList.remove('hidden');
  });
  // Ferme la modal
  document.querySelector('.modal-close')?.addEventListener('click', () => {
    document.getElementById('auth-modal').classList.add('hidden');
  });
  // Switch tabs
  const tabLogin    = document.getElementById('tab-login');
  const tabRegister = document.getElementById('tab-register');
  const formLogin   = document.getElementById('login-form');
  const formReg     = document.getElementById('register-form');
  tabLogin.addEventListener('click', () => {
    tabLogin.classList.add('active');
    tabRegister.classList.remove('active');
    formLogin.classList.remove('hidden');
    formReg.classList.add('hidden');
  });
  tabRegister.addEventListener('click', () => {
    tabRegister.classList.add('active');
    tabLogin.classList.remove('active');
    formReg.classList.remove('hidden');
    formLogin.classList.add('hidden');
  });
  // Burger menu
  const burgerMenu = document.getElementById('burger-menu');
  const sideMenu   = document.getElementById('side-menu');
  burgerMenu.addEventListener('click', () => {
    sideMenu.classList.toggle('open');
    burgerMenu.classList.toggle('open');
  });
  // Toggle mobile dropdown in side-menu (ouvre et ferme)
  document.querySelectorAll('#side-menu .dropdown-toggle').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const parent = btn.parentElement;
      parent.classList.toggle('open');
    });
  });
</script>

<script>
const burgerMenu = document.getElementById('burger-menu');
const sideMenu   = document.getElementById('side-menu');

burgerMenu.addEventListener('click', () => {
  sideMenu.classList.toggle('open');
  burgerMenu.classList.toggle('open');
});
</script>

<style>
.header {
    display: flex
;
    align-items: center;
    padding: 20px 40px;
    background: #1e1e1e47;
    box-shadow: 0 2px 8px rgba(90, 200, 250, 0.2);
    border-width: 0px 0px 1px 0px!important;
    border: solid #5ac8fa;
    gap: 80px;
}
    .logo-img {
  height: 40px;
  vertical-align: middle;
}

.nav-links {
  display: flex;
  gap: 20px;
  list-style: none;
  padding: 0;
}

.nav-links li {
  position: relative;
}

.nav-links a {
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  padding: 10px;
}

.nav-links .dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: #2c2c2c;
  border-radius: 6px;
  padding: 8px 0;
  display: none;
  z-index: 999;
}

.nav-links .dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu li a {
  display: block;
  padding: 8px 16px;
  color: #eee;
}

.dropdown-menu li a:hover {
  background: #444;
  color: #fff;
}

/* CSSS POUR HEADER */

/* Modal Authentification */
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.7);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal-overlay.hidden {
  display: none;
}
.modal {
  background: #1e1e1e;
  padding: 24px;
  border-radius: 12px;
  width: 320px;
  max-width: 90%;
  position: relative;
  text-align: center;
}
.modal-close {
  position: absolute;
  top: 12px; right: 12px;
  background: none; border: none;
  font-size: 1.5rem; color: #e1e1e1;
  cursor: pointer;
}
.modal h2 {
  margin-bottom: 16px;
  font-size: 1.25rem;
  color: #e1e1e1;
}
.btn--google {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #fff;
  color: #444;
  padding: 8px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  margin-bottom: 16px;
}
.btn--google .icon {
  width: 20px; height: 20px;
}
hr {
  border: none;
  border-top: 1px solid #444;
  margin: 16px 0;
}
.form-tabs {
  display: flex;
  justify-content: center;
  margin-bottom: 12px;
}
.form-tabs button {
  background: none;
  border: none;
  color: #aaa;
  padding: 8px 12px;
  cursor: pointer;
  font-weight: 600;
}
.form-tabs button.active {
  color: #5ac8fa;
  border-bottom: 2px solid #5ac8fa;
}
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.auth-form.hidden { display: none; }
.auth-form input {
  padding: 10px;
  border: 1px solid #444;
  border-radius: 6px;
  background: #2c2c2c;
  color: #e1e1e1;
}
.auth-form .btn--primary {
  background: #5ac8fa;
  color: #121212;
  border: none;
  padding: 10px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
}
.auth-form .btn--primary:hover {
  background: #3eaee2;
}
.nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
      gap: 40px;
}

.btn--primary {
  background: linear-gradient(135deg, #5ac8fa, #3eaee2);
  color: #121212;
  border: none;
  border-radius: 8px;
  padding: 12px 20px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 0 12px #5ac8fa;
  position: relative;
  overflow: hidden;
  animation: pulse 2s infinite;
}

/* Hover */
.btn--primary:hover {
  background: linear-gradient(135deg, #3eaee2, #5ac8fa);
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 0 20px #5ac8fa;
}

/* Effet de lumière glissante */
.btn--primary::after {
  content: "";
  position: absolute;
  top: 0;
  left: -75%;
  width: 50%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  transform: skewX(-25deg);
  transition: all 0.7s ease;
}

.btn--primary:hover::after {
  left: 130%;
}

/* Animation pulse */
@keyframes pulse {
  0%   { box-shadow: 0 0 12px #5ac8fa; }
  50%  { box-shadow: 0 0 24px #5ac8fa; }
  100% { box-shadow: 0 0 12px #5ac8fa; }
}

.btn--secondary span {
  display: none; /* Cacher le texte par défaut */
}

/* En mobile on montre le texte */
@media (max-width: 768px) {
  .btn--secondary span {
    display: inline;
  }
}

/* === Responsive === */
@media (max-width: 768px) {
  .header {
    
    justify-content: center;
    padding: 12px;
  }

  .nav {
    flex-direction: column;
    align-items: center;
    width: 100%;
  }

  .nav-links {
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
  }

  .auth-links {
    flex-direction: column;
    align-items: center;
  }
}

.auth-links {
  display: flex;
  
  gap: 12px;
}


  .dropdown-menu {
    position: relative;
    top: auto;
    left: auto;
    background: #2c2c2c;
    border-radius: 6px;
    padding: 8px;
    display: none;
  }

  .dropdown:hover .dropdown-menu {
    display: block;
  }



/* --- Burger Menu --- */
.burger-menu {
  display: none;
  flex-direction: column;
  gap: 6px;
  cursor: pointer;
  z-index: 1100;
}
.burger-menu span {
  width: 28px;
  height: 3px;
  background: #5ac8fa;
  border-radius: 2px;
  transition: 0.3s;
}

/* Animation croix */
.burger-menu.open span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}
.burger-menu.open span:nth-child(2) {
  opacity: 0;
}
.burger-menu.open span:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

/* --- Side Menu --- */
.side-menu {
  position: fixed!important;
  top: 0; left: -250px;
  width: 220px;
  height: 100vh;
      background: #1e1e1ef7;
  box-shadow: 2px 0 12px rgba(90,200,250,0.4);
  padding: 60px 20px;
  display: flex!important;
  flex-direction: column;
  gap: 20px;
  transition: left 0.4s ease;
  z-index: 999!important;
}

.side-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.side-menu li {
  margin-bottom: 20px;
}
.side-menu a {
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
    font-size: 1.1rem;
}
.side-menu a:hover {
  text-decoration: underline;
}

/* Animation ouverture */
.side-menu.open {
  left: 0;
}

/* --- Affiche burger uniquement en mobile --- */
@media (max-width: 768px) {
  .burger-menu {
    display: flex;
    position: absolute;
    top: 20px;
    right: 20px;
  }
  
  .nav {
    display: none;
  }
}


/* Bouton "Déconnexion" */
.btn--secondary {
  background: none;
  color: #5ac8fa;
  border: 2px solid #5ac8fa;
  border-radius: 8px;
  padding: 10px 18px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Hover déconnexion */
.btn--secondary:hover {
  background: #5ac8fa;
  color: #121212;
}

/* Icône logout */
.btn--secondary::before {
  content: "\f011"; /* Unicode FontAwesome pour "power-off" */
  font-family: "Font Awesome 5 Free"; 
  font-weight: 900;
  font-size: 1.1rem;
}


.auth-links a {
  text-decoration: none;
}

.side-menu .dropdown-menu {
  list-style: none;
  padding-left: 0;
  margin-top: 8px;
  display: none;
}
.side-menu .dropdown.open .dropdown-menu {
  display: block;
}

</style>