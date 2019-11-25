<?php
if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {
    //variable sécurisé
    $secret = htmlspecialchars($_COOKIE['auth']);

    //vérifier si le code secret appartient à un compte
    require('src/connexion.php');

    $req = $db->prepare("SELECT count(*) AS numberAccount FROM user WHERE secret = ?");
    $req->execute(array($secret));

    while ($user = $req->fetch()) {

        if ($user['numberAccount'] == 1) {
            $reqUser = $db->prepare("SELECT * FROM user WHERE secret = ?");
            $reqUser->execute(array($secret));

            while ($userAccount = $reqUser->fetch()) {

                $_SESSION['connect'] = 1;
                $_SESSION['email'] = $userAccount['email'];
            }
        }
    }
}
//bloquer un utilisateur malvaillant
if (isset($_SESSION['connect'])) {

    require('src/connexion.php');

    $reqUser = $db->prepare("SELECT * FROM user WHERE email = ?");
    $reqUser->execute(array($_SESSION['email']));

    while ($userAccount = $reqUser->fetch()) {

        if ($userAccount['blocked'] == 1) {
            header('location: logout.php');
            exit();
        }
    }
}
