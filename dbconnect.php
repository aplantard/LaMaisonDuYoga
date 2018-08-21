<?php
	$config = parse_ini_file('../../db_config.ini');
	$PARAM_hote = $config['host'];
	$PARAM_nom_bd = $config['dbname'];
	$PARAM_utilisateur = $config['username'];
	$PARAM_mdp = $config['password'];

	try
	{
		$connexion = new PDO('mysql:host='.$PARAM_hote.';
		dbname='.$PARAM_nom_bd, $PARAM_utilisateur, $PARAM_mdp);
	}
	//gestion des erreurs
	catch(Exception $e)
	{
		echo 'Erreur : '.$e->getMessage().'<br />';
		echo 'N° : '.$e->getCode();
		die;
	}

?>