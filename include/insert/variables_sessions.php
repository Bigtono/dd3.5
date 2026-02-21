<?
// variables de session chargées dans login_check.php et connexion.php
$_SESSION['user_id']   = (int)$user['j_id'];
$_SESSION['user_nom']  = $user['j_nom'];
$_SESSION['pseudo']  = $user['j_pseudo'];
$_SESSION['user_mail'] = $user['j_email'];
$_SESSION['mj']= (int)$user['j_admin'];
$_SESSION['ruleset']= (int)$user['j_default_ruleset_var_id'];
$_SESSION['rulesetRep']=libvar($user['j_default_ruleset_var_id']);
$_SESSION['items_par_page']= (int)$user['j_items_par_page'];
?>