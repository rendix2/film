<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 14. 9. 2016
	 * Time: 10:48
	 */
	class Database {

		private $connection;
		private $statement;
		private $serverName;

		public function __construct ( $server, $userName, $userPassword, $databaseName ) {
			$this->serverName = $server;

			$options = [ \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' ];

			try {
				$this->connection = new \PDO( 'mysql:host=' . $server . ';dbname=' . $databaseName . ';charser=utf8',
				$userName, $userPassword, $options );
				$this->connection->setAttribute ( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			} catch ( \PDOException $pdoEx ) {
				$this->checkConnection ( $pdoEx );
			}
		}

		public function __destruct () {
			$this->connection = NULL;
			$this->statement  = NULL;
			$this->serverName = NULL;
		}

		protected final function checkConnection ( \PDOException $pdoEx = NULL ) {
			//if ( $pdoEx == NULL ) throw new NullPointerException();

			$message = '';

			switch ( $pdoEx->getCode () ) {
				case 1045:
					$message .= 'Nesprávné údaje pro přihlášení k databázovému serveru: <b>' . $this->serverName . '</b><br>';
					break;
				case 2002:
					$message .= 'Nepodařilo se připojit k databázovému serveru: <b>' . $this->serverName . '</b><br>';
					break;
				case 1044:
					$message .= 'Nepodařilo se vybrat databázi na databázovém serveru: <b>' . $this->serverName . '</b><br>';
					break;
				default:
					$message .= 'Neočekávaná PDO chyba číslo: <b>' . $pdoEx->getCode () .
					'</b> při připojení k databázovému serveru: <b>' . $this->serverName . '</b><br>';
			}

			die( $message );
		}

		public final function fetch () {
			return $this->statement->fetch ( \PDO::FETCH_ASSOC );
		}

		public final function fetchAll () {
			return $this->statement->fetchAll ( \PDO::FETCH_ASSOC );
		}

		public final function fetchColumn () {
			return $this->statement->fetchColumn ( \PDO::FETCH_ASSOC );
		}

		public final function free () {
			$this->statement->closeCursor ();
			$this->statement = NULL;
		}

		public final function numRows () {
			return $this->statement->rowCount ();
		}

		public final function query ( $statement, array $options = [ ] ) {
			try {
				$this->statement = $this->connection->prepare ( $statement );
				$this->statement->execute ( $options );
			} catch ( PDOException $pdoEx ) {
				$message = 'Databázová chyba!<br>';
				$message .= 'Chyba číslo: ' . $pdoEx->getCode () . '<br>';
				$message .= 'Chyba: ' . $pdoEx->getMessage () . '<br>';

				die( $message );
			}
		}
	}