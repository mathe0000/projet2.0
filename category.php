<?php
// Connexion à la base de données
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoptonmaillot;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Catégories valides
$validCategories = ['football', 'basketball', 'rugby', 'autres'];
$category = isset($_GET['cat']) ? strtolower($_GET['cat']) : null;

if (!in_array($category, $validCategories)) {
    header('Location: home.php');
    exit();
}

// Filtres
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$team = isset($_GET['team']) ? trim($_GET['team']) : '';

// Configuration de la pagination
$perPage = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Récupération des produits
$sql = "SELECT SQL_CALC_FOUND_ROWS a.*, p.chemin, 
               COUNT(f.id_favoris) AS nb_favoris,
               u.nom AS vendeur_nom, u.prenom AS vendeur_prenom
        FROM annonce a
        LEFT JOIN photo p ON p.id_annonce = a.id_annonce
        LEFT JOIN favoris f ON a.id_annonce = f.id_annonce
        LEFT JOIN utilisateur u ON a.id_utilisateur = u.id_utilisateur
        WHERE a.sport = :category
        AND a.prix BETWEEN :min_price AND :max_price
        AND (:team = '' OR a.equipe LIKE :team_like)
        GROUP BY a.id_annonce
        ORDER BY a.id_annonce DESC
        LIMIT :offset, :per_page";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':category', $category);
