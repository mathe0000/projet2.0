<?php
// Démarrage de la session (doit être la première ligne)
session_start();

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoptonmaillot;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer toutes les catégories de sport disponibles
$categories = $pdo->query("SELECT DISTINCT sport FROM annonce ORDER BY sport")->fetchAll(PDO::FETCH_COLUMN);
// Récupérer toutes les équipes disponibles
$equipes = $pdo->query("SELECT DISTINCT equipe FROM annonce ORDER BY equipe")->fetchAll(PDO::FETCH_COLUMN);

// Filtres
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$selectedEquipe = isset($_GET['equipe']) ? $_GET['equipe'] : '';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 1000;

// Configuration de la pagination
$perPage = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Requête de base avec filtres
$sql = "SELECT SQL_CALC_FOUND_ROWS a.*, p.chemin, 
               COUNT(f.id_favoris) AS nb_favoris,
               u.nom AS vendeur_nom, u.prenom AS vendeur_prenom
        FROM annonce a
        LEFT JOIN photo p ON p.id_annonce = a.id_annonce
        LEFT JOIN favoris f ON a.id_annonce = f.id_annonce
        LEFT JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
        WHERE (:category = '' OR a.sport = :category)
        AND (:equipe = '' OR a.equipe = :equipe)
        AND a.prix BETWEEN :min_price AND :max_price
        GROUP BY a.id_annonce
        ORDER BY a.id_annonce DESC
        LIMIT :offset, :per_page";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':category', $selectedCategory);
$stmt->bindValue(':equipe', $selectedEquipe);
$stmt->bindValue(':min_price', $minPrice);
$stmt->bindValue(':max_price', $maxPrice);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProducts = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopTonMaillot - Toute la collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="collection.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <header class="collection-header">
        <h1>Notre Collection Complète</h1>
        <p>Découvrez tous nos maillots sportifs authentiques et collector</p>
    </header>

    <main class="collection-container">
        <form class="filters-container" method="get" action="">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                <h2>Filtrer la collection</h2>
            </div>
            
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="category"><i class="fas fa-running"></i> Sport</label>
                    <select class="filter-select" name="category" id="category">
                        <option value="">Tous les sports</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($category)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="equipe"><i class="fas fa-users"></i> Équipe</label>
                    <select class="filter-select" name="equipe" id="equipe">
                        <option value="">Toutes les équipes</option>
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?= htmlspecialchars($equipe) ?>" <?= $selectedEquipe === $equipe ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equipe) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="min_price"><i class="fas fa-euro-sign"></i> Prix min</label>
                    <input type="number" class="filter-input" name="min_price" id="min_price" 
                           placeholder="10" min="0" step="0.01" value="<?= htmlspecialchars($minPrice) ?>">
                </div>

                <div class="filter-group">
                    <label for="max_price"><i class="fas fa-euro-sign"></i> Prix max</label>
                    <input type="number" class="filter-input" name="max_price" id="max_price" 
                           placeholder="200" min="0" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>">
                </div>
                
                <div class="filter-submit">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Appliquer les filtres
                    </button>
                </div>
            </div>
        </form>

        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['nb_favoris'] > 10): ?>
                            <span class="product-badge">Populaire</span>
                        <?php endif; ?>
                        
                        <div class="product-image">
                            <img src="<?= !empty($product['chemin']) ? htmlspecialchars($product['chemin']) : 'https://via.placeholder.com/300x300?text=Sans+Image' ?>" 
                                 alt="<?= htmlspecialchars($product['equipe']) ?>">
                        </div>
                        <div class="product-info">
                            <span class="product-sport"><?= htmlspecialchars(ucfirst($product['sport'])) ?></span>
                            <h3 class="product-title"><?= htmlspecialchars($product['equipe']) ?></h3>
                            <p class="product-meta">
                                <i class="fas fa-tshirt"></i> <?= htmlspecialchars($product['type_maillot']) ?>
                            </p>
                            <p class="product-meta">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($product['genre']) ?>
                            </p>
                            <p class="product-meta">
                                <i class="fas fa-ruler-vertical"></i> Taille <?= htmlspecialchars($product['taille']) ?>
                            </p>
                            <div class="product-price">€<?= number_format($product['prix'], 2) ?></div>
                            <p class="product-seller">
                                <i class="fas fa-store"></i> 
                                <?= htmlspecialchars($product['vendeur_prenom']) ?> <?= htmlspecialchars(substr($product['vendeur_nom'], 0, 1)) ?>.
                            </p>
                            <p class="product-fav">
                                <i class="fas fa-heart"></i> <?= $product['nb_favoris'] ?> favoris
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-search"></i>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Nous n'avons trouvé aucun produit correspondant à vos critères de recherche.</p>
                    <a href="collection.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Réinitialiser les filtres
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1 && count($products) > 0): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?category=<?= urlencode($selectedCategory) ?>&equipe=<?= urlencode($selectedEquipe) ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&page=1">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?category=<?= urlencode($selectedCategory) ?>&equipe=<?= urlencode($selectedEquipe) ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&page=<?= $page - 1 ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?category=<?= urlencode($selectedCategory) ?>&equipe=<?= urlencode($selectedEquipe) ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?category=<?= urlencode($selectedCategory) ?>&equipe=<?= urlencode($selectedEquipe) ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&page=<?= $page + 1 ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?category=<?= urlencode($selectedCategory) ?>&equipe=<?= urlencode($selectedEquipe) ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&page=<?= $totalPages ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>