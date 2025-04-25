<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'connexion.php';

$errors = [];
$email = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Validation
    if (empty($email)) {
        $errors['email'] = "Veuillez entrer votre adresse email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide.";
    }

    if (empty($password)) {
        $errors['password'] = "Veuillez entrer votre mot de passe.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE adresse_mail = :email");
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur && password_verify($password, $utilisateur["mot_de_passe"])) {
                $_SESSION['utilisateur_id'] = $utilisateur["id_utilisateur"];
                $_SESSION['prenom'] = $utilisateur["prenom"];
                $_SESSION['nom'] = $utilisateur["nom"];
                $_SESSION['email'] = $utilisateur["email"];

                header("Location: Home.php");
                exit;
            } else {
                $errors['global'] = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $errors['global'] = "Erreur serveur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - ShopTonMaillot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="connexion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
   
</head>
<body>

<?php include('navbar.php'); ?>

<div class="login-container">
    <div class="container">
        <div class="login-logo">
            <h1>ShopTonMaillot</h1>
            <p>Connectez-vous à votre compte</p>
        </div>

        <!-- Erreur globale -->
        <?php if (!empty($errors['global'])): ?>
            <div class="error-block">
                <p><?= htmlspecialchars($errors['global']) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Champ Email avec erreur spécifique -->
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($email) ?>"
                       class="<?= !empty($errors['email']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Champ Mot de passe avec erreur spécifique -->
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required
                       class="<?= !empty($errors['password']) ? 'is-invalid' : '' ?>">
                <?php if (!empty($errors['password'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>
                <div class="forgot-password">
                    <a href="#">Mot de passe oublié ?</a>
                </div>
            </div>

            <button type="submit" class="btn-login">Se connecter</button>

            <div class="register-link">
                Vous n'avez pas de compte ? <a href="inscription.php">S'inscrire</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>