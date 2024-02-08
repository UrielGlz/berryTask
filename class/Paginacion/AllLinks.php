<?php

include_once 'Layout.php';

class AllLinks implements Layout{
	
	public function mostrar($parent, $queryVars){
		$totPages = $parent->getTotalPages();
		$currPage = $parent->getCurrPage();
		
		if($totPages >= 1):
		
			for($i = 1; $i <= $totPages; $i++) :
				if($i != $currPage):
?>				<a href="?<?php echo $queryVars ?>pag=<?php echo $i ?>">Page <?php echo $i ?></a>
<?php
				echo ($i != $totPages) ? " | " : "";
				else:
?>				<span>Page<?php echo $i ?></span>
<?php
				echo ($i != $totPages) ? " | " : "";
				endif;
			endfor;
		endif;
	}
}
?>