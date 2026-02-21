<?
// 1. Si déjà en session, OK
if (!empty($_SESSION['user_id'])) {
    return;
}

// 2. Sinon, on tente "se souvenir de moi"
if (!empty($_COOKIE['remember_me'])) {

    [$userId, $token] = explode(':', $_COOKIE['remember_me'], 2) + [null, null];

    if ($userId && $token) {
        $sql = "SELECT * FROM dd_joueurs WHERE j_id = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => (int)$userId]);
        $user = $stmt->fetch();

        if ($user && $user['j_remember_token']) {
            $tokenHash = hash('sha256', $token);
            $now = new DateTime();

            if (
                hash_equals($user['j_remember_token'], $tokenHash) &&
                $user['j_remember_token_expires'] &&
                $now <= new DateTime($user['j_remember_token_expires'])
            ) {
                // Token valide -> recréation de la session
                session_regenerate_id(true);
                include('include/insert/variables_sessions.php');
                return;
            }
        }
    }

    // Token invalide -> suppression du cookie (même path/domain que setcookie dans login_check)
    setcookie(
        'remember_me',
        '',
        [
            'expires'  => time() - 3600,
            'path'     => '/',
            'domain'   => '', // même choix que dans login_check
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

// 3. Toujours pas connecté -> redirection login
$currentUrl = $_SERVER['REQUEST_URI'] ?? 'index.php';
header('Location: login.php?redirect=' . urlencode($currentUrl));
exit;

?>