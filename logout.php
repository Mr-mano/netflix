<?php
session_start(); /*initialise la session*/
session_unset(); /*desactive la session*/
session_destroy(); /* détruire la session*/
//détruire le cookie (-1 = chiffre négatif pour périmer le cookie)
setcookie('auth', '', time()-1, '/', null, false, true);
header('location: ../netflix');
        exit();