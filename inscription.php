<?php
session_start();
require('log.php');

//si l'utilisateur est connecté on n'affiche pas la page inscription
if(isset($_SESSION['connect'])) {
    header('location: ../netflix');
    exit();
} 

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {

	/*le require est placé dans la condition pour faire un appel BD seulement lorsque l'on en a besoin(allége le code)*/
	require('src/connexion.php');

	//déclaration des variables
	$email = htmlspecialchars($_POST['email']);
	$password = htmlspecialchars($_POST['password']);
	$password_two = htmlspecialchars($_POST['password_two']);

	if ($password != $password_two) {
		header('location: ../netflix/inscription.php?error=1&message= Vos mots de passe ne sont pas identiques');
		exit();
	}
	// ADRESSE EMAIL SYNTAXE
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: ../netflix/inscription.php?error=1&message=Votre adresse email est invalide.');
		exit();
	}

	//vérifier si l'email exist déjà
    $req = $db->prepare("SELECT COUNT(*) AS email FROM user WHERE email = ?");
    $req->execute(array($email));
    while ($verif_email = $req->fetch()) {
        if ($verif_email['email'] != 0) {
            header('location: ../netflix/inscription.php?error=1&message=Votre adresse email est déjà utilisé.');
            exit();
        }
    }

	 //HASH (identifiant secret)
	 $secret = sha1($email) . time();
	 $secret = sha1($secret) . time();

	 //cryptage du password
	 $password = "aq1" . sha1($password . "1254") . "25";

	  //envoie de le requete en base de données
	  $req = $db->prepare('INSERT INTO user(email, password, secret) VALUES(?, ?, ?)');
	  $req->execute(array($email, $password, $secret));
	  header('location: ../netflix/inscription.php?true=1');
	  exit();
}

?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>

<body>

	<?php include('src/header.php'); ?>

	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>
			<?php
			if (isset($_GET['error'])) {
            echo '<p class="error">Email ou mot de passe incorrect.</p>';
        } else if (isset($_GET['true'])) {
            echo '<p class="true">Vous êtes connecté.</p>';
        }
        ?>
			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>