	
	
	function change(liste){
		id=liste.options[liste.options.selectedIndex].value;
		contentFilm=lancer_script("tpl_serie.php?id="+id,false);
		$('content_left').set('html',contentFilm);
		document.getElementById('body').style.background="url('"+x[id]+"') TOP CENTER";
		document.getElementById('principale3').style.marginTop='0px';
		hauteur = parseInt(window.innerHeight);	
		hauteur=hauteur - parseInt(document.getElementById('entete').offsetHeight);
		hauteur = hauteur - parseInt(document.getElementById('table_principale').offsetHeight)+46;
		if(hauteur>0){document.getElementById('principale3').style.marginTop=hauteur+'px';}
	}
	
	
	function rep_serie(){
		var repertoire=document.getElementById('rep').value;
		document.location.href="dossier_serie.php?dossier="+repertoire;	
	}
	
	
	function maj_serie(id){
		fichier="classes/Filmotheque.class.php?class=serie&fonction=maj_serie&nb_var=1&var0="+id;
		lancer_script(fichier,true);
	}
	
	function maj_all_serie(id){
		fichier="classes/Filmotheque.class.php?class=serie&fonction=maj_all_serie";
		lancer_script(fichier,true);
	}





