<?php
// login_check.php
include("include/session.php");
include("include/dblib.inc.php");

/* V2 - version intermédiaire OK

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = !empty($_POST['remember']);
$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

if ($email === '' || $password === '') {
    header('Location: login.php?error=Champs+incomplets&redirect=' . urlencode($redirect));
    exit;
}

// Récupérer le joueur par email
$sql = "SELECT * FROM dd_joueurs WHERE j_email = :email LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['j_password_hash'])) {
    header('Location: login.php?error=Email+ou+mot+de+passe+incorrect&redirect=' . urlencode($redirect));
    exit;
}

// Mot de passe OK -> connexion
session_regenerate_id(true); // important pour éviter le vol de session

$_SESSION['user_id']   = (int)$user['j_id'];
$_SESSION['user_nom']  = $user['j_nom'];
$_SESSION['pseudo']  = $user['j_pseudo'];
$_SESSION['user_mail'] = $user['j_email'];
$_SESSION['mj']= (int)$user['j_admin'];
$_SESSION['ruleset']= (int)$user['j_default_ruleset_var_id'];

// Option "se souvenir de moi"
if ($remember) {
    // Token aléatoire
    $token = bin2hex(random_bytes(32));

    // On stocke un hash du token en BDD
    $tokenHash = hash('sha256', $token);

    // Expiration dans 30 jours
    $expires = (new DateTime('+30 days'))->format('Y-m-d H:i:s');

    $sql = "UPDATE dd_joueurs
            SET j_remember_token = :token, j_remember_token_expires = :expires
            WHERE j_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':token'   => $tokenHash,
        ':expires' => $expires,
        ':id'      => $user['j_id'],
    ]);

    // Cookie : contient id + token (pas hashé), séparés par ":"
    $cookieValue = $user['j_id'] . ':' . $token;

    setcookie(
        'remember_me',
        $cookieValue,
        [
            'expires'  => time() + 60 * 60 * 24 * 30, // 30 jours
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

header('Location: ' . $redirect);
exit;
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = !empty($_POST['remember']);
$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

if ($email === '' || $password === '') {
    header('Location: login.php?error=Champs+incomplets&redirect=' . urlencode($redirect));
    exit;
}

// Récupération utilisateur
$sql = "SELECT * FROM dd_joueurs WHERE j_email = :email LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['j_password_hash'])) {
    header('Location: login.php?error=Email+ou+mot+de+passe+incorrect&redirect=' . urlencode($redirect));
    exit;
}

// Connexion OK
session_regenerate_id(true);

// création des variables de session
include('include/insert/'.$_SESSION['rulesetRep'].'/variables_sessions.php');

// Mise à jour de la dernière connexion
$sql = "UPDATE dd_joueurs
        SET j_derniere_connexion = NOW()
        WHERE j_id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $user['j_id']]);

// Gestion du remember_me
if ($remember) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expires   = (new DateTime('+30 days'))->format('Y-m-d H:i:s');

    $sql = "UPDATE dd_joueurs
            SET j_remember_token = :token,
                j_remember_token_expires = :expires
            WHERE j_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':token'   => $tokenHash,
        ':expires' => $expires,
        ':id'      => $user['j_id'],
    ]);

    $cookieValue = $user['j_id'] . ':' . $token;

    setcookie(
        'remember_me',
        $cookieValue,
        [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => '/',
            'domain'   => '', // IMPORTANT : même réglage que dans auth.php
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
} else {
    // Si la case n'est pas cochée, on supprime un éventuel ancien cookie
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
}

header('Location: ' . $redirect);
exit;

?>