<?php
include("include/session.php");
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// deconnexion.php
if (!empty($_SESSION['user_id'])) {
    // On nettoie le token remember_me en BDD
    $sql = "UPDATE dd_joueurs
            SET j_remember_token = NULL,
                j_remember_token_expires = NULL
            WHERE j_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $_SESSION['user_id']]);
}

// Destruction de la session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();

// Suppression du remember_me
setcookie(
    'remember_me',
    '',
    [
        'expires'  => time() - 3600,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]
);

header('Location: login.php');
exit;

?>