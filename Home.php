<?php
// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoptonmaillot;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
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
    <!-- Inclusion de la navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Maillots Sportifs d'Exception</h1>
            <p>Découvrez notre collection exclusive de maillots de sport authentiques et collector</p>
            <a href="collection.php" class="btn">Explorer la collection</a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="section-header">
            <h2>Nos Produits Phares</h2>
            <p>Les maillots les plus recherchés du moment</p>
        </div>

        <div class="products-grid">
            <?php
            // Récupération des produits les plus ajoutés en favoris
            $sql = "
                SELECT a.*, p.chemin, COUNT(f.id_favoris) AS nb_favoris
                FROM annonce a
                LEFT JOIN (
                    SELECT id_annonce, MIN(id_photo) AS min_id
                    FROM photo
                    GROUP BY id_annonce
                ) first_photo ON a.id_annonce = first_photo.id_annonce
                LEFT JOIN photo p ON p.id_photo = first_photo.min_id
                LEFT JOIN favoris f ON a.id_annonce = f.id_annonce
                GROUP BY a.id_annonce
                ORDER BY nb_favoris DESC
                LIMIT 4
            ";

            $stmt = $pdo->query($sql);

            while ($annonce = $stmt->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($annonce['chemin'] ?? 'https://via.placeholder.com/300x300?text=Sans+Image') ?>" alt="<?= htmlspecialchars($annonce['equipe']) ?>">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($annonce['equipe']) ?> - <?= htmlspecialchars($annonce['type_maillot']) ?></h3>
                        <div class="product-price">€<?= number_format($annonce['prix'], 2) ?></div>
                        <p style="font-size: 0.9em; color: #888;">❤️ <?= $annonce['nb_favoris'] ?> ajout(s) aux favoris</p>
                        <div class="product-actions">
                            <button class="btn-add-to-cart">Ajouter au panier</button>
                            <button class="btn-wishlist"><i class="far fa-heart"></i></button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-header">
            <h2>Parcourir par Catégorie</h2>
            <p>Trouvez le maillot parfait pour votre sport favori</p>
        </div>

        <div class="categories-grid">
            <a href="category.php?cat=football" class="category-card">
                <div class="category-image">
                    <img src="maillot-real-madrid-2024-2025-exterieur-orange-1.jpg" alt="Football">
                </div>
                <h3>Football</h3>
            </a>

            <a href="category.php?cat=basketball" class="category-card">
                <div class="category-image">
                    <img src="1200x680_ekpb8xhx0aa7hoy_1.jpg" alt="Basketball">
                </div>
                <h3>Basketball</h3>
            </a>

            <a href="category.php?cat=rugby" class="category-card">
                <div class="category-image">
                    <img src="sportspaysbasque-c13f73232e1843d289b517e824896325-132259-ph0.jpg" alt="Rugby">
                </div>
                <h3>Rugby</h3>
            </a>

            <a href="category.php?cat=autres" class="category-card">
                <div class="category-image">
                    <img src="maillot-ext-2.jpg" alt="Autres Sports">
                </div>
                <h3>Autres Sports</h3>
            </a>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-us">
        <div class="section-header">
            <h2>Pourquoi Choisir ShopTonMaillot?</h2>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Authenticité Garantie</h3>
                <p>Tous nos maillots sont 100% authentiques avec certificat d'authenticité.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Livraison Rapide</h3>
                <p>Expédition sous 24h et livraison en 2-3 jours ouvrés.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3>Retours Faciles</h3>
                <p>Retours acceptés sous 30 jours sans frais.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Support 24/7</h3>
                <p>Notre équipe est disponible pour répondre à toutes vos questions.</p>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="newsletter-content">
            <h2>Abonnez-vous à notre newsletter</h2>
            <p>Recevez les dernières nouveautés, offres exclusives et réductions.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Votre adresse email" required>
                <button type="submit" class="btn">S'abonner</button>
            </form>
        </div>
    </section>

</body>
</html>