<?php

class Tradicional implements Layout{
	private $enlaces = 10;
	public function __construct($numEnlaces = 10){
            //$this->enlaces = $numEnlaces;
            $this->enlaces = 10;
	}
	
	public function mostrar($parent, $queryVars){
		$pag = $parent->getCurrPage();
		$totPages = $parent->getTotalPages();

?>
<ul class="pagination clear">
<?php
		/**
		 * Primera Pagina
		 */
                //if($parent->getActionController()){$actionController = "?actionController=".$parent->getActionController();}
		if($pag <= 1):
?>			<li class="actual"><a href="<?php echo $_SERVER['PHP_SELF'] ?>?<?php echo $queryVars?>pag=<?php echo 1 ?>"><?php echo 1 ?></a></li>
<?php		/** ENLACES POSTERIORES.
			* Estoy en la 1, comienzo de 2 pero solo si existe almenos una 2 pagina.
			* Muestro tantos enlaces esten especificados le sumo 2 por que de ahi
			* comence a contar...
			* **/
			for($i = 2; $i <= $totPages && $i < $this->enlaces+2; $i++): 
?>			<li class="afterLinks"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $i ?>"><?php echo $i?></a></li>
<?php		endfor;
			
			if($pag < $totPages - $this->enlaces):	//[...] Existe mas paginas adelante. (tot_pags - enlaces = numero de pagina minimo para mostrar enlaces hasta la ultima pagina)
?>			<!--<li class="morePages">&hellip;</li>-->
<?php		endif;

			if($totPages != 1):	//Si existe solo una pagina, no muestro enlaces hacia adelante... me salto este if
?>			<li class="nextPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php	echo $queryVars?>pag=<?php echo $pag+1?>">Next &rsaquo;</a></li>
			<li class="lastPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $totPages?>">Last &raquo;</a></li>
<?php		endif;
		elseif($pag >= $totPages):	//##############################################	LAST PAGE. Y existe mas de 1 pagina.
?>			<li class="firstPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=1">&laquo; First</a></li>
			<li class="previousPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $pag-1?>">&lsaquo; Previous</a></li>
<?php
			if($pag > $this->enlaces+1):	//El numero de enlaces no es suficiente para mostrar hasta la primera pagina... le falta almenos 1
?>			<!--<li class="morePages">&hellip;</li>-->
<?php		endif;
			
			$befLinks_LastP = $totPages - $this->enlaces;
			$befLinks_LastP = ($befLinks_LastP <= 0) ? 1:$befLinks_LastP;
			
			for($i = $befLinks_LastP; $i < $totPages; $i++):
?>			<li class="beforeLinks"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $i?>"><?php echo $i?></a></li>
<?php		endfor;
?>			<li class="actual"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $pag?>"><?php echo $pag;?></a></li>
<?php
		else:	//##############################	AFTER THAN FISRT AND BEFORE THAN LAST PAGE
?>			<li class="firstPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=1">&laquo; First</a></li>
			<li class="previousPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $pag-1?>">&lsaquo; Previous</a></li>
<?php
			if($pag > $this->enlaces+1):
?>			<!--<li class="morePages">&hellip;</li>-->
<?php		endif;
			
			$j = 0;
			for($i = $pag - $this->enlaces;$i < $pag && $j <= $this->enlaces; $i++):
				$j++;
				if($i > 0):
?>				<li class="beforeLinks"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $i?>"><?php echo $i?></a></li>
<?php			endif;
			endfor;
?>			<li class="actual"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $pag?>"><?php echo $pag;?></a></li>
<?php
			$j = 0;
			for($i = $pag + 1; $i <= $totPages && $j < $this->enlaces; $i++):
				$j++;
?>				<li class="afterLinks"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $i?>"><?php echo $i?></a></li>
<?php		endfor;
			
			if($pag < $totPages-$this->enlaces):
?>			<!--<li class="morePages">&hellip;</li>-->
<?php		endif;
			
?>			<li class="nextPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $pag+1?>">Next &rsaquo;</a></li>
			<li class="lastPage"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $queryVars?>pag=<?php echo $totPages?>">Last &raquo;</a></li>
<?php	endif;
?>
</ul>
<?php
	}	//end mostrar
}