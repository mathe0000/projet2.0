<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$estConnecte = isset($_SESSION['utilisateur_id']); // Vérifie si l'utilisateur est connecté
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopTonMaillot - Maillots sportifs de qualité</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="home.php" class="logo">ShopTonMaillot</a>

        <div class="navbar-links">
            <a href="collection.php" class="nav-link">Collection</a>

            <!-- Mettre une annonce : redirection si pas connecté -->
            <a href="<?= $estConnecte ? 'annonce.php' : 'acces-refuse.html' ?>" class="nav-link">Mettre une annonce</a>

            <div class="dropdown">
                <a href="#" class="nav-link">Catégories <i class="fas fa-chevron-down"></i></a>
                <div class="dropdown-content">
                    <a href="category.php?cat=football">Football</a>
                    <a href="category.php?cat=basketball">Basketball</a>
                    <a href="category.php?cat=rugby">Rugby</a>
                    <a href="category.php?cat=autres">Autres sports</a>
                </div>
            </div>

            <!-- Favoris : uniquement si connecté -->
            <?php if ($estConnecte): ?>
                <a href="favoris.html" class="nav-link"><i class="fas fa-heart"></i> Favoris</a>
            <?php endif; ?>

            <!-- Mon compte : uniquement si connecté -->
            <?php if ($estConnecte): ?>
                <div class="dropdown">
                    <a href="#" class="nav-link">Mon compte <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <a href="annonce.php">Mes annonces</a>
                        <a href="espace-personnel.html">Espace personnel</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Connexion ou Déconnexion -->
            <?php if (!$estConnecte): ?>
                <a href="connexion2.php" class="nav-link">Se connecter</a>
            <?php else: ?>
                <a href="logout.php" class="nav-link">Se déconnecter</a>
            <?php endif; ?>

            <!-- Panier : redirection si pas connecté -->
            <a href="<?= $estConnecte ? 'panier.html' : 'acces-refuse.html' ?>" class="nav-link cart-icon">
                <i class="fas fa-shopping-cart"></i> <span class="cart-count">0</span>
            </a>

            <!-- Messagerie : redirection si pas connecté -->
            <a href="<?= $estConnecte ? 'discussion.html' : 'acces-refuse.html' ?>" class="nav-link">
                <i class="fas fa-envelope"></i>
            </a>
        </div>

        <div class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<!-- Ici tu peux continuer ton contenu de page -->
</body>
</html>
