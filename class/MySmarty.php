<?php

	/**
	 * Created by PhpStorm.
	 * User: Tomáš
	 * Date: 14. 9. 2016
	 * Time: 11:29
	 */
	final  class MySmarty {

		private $smarty;

		public function __construct () {
			$this->smarty = new Smarty();
			$this->smarty->setTemplateDir ( dirname ( __DIR__ ) . '/template/' );
			$this->smarty->setCacheDir ( dirname ( __DIR__ ) . '/template/cache/' );
			$this->smarty->setCompileDir ( dirname ( __DIR__ ) . '/template/compile/' );
			$this->smarty->setCompileCheck ( TRUE );
			//$this->smarty->setCaching(TRUE);
		}

		public function getSmarty () {
			return $this->smarty;
		}

		public function display ( $templateName, $data, $dataName = 'data' ) {
			try {
				$this->smarty->assign ( $dataName, $data );
				$this->smarty->display ( $templateName . '.tpl' );
				$this->smarty->clearAllAssign ();
			} catch ( \SmartyException $SmartyException ) {
				echo $SmartyException->getMessage ();
			}
		}
	}