<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: connexion.php");
    exit;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST")  {
    $sport = $_POST['sport'];
    $genre = $_POST['genre'];
    $equipe = htmlspecialchars(trim($_POST['equipe']));
    $type = $_POST['type'];
    $taille = $_POST['taille'];
    $prix = $_POST['prix'];
    $description = htmlspecialchars(trim($_POST['description']));

    if (empty($sport) || empty($genre) || empty($equipe) || empty($type) || empty($taille) || empty($prix)) {
        $errors[] = "Tous les champs obligatoires doivent être remplis.";
    }

    $images = $_FILES['images'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $maxFiles = 5;
    $imageErrors = [];
    $imagePaths = [];
    $allowedTypes = ['image/jpeg', 'image/png'];

    if (count($images['name']) > $maxFiles) {
        $imageErrors[] = "Vous pouvez télécharger au maximum $maxFiles images.";
    } else {
        foreach ($images['name'] as $key => $imageName) {
            $fileType = $images['type'][$key];
            $fileSize = $images['size'][$key];

            if ($fileSize > $maxFileSize) {
                $imageErrors[] = "Le fichier $imageName dépasse la taille maximale de 5MB.";
            }
            if (!in_array($fileType, $allowedTypes)) {
                $imageErrors[] = "Le fichier $imageName n'est pas un format valide. Seuls JPG et PNG sont autorisés.";
            }
            if ($fileSize > 0 && empty($imageErrors)) {
                $imagePath = 'uploads/' . uniqid() . '-' . basename($imageName);
                if (!move_uploaded_file($images['tmp_name'][$key], $imagePath)) {
                    $imageErrors[] = "Erreur lors de l'upload du fichier $imageName.";
                } else {
                    $imagePaths[] = $imagePath;
                }
            }
        }
    }

    if (!empty($imageErrors)) {
        $errors = array_merge($errors, $imageErrors);
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO annonces (sport, genre, equipe, type, taille, prix, description, images, user_id) 
                                    VALUES (:sport, :genre, :equipe, :type, :taille, :prix, :description, :images, :user_id)");
            $stmt->bindParam(':sport', $sport);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':equipe', $equipe);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':taille', $taille);
            $stmt->bindParam(':prix', $prix);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':images', implode(',', $imagePaths));
            $stmt->bindParam(':user_id', $_SESSION['utilisateur_id']);

            $stmt->execute();
            $_SESSION['success'] = "Votre annonce a été publiée avec succès !";
            header("Location: confirmation.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement de l'annonce : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une annonce - ShopTonMaillot</title>
    <link rel="stylesheet" href="annonce.css">
    <style>
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="main-container">
    <div class="form-container">
        <h1>Déposer une annonce de maillot</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-block">
                <?php foreach ($errors as $message): ?>
                    <p class="error-message"><?= htmlspecialchars($message) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Sport - Liste déroulante -->
            <label for="sport">Sport :</label>
            <select name="sport" id="sport" required>
                <option value="">-- Sélectionnez un sport --</option>
                <option value="football">Football</option>
                <option value="basketball">Basketball</option>
                <option value="rugby">Rugby</option>
                <option value="autres">Autres sports</option>
            </select>

            <!-- Genre - Liste déroulante -->
            <label for="genre">Genre :</label>
            <select name="genre" id="genre" required>
                <option value="">-- Sélectionnez un genre --</option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
                <option value="unisexe">Unisexe</option>
                <option value="enfant">Enfant</option>
            </select>

            <!-- Équipe - Champ texte -->
            <label for="equipe">Équipe/nation :</label>
            <input type="text" name="equipe" id="equipe" required>

            <!-- Type - Liste déroulante -->
            <label for="type">Type :</label>
            <select name="type" id="type" required>
                <option value="">-- Sélectionnez un type --</option>
                <option value="domicile">Domicile</option>
                <option value="exterieur">Extérieur</option>
                <option value="entrainement">Entraînement</option>
                <option value="third">Third/Alternatif</option>
                <option value="special">Édition spéciale</option>
            </select>

            <!-- Taille - Liste déroulante -->
            <label for="taille">Taille :</label>
            <select name="taille" id="taille" required>
                <option value="">-- Sélectionnez une taille --</option>
                <option value="XS">XS (Extra Small)</option>
                <option value="S">S (Small)</option>
                <option value="M">M (Medium)</option>
                <option value="L">L (Large)</option>
                <option value="XL">XL (Extra Large)</option>
                <option value="XXL">XXL (Double Extra Large)</option>
                <option value="XXXL">XXXL (Triple Extra Large)</option>
                
            </select>

            <!-- Prix - Champ nombre -->
            <label for="prix">Prix (€) :</label>
            <input type="number" name="prix" id="prix" step="0.01" min="0" required>

            <!-- Description - Zone de texte -->
            <label for="description">Description :</label>
            <textarea name="description" id="description" rows="4"></textarea>

            <!-- Images - Upload multiple -->
            <label for="images">Images (max 5, JPG/PNG) :</label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg, image/png">

            <button type="submit" class="btn-submit">Publier l'annonce</button>
        </form>
    </div>
</div>

</body>
</html>