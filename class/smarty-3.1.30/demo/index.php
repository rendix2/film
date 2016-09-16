<?php
	/**
	 * Example Application
	 * @package Example-application
	 */

require '../libs/Smarty.class.php';

	$smarty = new Smarty;

//$smarty->force_compile = true;
$smarty->debugging          = TRUE;
	$smarty->caching        = TRUE;
	$smarty->cache_lifetime = 120;

	$smarty->assign ( "Name", "Fred Irving Johnathan Bradley Peppergill", TRUE );
	$smarty->assign ( "FirstName", [ "John", "Mary", "James", "Henry" ] );
	$smarty->assign ( "LastName", [ "Doe", "Smith", "Johnson", "Case" ] );
	$smarty->assign ( "Class", [ [ "A", "B", "C", "D" ], [ "E", "F", "G", "H" ], [ "I", "J", "K", "L" ],
	[ "M", "N", "O", "P" ],
	] );

	$smarty->assign ( "contacts", array (array ( "phone" => "1", "fax" => "2", "cell" => "3" ),
	array ( "phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234" ),
	) );

	$smarty->assign ( "option_values", array ( "NY", "NE", "KS", "IA", "OK", "TX" ) );
	$smarty->assign ( "option_output", [ "New York", "Nebraska", "Kansas", "Iowa", "Oklahoma", "Texas" ] );
	$smarty->assign ( "option_selected", "NE" );

	$smarty->display ( 'index.tpl' );
