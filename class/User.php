<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 14. 9. 2016
	 * Time: 11:54
	 */
	final class User {

		private $database;
		private $smarty;

		public function __construct ( Database $database, MySmarty $smarty ) {
			$this->database = $database;
			$this->smarty   = $smarty;
		}

		public function login () {
			if ( $_SESSION[ 'logged' ] ) myExit ( $this->smarty, 'Jsi přihlášen' );

			$this->smarty->display ( 'userLogin', $_POST[ 'user_name' ], 'user_name' );

			if ( isset( $_POST[ 'submit' ] ) ) {
				$errors = [ ];

				if ( empty( $_POST[ 'user_name' ] ) )
					$errors[] = 'Prázdné uživatelské jméno';
				if ( empty( $_POST[ 'user_password' ] ) )
					$errors[] = 'Prázdné uživatelské heslo';
				if ( count ( $errors ) != 0 )
					Starter::myExit ( $this->smarty, implode ( '<br>', $errors ) );

				$this->database->query ( 'SELECT user_name, user_password FROM users WHERE user_name = :user_name LIMIT 1;',
				[ 'user_name' => $_POST[ 'user_name' ] ] );

				if ( !$this->database->numRows () == 1 )
					echo 'Učet nenalezen';

				$data = $this->database->fetch ();

				if (
				$data[ 'user_name' ] === $_POST[ 'user_name' ] &&
				$data[ 'user_password' ] === hash ( 'sha512', $_POST[ 'user_password' ] )
				)
					$_SESSION[ 'logged' ] = TRUE;
				else {
					$_SESSION[ 'logged' ] = FALSE;
					echo 'Přihlášení se nezdařilo';
				}
			}
		}

		public function logout () {
			if ( !$_SESSION[ 'logged' ] ) myExit ( $this->smarty, 'Nejsi přihlášen' );

			session_destroy ();
		}

		public function register () {
			if ( $_SESSION[ 'logged' ] ) myExit ( $this->smarty, 'Jsi přihlášen' );

			$this->smarty->display ( 'userRegister', $_POST[ 'user_name' ], 'user_name' );

			if ( isset( $_POST[ 'submit' ] ) ) {
				$errors = [ ];

				if ( empty( $_POST[ 'user_name' ] ) )
					$errors[] = 'Prázdné uživatelské jméno';
				else {

					if ( mb_strlen ( $_POST[ 'user_name' ], Starter::UTF8 ) < 8 )
						$errors[] = 'Krátké uživatelské jméno';

					if ( mb_strlen ( $_POST[ 'user_name' ], Starter::UTF8 ) > 100 )
						$errors[] = 'Dlouhé uživatelské jméno';

					$this->database->query ( 'SELECT 1 FROM users WHERE user_name = :user_name LIMIT 1;',
					[ 'user_name' => $_POST[ 'user_name' ] ] );

					if ( $this->database->numRows () == 1 )
						$errors[] = 'Uživatelské jméno je již zaregistrované';
				}

				if ( empty( $_POST[ 'user_password' ] ) )
					$errors[] = 'Prázdné uživatelské jméno';
				else {

					if ( mb_strlen ( $_POST[ 'user_password' ], Starter::UTF8 ) < 8 )
						$errors[] = 'Krátké uživatelské heslo';

					if ( mb_strlen ( $_POST[ 'user_password' ], Starter::UTF8 ) > 100 )
						$errors[] = 'Dlouhé uživatelské heslo';
				}

				if ( empty( $_POST[ 'user_password_check' ] ) )
					$errors[] = 'Prázdné uživatelské heslo pro kontrolu';
				else {

					if ( mb_strlen ( $_POST[ 'user_password_check' ], Starter::UTF8 ) < 8 )
						$errors[] = 'Krátké uživatelské heslo pro kontrolu';

					if ( mb_strlen ( $_POST[ 'user_password_check' ], Starter::UTF8 ) > 100 )
						$errors[] = 'Dlouhé uživatelské heslo pro kontrolu';
				}

				if ( !( $_POST[ 'user_password' ] === $_POST[ 'user_password_check' ] ) )
					$errors[] = 'Hesla nejsou stejná';

				if ( $_POST[ 'user_password' ] === $_POST[ 'user_name' ] )
					$errors[] = 'Shodné jméno a heslo';

				if ( count ( $errors ) != 0 ) {
					myExit ( $this->smarty, implode ( '<br>', $errors ) );
				}

				$this->database->query ( 'INSERT INTO users (user_name, user_password) VALUES(:user_name,
					:user_password);', [ 'user_name' => $_POST[ 'user_name' ], 'user_password' => hash ( 'sha512',
				$_POST[ 'user_password' ] ),
				] );

				if ( $this->database->numRows () == 1 )
					echo 'Registrace byla úspěšná';
				else
					echo 'Registrace se nezdařila';
			}
		}

		public function update () {

		}
	}