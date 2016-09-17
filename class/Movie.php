<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 14. 9. 2016
	 * Time: 13:16
	 */
	final class Movie {

		private $database;
		private $smarty;
		private $html;

		public function __construct ( Database $database, MySmarty $smarty ) {
			$this->database = $database;
			$this->smarty   = $smarty;
		}

		public function __destruct () {
			$this->database = NULL;
			$this->smarty   = NULL;
			$this->html     = NULL;
		}

		private static function getCsfdId ( $input ) {
			$csfdId = explode ( '/', $input );

			return explode ( '-', $csfdId[ 4 ] )[ 0 ];
		}

		private static function prepareOrderBy () {
			switch ( $_POST[ 'orderBy' ] ) {
				case 'year':
					return 'movie_year';
				case 'name':
				default:
					return 'movie_name_czech';
			}
		}

		private static function saveImage ( $address ) {
			$image     = file_get_contents ( $address );
			$imageName = md5 ( uniqid ( mt_rand (), TRUE ) );
			file_put_contents ( dirname ( __DIR__ ) . '/images/' . $imageName . '.jpg', $image );

			return $imageName;
		}

		public static function validateCsfdLink ( $address ) {
			return preg_match ( '#^(http|https):\/\/(www.){0,1}csfd\.cz\/film\/[0-9]{1,6}-[a-z-0-9]*\/prehled\/$#',
			$address );
		}

		public function add () {
			if ( !$_SESSION[ 'logged' ] ) Starter::myExit ( $this->smarty, 'Nejsi přihlášený' );

			$this->smarty->display ( 'movieAdd', $_POST[ 'csfdLink' ], 'csfdLink' );

			if ( isset( $_POST[ 'submit' ] ) ) {
				$errors = [ ];

				if ( empty( $_POST[ 'csfdLink' ] ) )
					$errors[] = 'Prázdný odkaz na csfd';

				if ( !self::validateCsfdLink ( $_POST[ 'csfdLink' ] ) )
					$errors[] = 'Odkaz není na csfd';

				if ( count ( $errors ) != 0 )
					Starter::myExit ( $this->smarty, implode ( '<br>', $errors ) );

				$this->database->query ( 'SELECT 1 FROM movies WHERE movie_csfd_id = :csfdId LIMIT 1;',
				[ 'csfdId' => self::getCsfdId ( $_POST[ 'csfdLink' ] ) ] );

				if ( $this->database->numRows () == 1 )
					$errors[] = 'Film je již přidán!';

				if ( count ( $errors ) != 0 )
					Starter::myExit ( $this->smarty, implode ( '<br>', $errors ) );

				$data = $this->getMovieInfo ();

				$this->database->query (
				'INSERT INTO movies (movie_csfd_id,movie_name_czech, movie_name_origin, movie_year, movie_picture, movie_description)
					 VALUES (:movie_csfd_id, :movie_name_czech, :movie_name_origin, :movie_year, :movie_picture,
					 :movie_description);',

				[ 'movie_csfd_id'     => self::getCsfdId ( $_POST[ 'csfdLink' ] ),
				  'movie_name_czech'  => $data[ 'cz' ],
				  'movie_name_origin' => $data[ 'origin' ],
				  'movie_year'        => $data[ 'year' ],
				  'movie_picture'     => $data[ 'image' ],
				  'movie_description' => $data[ 'desc' ],
				] );

				echo $this->database->numRows () == 1 ? 'Film přidán!' : 'Film se nepodařilo uložit';
			}
		}

		private function addRelatedMovie () {
			$this->database->query ( 'INSERT INTO related_movies (user_id, movie_id, movie_related_id) VALUES (:user_id,:movie_id,
			:movie_realted_id);',
			[ 'user_id' => $_SESSION[ 'user_id' ], 'movie_id' => $_GET[ 'movie_id' ], 'movie_related_id' =>
			$_POST[ 'movie_related_id' ],
			] );

			echo $this->database->numRows () == 1 ? 'Doporučený film uložen' : 'Doporučení se nepodařilo uložit';
		}

		private function getMovieInfo () {
			$result = [ ];

			foreach ( $this->startParseData ()->find ( 'meta' ) as $element ) {
				//echo $element->content.'<br>';

				if ( $element->name == 'description' ) {
					$result[ 'description' ] = $element->content;
					continue;
				}

				if ( $element->property == 'og:title' ) {
					$result[ 'title' ] = $element->content;
					continue;
				}

				// preg_quote
				if ( preg_match ( '#^http:\/\/img\.csfd\.cz\/files\/images\/film\/#',
				$element->content ) ) {
					$result[ 'image' ] = $element->content;
					continue;
				}
			}

			if ( preg_match ( '#\/#', preg_quote ( $result[ 'title' ], '#' ) ) ) {
				$names     = $result[ 'title' ];
				$names     = explode ( '/ ', $names );
				$czechName = ucfirst ( $names[ 0 ] );
				$engName   = $names[ 1 ];
				$tmp       = explode ( '(', $engName );
				$engName   = ucfirst ( $tmp[ 0 ] );

				// check!!!!!!
				$year  = $tmp[ 1 ];
				$year2 = substr ( $year, 0, 4 );
			} else {
				$name      = explode ( ' \|', $result[ 'title' ] );
				$name      = explode ( ' (', $name[ 0 ] );
				$names     = $name[ 0 ];
				$czechName = $names;
				$engName   = $names;

				$year2 = substr ( $name[ 1 ], 0, 4 );
			}

			$imageName = self::saveImage ( $result[ 'image' ] );

			return [ 'cz'    => $czechName, 'origin' => $engName, 'year' => $year2, 'desc' => $result[ 'description' ],
			         'image' => $imageName,
			];
		}

		public function liveRealSearch () {
			$this->prepareSearch ( $_POST[ 'liveSearch' ] );

			$this->database->query ( 'SELECT movie_name_czech, movie_picture, movie_name_origin, movie_year FROM
			movies WHERE movie_name_czech LIKE concat("%", :search, "%") OR movie_name_origin LIKE concat("%",
			:search, "%") LIMIT 6; ',
			[ 'search' => $_POST[ 'liveSearch' ] ] );

			$this->smarty->display ( 'liveMoviesView', $this->database->fetchAll () );
		}

		private function preparePagination () {

		}

		private function prepareSearch ( $input ) {
			$errors = [ ];

			if ( empty( $input ) )
				$errors[] = 'Prázné pole hledání';
			if ( mb_strlen ( $input, Starter::UTF8 ) < 3 )
				$errors[] = '';

			if ( count ( $errors ) != 0 )
				Starter::myExit ( $this->smarty, implode ( '<br>', $errors ) );
		}

		public function realSearch () {
			$this->prepareSearch ( $_POST[ 'search' ] );
			$this->database->query ( 'SELECT movie_name_czech, movie_picture, movie_name_origin, movie_year FROM movies
										  WHERE MATCH(movie_name_czech, movie_name_origin) AGAINST (:search IN
										  BOOLEAN MODE)
 										  ORDER BY MATCH(movie_name_czech) AGAINST (:search) DESC;',
			[ 'search' => $_POST[ 'search' ] ] );

			if ( $this->database->numRows () == 0 )
				$this->database->query ( 'SELECT movie_name_czech, movie_picture, movie_name_origin, movie_year FROM
			movies WHERE movie_name_czech LIKE concat("%", :search, "%") OR movie_name_origin LIKE concat("%", :search, "%");',
				[ 'search' => $_POST[ 'search' ] ] );

			$this->smarty->display ( 'moviesView', $this->database->fetchAll () );
		}

		public function removeMovie () {

			$this->database->query ( 'DELETE FROM movies WHERE movie_id = :movie_id AND user_id = :user_id;',
			[ 'movie_id' => $_GET[ 'movie_id' ], 'user_id' => $_SESSION[ 'user_id' ] ] );

			if ( $this->database->numRows () == 1 )
				echo 'Film jsem smazal';
			else
				echo 'Film se nepodařilo smazat';
		}

		public function searchMovie () {
			//	if ( !isset( $_POST[ 'submit' ] ) )

			$this->smarty->display ( 'movieSearch', $_POST[ 'search' ], 'search' );

			if ( isset( $_POST[ 'submit' ] ) )
				$this->prepareSearch ( $_POST[ 'search' ] );
		}

		public function showMovie () {
			$this->database->query ( 'SELECT movie_name_czech, movie_name_origin, movie_year, movie_picture, movie_description
						FROM movies
						WHERE movie_id = :movie_id LIMIT 1;',

			[ 'movie_id' => $_GET[ 'movie_id' ] ] );

			if ( $this->database->numRows () == 0 )
				Starter::myExit ( $this->smarty, 'Film nenalezen' );

			$this->smarty->display ( 'movieShow', $this->database->fetch () );
		}

		private function showRelatedMovies () {
			$this->database->query ( 'SELECT m.movie_id, m.movie_name_czech, m.movie_year, m.movie_picture, rm.relation_id
 										FROM related_movies rm
										LEFT JOIN movies m
										ON rm.movie_related_id = m.movie_id
 WHERE rm.movie_id = :movie_id', [ 'movie_id' => $_GET[ 'movie_id' ] ] );
		}

		private function startParseData () {
			$html = file_get_contents ( $_POST[ 'csfdLink' ] );

			/// check if used gzip or not!!
			$html = ord ( $html[ 0 ] ) == 31 ? gzdecode ( $html ) : $html;

			$dom = new simple_html_dom();
			$dom->load ( $html );

			return $dom;
		}
	}