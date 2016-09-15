<?php

	/**
	 * Smarty compiler exception class
	 * @package Smarty
	 */
	class SmartyCompilerException extends SmartyException {
		/**
		 * The line number of the template error
		 * @type int|null
		 */
		public $line = NULL;
		/**
		 * The template source snippet relating to the error
		 * @type string|null
		 */
		public $source = NULL;
		/**
		 * The raw text of the error message
		 * @type string|null
		 */
		public $desc = NULL;
		/**
		 * The resource identifier or template name
		 * @type string|null
		 */
		public $template = NULL;

		public function __toString () {
			return ' --> Smarty Compiler: ' . $this->message . ' <-- ';
		}
	}
