	function acteur_film(){
		var acteur=document.getElementById('acteur').value;
		if(acteur==""){alert("l'acteur n'a pas été renseigné!!");return;}
		else{
			document.location.href="index.php?acteur="+acteur;
		}	
	}
	
	function film(){
		var film=document.getElementById('film').value;
		if(film==""){alert("le film n'a pas été renseigné!!");return;}
		else{
			document.location.href="index.php?titre="+film;
		}	
	}	
	function change(liste){
		id=liste.options[liste.options.selectedIndex].value;
		contentFilm=lancer_script("tpl_film.php?id="+id,false);
		$('content_left').set('html',contentFilm);
		document.getElementById('body').style.background="url('"+x[id]+"') TOP CENTER";
		document.getElementById('principale3').style.marginTop='0px';
		hauteur = parseInt(window.innerHeight);	
		hauteur=hauteur - parseInt(document.getElementById('entete').offsetHeight);
		hauteur = hauteur - parseInt(document.getElementById('table_principale').offsetHeight)+46;
		if(hauteur>0){document.getElementById('principale3').style.marginTop=hauteur+'px';}
	}
	
	
	function change_genre(liste){
		var retour_liste ="";
		id_genre=liste.options[liste.options.selectedIndex].value;
		fichier="classes/Filmotheque.class.php?class=genre&fonction=display_film_genre&nb_var=1&var0="+id_genre;
		retour_liste=lancer_script(fichier,false);
		sel_rep=document.getElementById('select_principale');
		sel_rep.innerHTML=retour_liste;
		change(sel_rep);
	}

	
	function modifier_film_id(j,id){
		id_allocine=document.getElementById('new_id_allocine_'+j).value;
		fichier="classes/Filmotheque.class.php?class=film&fonction=modifier_film&nb_var=2&var0="+id_allocine+"&var1="+id;
		lancer_script(fichier,true);
	}
	
	function maj_fanart(j,id){
		lien=document.getElementById('new_fanart_'+j).value;
		fichier="classes/Filmotheque.class.php?class=images&fonction=maj_fanart_film&nb_var=2&var0="+id+"&var1="+lien;
		lancer_script(fichier,true);
	}
	
	
	
	function suppr(id){
		if(confirm('etes vous sur de supprimer ce film')){
			fichier="classes/Filmotheque.class.php?class=film&fonction=delete_film&nb_var=2&var0="+id+"&var1=true";
			lancer_script(fichier,true);
			
		}
	}
	
	
	function supprimer_lien(lien){
		if(confirm('etes vous sur de supprimer ce fichier')){
			fichier="classes/Filmotheque.class.php?class=liens&fonction=delete_lien_film&nb_var=2&var0="+lien+"&var1=true";
			lancer_script(fichier,true);
			
		}
	}
	
	function voter_interet(id,value){
		fichier="classes/Filmotheque.class.php?class=film&fonction=modifier_interet_film&nb_var=2&var0="+id+"&var1="+value;
		lancer_script(fichier,true);
		for(i=1;i<=value;i++){
			document.getElementById('etoile_'+id+'_'+i).src='images/etoile_pleine.png';
		}
		j=parseInt(value)+1;
		for(i=j;i<=5;i++){
			document.getElementById('etoile_'+id+'_'+i).src='images/etoile_vide.png';
		}

	}
	
	function voter_qualite(id,liste){
		value=liste.options[liste.options.selectedIndex].value;
		fichier="classes/Filmotheque.class.php?class=liens&fonction=modifer_qualite_lien&nb_var=2&var0="+id+"&var1="+value;
		lancer_script(fichier,true);
	}
	
	
	