$stmt->bindValue(':min_price', $minPrice);
$stmt->bindValue(':max_price', $maxPrice);
$stmt->bindValue(':team', $team);
$stmt->bindValue(':team_like', "%$team%", PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProducts = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Noms des catégories
$categoryNames = [
    'football' => 'Football',
    'basketball' => 'Basketball',
    'rugby' => 'Rugby',
    'autres' => 'Autres Sports'
];
?>
<?php
// [Votre code PHP reste inchangé jusqu'au DOCTYPE]
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopTonMaillot - <?= $categoryNames[$category] ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a9d8f;
            --secondary-color: #e63946;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
        }
        
        .category-header { 
            background: var(--dark-color); 
            color: white; 
            padding: 80px 20px 60px;
            text-align: center; 
            position: relative;
            overflow: hidden;
        }
        
        .category-header::after {
            content: "";
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: var(--light-color);
            transform: skewY(-2deg);
            z-index: 1;
        }
        
        .category-header h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }
        
        .category-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .products-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 40px 20px; 
            position: relative;
            z-index: 2;
        }
        
        /* Nouveau style pour les filtres */
        .filters-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 40px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .filters-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: var(--dark-color);
        }
        
        .filters-title i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .filter-group {
            position: relative;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.95rem;
        }
        
        .filter-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .filter-input:focus {
            border-color: var(--primary-color);
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.2);
        }
        
        .filter-input::placeholder {
            color: #adb5bd;
        }
        
        .filter-submit {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
        }
        
        .btn-filter {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-filter:hover {
            background: #21867a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .btn-filter i {
            margin-right: 8px;
        }
        
        /* Styles existants améliorés */
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 25px; 
            margin-top: 30px; 
        }
        
        .product-card { 
            border: 1px solid #e9ecef; 
            border-radius: 10px; 
            overflow: hidden; 
            transition: all 0.3s ease; 
            background: white;
        }
        
        .product-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
            border-color: var(--primary-color);
        }
        
        .product-image { 
            height: 240px; 
            overflow: hidden; 
            position: relative;
        }
        
        .product-image img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.5s ease; 
        }
        
        .product-card:hover .product-image img { 
            transform: scale(1.05); 
        }
        
        .product-info { 
            padding: 20px; 
        }
        
        .product-title { 
            font-size: 1.2rem; 
            margin-bottom: 8px; 
            color: var(--dark-color); 
            font-weight: 600;
        }
        
        .product-meta { 
            font-size: 0.95rem; 
            color: var(--gray-color); 
            margin-bottom: 5px; 
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .product-meta i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }
        
        .product-price { 
            font-weight: bold; 
            color: var(--secondary-color); 
            font-size: 1.3rem; 
            margin: 15px 0; 
        }
        
        .product-seller { 
            font-size: 0.9rem; 
            color: var(--gray-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .product-seller i {
            color: var(--primary-color);
        }
        
        .product-fav { 
            font-size: 0.9rem; 
            color: var(--gray-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .pagination { 
            display: flex; 
            justify-content: center; 
            margin: 50px 0 30px; 
            gap: 8px; 
            flex-wrap: wrap; 
        }
        
        .pagination a, .pagination span { 
            padding: 10px 16px; 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            text-decoration: none; 
            color: var(--dark-color);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .pagination a:hover { 
            background-color: #f1f3f5; 
            border-color: #ced4da;
        }
        
        .pagination .current { 
            background-color: var(--primary-color); 
            color: white; 
            border-color: var(--primary-color);
        }
        
        .back-to-home { 
            text-align: center; 
            margin: 40px 0 20px; 
        }
        
        .btn { 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px; 
            background-color: var(--primary-color); 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn:hover { 
            background-color: #21867a; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .no-products i {
            font-size: 2.5rem;
            color: var(--gray-color);
            margin-bottom: 15px;
        }
        
        .no-products p {
            font-size: 1.1rem;
            color: var(--dark-color);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <header class="category-header">
        <h1>Maillots de <?= $categoryNames[$category] ?></h1>
        <p>Découvrez notre sélection exclusive</p>
    </header>

    <main class="products-container">
        <form class="filters-container" method="get" action="">
            <input type="hidden" name="cat" value="<?= htmlspecialchars($category) ?>">
            
            <div class="filters-title">
                <i class="fas fa-sliders-h"></i>
                <h2>Filtrer les résultats</h2>
            </div>
            
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="min_price"><i class="fas fa-euro-sign"></i> Prix minimum</label>
                    <input type="number" class="filter-input" name="min_price" id="min_price" 
                           placeholder="10€" min="0" value="<?= htmlspecialchars($minPrice) ?>">
                </div>

                <div class="filter-group">
                    <label for="max_price"><i class="fas fa-euro-sign"></i> Prix maximum</label>
                    <input type="number" class="filter-input" name="max_price" id="max_price" 
                           placeholder="200€" min="0" value="<?= htmlspecialchars($maxPrice) ?>">
                </div>

                <div class="filter-group">
                    <label for="team"><i class="fas fa-users"></i> Équipe</label>
                    <input type="text" class="filter-input" name="team" id="team" 
                           placeholder="PSG, Real Madrid..." value="<?= htmlspecialchars($team) ?>">
                </div>
                
                <div class="filter-submit">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Appliquer les filtres
                    </button>
                </div>
            </div>
        </form>

        <div class="products-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= !empty($product['chemin']) ? htmlspecialchars($product['chemin']) : 'https://via.placeholder.com/300x300?text=Sans+Image' ?>" 
                                 alt="<?= htmlspecialchars($product['equipe']) ?>">
                        </div>
                        <div class="product-info">
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
                    <p>Aucun produit ne correspond à vos critères de recherche.</p>
                    <a href="category.php?cat=<?= $category ?>" class="btn">
                        <i class="fas fa-times"></i> Réinitialiser les filtres
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?cat=<?= $category ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&team=<?= urlencode($team) ?>&page=1">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="?cat=<?= $category ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&team=<?= urlencode($team) ?>&page=<?= $page - 1 ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($page + 2, $totalPages); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?cat=<?= $category ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&team=<?= urlencode($team) ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?cat=<?= $category ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&team=<?= urlencode($team) ?>&page=<?= $page + 1 ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="?cat=<?= $category ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?>&team=<?= urlencode($team) ?>&page=<?= $totalPages ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="back-to-home">
            <a href="home.php" class="btn">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </main>
</body>
</html>