	<div class="block-sorts">
                  <?
									for ($i = 0; $i <= 9; $i++):
									?>
                  <div> 
                      Tous les sortil&egrave;ges de mage de niveau <? echo $i; ?>
                    	<a href="fiche_sortilege.php?v=L&amp;c=mage&amp;n=<? echo $i; ?>"><img src="graphisme/icone_pdf.gif" width="18" height="18" border="0" /></a>
                  </div>
									<? endfor; ?>
	</div>
	<div class="block-sorts">
           				<?
									for ($i = 0; $i <= 9; $i++):
									?>
                  <div>
                     Tous les sortil&egrave;ges de pretre de niveau <? echo $i; ?>
                    <a href="fiche_sortilege.php?v=L&amp;c=pretre&amp;n=<? echo $i; ?>"><img src="graphisme/icone_pdf.gif" width="18" height="18" border="0" /></a></td>
									</div>
                  <? endfor; ?>
	</div>
	<div class="block-sorts">
                  <?
									for ($i = 0; $i <= 9; $i++):
									?>
                  <div>
                      Tous les sortil&egrave;ges de druide de niveau <? echo $i; ?>
                    <a href="fiche_sortilege.php?v=L&amp;c=druide&amp;n=<? echo $i; ?>"><img src="graphisme/icone_pdf.gif" width="18" height="18" border="0" /></a></td>
                  </div>
                  <? endfor; ?>                  

	 </div>

