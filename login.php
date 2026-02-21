<?
// login.php
include("include/session.php");
include("include/dblib.inc.php");

// Si l'utilisateur est déjà connecté, on peut le rediriger
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Message d'erreur éventuel
$error = $_GET['error'] ?? null;
$redirect = $_GET['redirect'] ?? 'index.php';
?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
</HEAD>

<body>
  <div id="page">
  	<? include("include/header.php"); ?>
    <div class="wrapper">
      <? if ($error): ?>
          <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
      <? endif; ?>

      <form action="login_check.php" method="post" class="login">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        
        <label for="email" class="label">Email</label>
        <input type="email" name="email" id="email" required>
        
        <label for="password" class="label">Mot de passe</label>
        <input type="password" name="password" id="password" required class="w200">
        
        <div class="mb10">
          <label class="label"><input type="checkbox" name="remember" value="1"> Se souvenir de moi</label>
        </div>
        
        <button type="submit" class="btNoir">Connexion</button>
      </form>      
      
    </div>
  </div><!-- page --->
</body>
