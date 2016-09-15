<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 14. 9. 2016
	 * Time: 11:00
	 */
	final class Starter {

		const UTF8 = 'UTF-8';
		private static $starter = NULL;

		private function __construct () {
			require ( __DIR__ . '/Database.php' );
			require ( __DIR__ . '/User.php' );
			require ( __DIR__ . '/smarty-3.1.30/libs/Smarty.class.php' );
			require ( __DIR__ . '/MySmarty.php' );
			require ( __DIR__ . '/Movie.php' );
			require ( __DIR__ . '/simple_html_dom.php' );

			session_start ();
			self::checks ();
		}

		private static function checks () {
			$_GET[ 'akce' ]       = isset( $_GET[ 'akce' ] ) ? $_GET[ 'akce' ] : '';
			$_SESSION[ 'logged' ] = isset( $_SESSION[ 'logged' ] ) ? $_SESSION[ 'logged' ] : '';
			$_POST[ 'user_name' ] = isset( $_POST[ 'user_name' ] ) ? $_POST[ 'user_name' ] : '';
			$_POST[ 'csfdLink' ]  = isset( $_POST[ 'csfdLink' ] ) ? $_POST[ 'csfdLink' ] : '';
			$_POST[ 'search' ]    = isset( $_POST[ 'search' ] ) ? $_POST[ 'search' ] : '';
		}

		public static function myExit ( MySmarty $smarty, $data = '' ) {
			echo $data;
			$smarty->display ( 'pageFooter', [ ] );
			exit;
		}

		public static function start () {
			return ( self::$starter === NULL ) ? new Starter() : self::$starter;
		}

	}