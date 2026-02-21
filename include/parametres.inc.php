<?

//**********************************
// paramètres généraux du site web *
//**********************************

DEFINE("TABLE_SORTS","listesort"); 
DEFINE("TABLE_CADDIE","cadie_impression"); 
DEFINE("ressourcesS","resource_livre");
DEFINE("NB_SORT",8); // nb de sorts par page

// adresse du site
$prm_url = "http://localhost/impression/";


//chemin du répertoire principal contenant le site
//$prm_main_dir = $HTTP_RACINE."/vampire/";
$prm_main_dir = $_SERVER['DOCUMENT_ROOT']."/impression/";


// séparateur du systeme
//$prm_sep = "\\";
$prm_sep = "/";


?>
