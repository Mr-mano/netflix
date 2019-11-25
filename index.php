<?php
session_start();
require('log.php');


if (!empty($_POST['email']) && !empty($_POST['password'])) {

	require('src/connexion.php');

	//déclaration des variables
	$email = htmlspecialchars($_POST['email']);
	$password = htmlspecialchars($_POST['password']);
	$error = 1;

	// vérifier si email est valide
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: ../netflix?error=1&message=Votre adresse email est invalide.');
		exit();
	}

	//HASH (identifiant secret)
	$secret = sha1($email) . time();
	$secret = sha1($secret) . time();

	//cryptage du password
	$password = "aq1" . sha1($password . "1254") . "25";

	//vérifier si l'email est déjà utilisé
	$req = $db->prepare('SELECT * FROM user WHERE email = ?');
	$req->execute(array($email));

	while ($user = $req->fetch()) {

		if ($password == $user['password']) {
			//$error = 0;
			$_SESSION['connect'] = 1;
			$_SESSION['email'] = $user['email'];

			//gestion connexion automatique(checbox)
			if (isset($_POST['auto'])) {
				//création du cookie(1 an)
				setcookie(
					'auth',
					$user['secret'],
					time() + 365 * 24 * 3600,
					'/',
					null,
					false,
					true
				);
			}
			header('location: ../netflix?true=1');
			exit();
		} else {
			header('location: ../netflix?error=1&message= Email ou mot de passe incorrect!');
		}
	}

	/*if ($error == 1) {
		header('location: ../netflix?error=1&message= Email ou mot de passe incorrect!');
		exit();
	}*/
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
			<!--Si l'utilisateur est connecté afiiche ça :-->
			<?php if (isset($_SESSION['connect'])) { ?>

				<h1>Bonjour !</h1>
				<p>Vous pouvez sélectionner vos séries</p>
				<small><a href="logout.php">Déconnexion</a></small>

				<!--sinon afiiche ça :-->
			<?php } else { ?>


				<h1>S'identifier</h1>
				<?php
					if (isset($_GET['error'])) {
						if (isset($_GET['message'])) {
							echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
						}
					} else if (isset($_GET['true'])) {
						echo '<p class="alert success">Vous êtes connecté.</p>';
					}
					?>
				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>


				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>