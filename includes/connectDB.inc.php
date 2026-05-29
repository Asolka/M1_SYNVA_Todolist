<?php
session_start();

require_once("auth.php"); //Pour des raisons de sécurité, les logs de connexion à la BDD sont dans un fichier à part, puisque ce fichier sera déposé sur github. 

    //Connexion à la BDD
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $link=mysqli_connect($host, $user, $passwd, $db);
    $link->set_charset('utf8');
} catch (mysqli_sql_exception $e) {
    echo "MySQLi Error Code: " . $e->getCode() . "<br />";
    echo "Exception Msg: " . $e->getMessage();
    exit();
}

    //Fonction de sécurité pour les variables de session
function random_pw($pw_length) {
    $pass = NULL;
    $charlist = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz023456789';
    $ps_len = strlen($charlist);

    for($i = 0; $i < $pw_length; $i++) {
        $pass .= $charlist[mt_rand(0, $ps_len - 1)];
    }
    return ($pass);
}
?>