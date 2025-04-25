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
        <a href="collection.html" class="btn">Explorer la collection</a>
    </div>
</section>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="section-header">
            <h2>Nos Produits Phares</h2>
            <p>Les maillots les plus recherchés du moment</p>
        </div>
        
        <div class="products-grid">
            <div class="product-card">
                <div class="product-badge">Nouveau</div>
                <img src="https://via.placeholder.com/300x300?text=Maillot+PSG+2023" alt="Maillot PSG 2023">
                <div class="product-info">
                    <h3>Maillot PSG Domicile 2023/24</h3>
                    <div class="product-price">€89.99</div>
                    <div class="product-actions">
                        <button class="btn-add-to-cart">Ajouter au panier</button>
                        <button class="btn-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-badge">-20%</div>
                <img src="https://via.placeholder.com/300x300?text=Maillot+France+2022" alt="Maillot France 2022">
                <div class="product-info">
                    <h3>Maillot France Équipe 2022</h3>
                    <div class="product-price"><span class="old-price">€99.99</span> €79.99</div>
                    <div class="product-actions">
                        <button class="btn-add-to-cart">Ajouter au panier</button>
                        <button class="btn-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <img src="https://via.placeholder.com/300x300?text=Maillot+Lakers" alt="Maillot Lakers">
                <div class="product-info">
                    <h3>Maillot Los Angeles Lakers</h3>
                    <div class="product-price">€74.99</div>
                    <div class="product-actions">
                        <button class="btn-add-to-cart">Ajouter au panier</button>
                        <button class="btn-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <img src="https://via.placeholder.com/300x300?text=Maillot+Barcelone" alt="Maillot Barcelone">
                <div class="product-info">
                    <h3>Maillot FC Barcelone 2023</h3>
                    <div class="product-price">€84.99</div>
                    <div class="product-actions">
                        <button class="btn-add-to-cart">Ajouter au panier</button>
                        <button class="btn-wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-header">
            <h2>Parcourir par Catégorie</h2>
            <p>Trouvez le maillot parfait pour votre sport favori</p>
        </div>
        
        <div class="categories-grid">
            <a href="category.html?cat=football" class="category-card">
                <div class="category-image">
                    <img src="maillot-real-madrid-2024-2025-exterieur-orange-1.jpg" alt="Football">
                </div>
                <h3>Football</h3>
            </a>
            
            <a href="category.html?cat=basketball" class="category-card">
                <div class="category-image">
                    <img src="1200x680_ekpb8xhx0aa7hoy_1.jpg" alt="Basketball">
                </div>
                <h3>Basketball</h3>
            </a>
            
            <a href="category.html?cat=rugby" class="category-card">
                <div class="category-image">
                    <img src="sportspaysbasque-c13f73232e1843d289b517e824896325-132259-ph0.jpg" alt="Rugby">
                </div>
                <h3>Rugby</h3>
            </a>
            
            <a href="category.html?cat=autres" class="category-card">
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
