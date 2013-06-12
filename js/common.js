	function afficher(name){alert(name);
		fichier="classes/Filmotheque.class.php?fonction=afficher_lien_finder&nb_var=1&var0="+name;
		lancer_script(fichier,true);
	}
	
	function lancer_script(fichier,bool){
		if(window.XMLHttpRequest) // FIREFOX
		xhr_object = new XMLHttpRequest();
		else if(window.ActiveXObject) // IE
		xhr_object = new ActiveXObject("Msxml2.XMLHTTP");
		else
		return(false);
		xhr_object.open("GET", fichier, bool);
		xhr_object.send(null);
		if(xhr_object.readyState == 4) return(xhr_object.responseText);
		else return(false);
	}
	
	function rep_film(){
		var repertoire=document.getElementById('rep').value;
		document.location.href="dossier.php?dossier="+repertoire;
	}
	function acteur(){
		var acteur=document.getElementById('acteur').value;
		if(acteur==""){alert("l'acteur n'a pas été renseigné!!");return;}
		else{
			document.location.href="fiche_acteur_recherche.php?acteur="+acteur;
		}	
	}	
	function chargement(){
		sel_rep=document.getElementById('select_principale');
		change(sel_rep);
	}

	function lancer_video(lien){
		fichier="classes/Filmotheque.class.php?class=fichier&fonction=lancer_video&nb_var=1&var0="+lien;
		retour_liste=lancer_script(fichier,true);
	}		
	function ouvrir(){
		document.getElementById('login').style.overflow='visible';
		document.getElementById('container').style.display='none';
		hauteur=parseInt(document.getElementById('principale3').style.marginTop)+38;
		document.getElementById('principale3').style.marginTop=hauteur+'px';
		
	}
	
	function fermer(){
		document.getElementById('login').style.overflow='hidden';
		document.getElementById('container').style.display='inline';
		hauteur=parseInt(document.getElementById('principale3').style.marginTop)-38;
		document.getElementById('principale3').style.marginTop=hauteur+'px';
	}
	