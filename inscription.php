<?php 
require("connexion.php");

$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"] ?? '');
    $prenom = trim($_POST["prenom"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $telephone = trim($_POST["telephone"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $confirm_password = trim($_POST["confirm_password"] ?? '');

    // Vérifications
    if (empty($nom)) $errors['nom'] = "Le nom est requis.";
    if (empty($prenom)) $errors['prenom'] = "Le prénom est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Adresse email invalide.";
    if (!empty($telephone) && !preg_match('/^\+?[0-9]{7,15}$/', $telephone)) {
        $errors['telephone'] = "Numéro de téléphone invalide.";
    }
    if (strlen($password) < 8) $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";

    // Vérifier que l'email n'est pas déjà utilisé
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE adresse_mail = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors['email'] = "Cette adresse email est déjà utilisée.";
        }
    }

    // Insertion si tout est OK
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, adresse_mail, numero_tel, mot_de_passe)
                                    VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':mot_de_passe', $password_hash);
            $stmt->execute();

            header("Location: connexion2.php?success=1");
            exit();

        } catch (PDOException $e) {
            $errors['global'] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ShopTonMaillot</title>
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
<div class="container">
    <div class="logo">
        <h1>ShopTonMaillot</h1>
        <p>Créez votre compte pour commencer</p>
    </div>

    <?php if (!empty($errors['global'])): ?>
        <div class="error-message" style="text-align:center"><?= htmlspecialchars($errors['global']) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="nom" class="required">Nom</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
            <?php if (!empty($errors['nom'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['nom']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="prenom" class="required">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom ?? '') ?>" required>
            <?php if (!empty($errors['prenom'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['prenom']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email" class="required">Adresse email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="telephone">Numéro de téléphone (optionnel)</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone ?? '') ?>">
            <?php if (!empty($errors['telephone'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['telephone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password" class="required">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <?php if (!empty($errors['password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="required">Confirmez le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <?php if (!empty($errors['confirm_password'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit">S'inscrire</button>
    </form>

    <div class="login-link">
        Déjà un compte ? <a href="connexion.php">Connectez-vous</a>
    </div>
</div>
</body>
</html>
