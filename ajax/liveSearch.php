<?php
	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 15. 9. 2016
	 * Time: 19:48
	 */

	error_reporting ( E_ALL );

	require_once ( '../class/Starter.php' );

	$starter = Starter::start ();
	$db      = new Database( 'innodb.endora.cz', 'film', 'Siemens1', 'film' );
	$smarty  = new MySmarty();
	$movie   = new Movie( $db, $smarty );

	$movie->liveRealSearch ();