<?php
	/**
	 * Smarty Resource Plugin
	 * @package    Smarty
	 * @subpackage TemplateResources
	 * @author     Rodney Rehm
	 */

	/**
	 * Smarty Resource Plugin
	 * Wrapper Implementation for custom resource plugins
	 * @package    Smarty
	 * @subpackage TemplateResources
	 */
	abstract class Smarty_Resource_Custom extends Smarty_Resource {
		/**
		 * fetch template and its modification time from data source
		 * @param string $name template name
		 * @param string &$source template source
		 * @param integer &$mtime template modification timestamp (epoch)
		 */
		abstract protected function fetch ( $name, &$source, &$mtime );

		/**
		 * Fetch template's modification timestamp from data source
		 * {@internal implementing this method is optional.
		 *  Only implement it if modification times can be accessed faster than loading the complete template source.}}
		 * @param  string $name template name
		 * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
		 */
		protected function fetchTimestamp ( $name ) {
			return NULL;
		}

		/**
		 * Determine basename for compiled filename
		 * @param  Smarty_Template_Source $source source object
		 * @return string                 resource's basename
		 */
		public function getBasename ( Smarty_Template_Source $source ) {
			return basename ( $source->name );
		}

		/**
		 * Load template's source into current template object
		 * @param  Smarty_Template_Source $source source object
		 * @return string                 template source
		 * @throws SmartyException        if source cannot be loaded
		 */
		public function getContent ( Smarty_Template_Source $source ) {
			$this->fetch ( $source->name, $content, $timestamp );
			if ( isset( $content ) ) {
				return $content;
			}

			throw new SmartyException( "Unable to read template {$source->type} '{$source->name}'" );
		}

		/**
		 * populate Source Object with meta data from Resource
		 * @param Smarty_Template_Source $source source object
		 * @param Smarty_Internal_Template $_template template object
		 */
		public function populate ( Smarty_Template_Source $source, Smarty_Internal_Template $_template = NULL ) {
			$source->filepath = $source->type . ':' . $source->name;
			$source->uid      = sha1 ( $source->type . ':' . $source->name );

			$mtime = $this->fetchTimestamp ( $source->name );
			if ( $mtime !== NULL ) {
				$source->timestamp = $mtime;
			} else {
				$this->fetch ( $source->name, $content, $timestamp );
				$source->timestamp = isset( $timestamp ) ? $timestamp : FALSE;
				if ( isset( $content ) ) {
					$source->content = $content;
				}
			}
			$source->exists = ! !$source->timestamp;
		}
	}
