<?
/* STRICTEMENT la base de données, RIEN d’autre */

ini_set('display_errors', 0);
error_reporting(0);

/* connexion PDO uniquement */
global $db;
if ($_SERVER['SERVER_NAME']=="maikastel.fr"):
  $user='maikasteiymaika';
  $pass='Mai150222290858';
  $dsn='mysql:host=maikasteiymaika.mysql.db;dbname=maikasteiymaika';
  $site="maikastel.fr";
  else:
  $user='maikasteiymaika';
  $pass='Mai15022228';
  $dsn='mysql:host=maikasteiymaika.mysql.db;dbname=maikasteiymaika';
  $site="maikastel.fr";
endif;
try {
    $db = new PDO(
        $dsn,
        $user,
        $pass,
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        )
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // si tu veux, tu peux aussi fixer le fetch mode par défaut :
    // $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg = 'ERREUR connexion PDO (' . $dsn . ') dans ' . $e->getFile()
         . ' Ligne ' . $e->getLine() . ' : ' . $e->getMessage();
    die($msg);
}
?>