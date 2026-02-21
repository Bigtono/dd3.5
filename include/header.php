  <header>
    <div class="dflex clearfix">
      <button type="button" id="toggleMenu" class="toggle_menu">
        <i class="fa fa-bars txt-white"></i>
      </button>
      <h1><a href="http://<? echo $_SESSION['url_site']; ?>" class="lien txt-white"><? echo $_SESSION['titre_site']; ?></a></h1>
      <form action="recherche.php" method="get" name="recherche" id="recherche">
        <input type="text" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)"/>
        <button id="fa-search"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
      <div id="bouton_recherche"><div onClick="toggle('menu_recherche2')"><i class="fa fa-search" aria-hidden="true"></i></div></div>
      <div><span class="ruleset"><? echo libvar($_SESSION['ruleset']); ?></span></div>
    </div>
    <div id="menu_recherche2" class="search-container">
      <form action="recherche.php" method="get" name="recherche2" id="recherche2" class="search-form">
        <input type="text" class="search-input" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)"/>
        <button id="fa-search2" class="search-button"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
    </div>
  </header>
