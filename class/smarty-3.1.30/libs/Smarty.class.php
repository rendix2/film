<?php
	/**
	 * Project:     Smarty: the PHP compiling template engine
	 * File:        Smarty.class.php
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU Lesser General Public
	 * License as published by the Free Software Foundation; either
	 * version 2.1 of the License, or (at your option) any later version.
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * Lesser General Public License for more details.
	 * You should have received a copy of the GNU Lesser General Public
	 * License along with this library; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 * For questions, help, comments, discussion, etc., please join the
	 * Smarty mailing list. Send a blank e-mail to
	 * smarty-discussion-subscribe@googlegroups.com
	 * @link      http://www.smarty.net/
	 * @copyright 2016 New Digital Group, Inc.
	 * @copyright 2016 Uwe Tews
	 * @author    Monte Ohrt <monte at ohrt dot com>
	 * @author    Uwe Tews
	 * @author    Rodney Rehm
	 * @package   Smarty
	 * @version   3.1.30
	 */

	/**
	 * define shorthand directory separator constant
	 */
	if ( !defined ( 'DS' ) ) {
		define ( 'DS', DIRECTORY_SEPARATOR );
	}

	/**
	 * set SMARTY_DIR to absolute path to Smarty library files.
	 * Sets SMARTY_DIR only if user application has not already defined it.
	 */
	if ( !defined ( 'SMARTY_DIR' ) ) {
		define ( 'SMARTY_DIR', dirname ( __FILE__ ) . DS );
	}

	/**
	 * set SMARTY_SYSPLUGINS_DIR to absolute path to Smarty internal plugins.
	 * Sets SMARTY_SYSPLUGINS_DIR only if user application has not already defined it.
	 */
	if ( !defined ( 'SMARTY_SYSPLUGINS_DIR' ) ) {
		define ( 'SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . 'sysplugins' . DS );
	}
	if ( !defined ( 'SMARTY_PLUGINS_DIR' ) ) {
		define ( 'SMARTY_PLUGINS_DIR', SMARTY_DIR . 'plugins' . DS );
	}
	if ( !defined ( 'SMARTY_MBSTRING' ) ) {
		define ( 'SMARTY_MBSTRING', function_exists ( 'mb_get_info' ) );
	}
	if ( !defined ( 'SMARTY_RESOURCE_CHAR_SET' ) ) {
		// UTF-8 can only be done properly when mbstring is available!
		/**
		 * @deprecated in favor of Smarty::$_CHARSET
		 */
		define ( 'SMARTY_RESOURCE_CHAR_SET', SMARTY_MBSTRING ? 'UTF-8' : 'ISO-8859-1' );
	}
	if ( !defined ( 'SMARTY_RESOURCE_DATE_FORMAT' ) ) {
		/**
		 * @deprecated in favor of Smarty::$_DATE_FORMAT
		 */
		define ( 'SMARTY_RESOURCE_DATE_FORMAT', '%b %e, %Y' );
	}

	/**
	 * Try loading the Smarty_Internal_Data class
	 * If we fail we must load Smarty's autoloader.
	 * Otherwise we may have a global autoloader like Composer
	 */
	if ( !class_exists ( 'Smarty_Autoloader', FALSE ) ) {
		if ( !class_exists ( 'Smarty_Internal_Data', TRUE ) ) {
			require_once dirname ( __FILE__ ) . '/Autoloader.php';
			Smarty_Autoloader::registerBC ();
		}
	}

	/**
	 * Load always needed external class files
	 */
	if ( !class_exists ( 'Smarty_Internal_Data', FALSE ) ) {
		require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_data.php';
	}
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_extension_handler.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_templatebase.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_template.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_resource.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_variable.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_template_source.php';
	require_once SMARTY_SYSPLUGINS_DIR . 'smarty_template_resource_base.php';

	/**
	 * This is the main Smarty class
	 * @package Smarty
	 * The following methods will be dynamically loaded by the extension handler when they are called.
	 * They are located in a corresponding Smarty_Internal_Method_xxxx class
	 * @method int clearAllCache( int $exp_time = NULL, string $type = NULL )
	 * @method int clearCache( string $template_name, string $cache_id = NULL, string $compile_id = NULL, int $exp_time = NULL, string $type = NULL )
	 * @method int compileAllTemplates( string $extension = '.tpl', bool $force_compile = FALSE, int $time_limit = 0, int $max_errors = NULL )
	 * @method int compileAllConfig( string $extension = '.conf', bool $force_compile = FALSE, int $time_limit = 0, int $max_errors = NULL )
	 * @method int clearCompiledTemplate( $resource_name = NULL, $compile_id = NULL, $exp_time = NULL )
	 */
	class Smarty extends Smarty_Internal_TemplateBase {
		/**#@+
		 * constant definitions
		 */

		const CACHING_LIFETIME_CURRENT = 1;
		const CACHING_LIFETIME_SAVED   = 2;
		/**
		 * define caching modes
		 */
		const CACHING_OFF = 0;
		/**
		 * define constant for clearing cache files be saved expiration dates
		 */
		const CLEAR_EXPIRED          = -1;
		const COMPILECHECK_CACHEMISS = 2;
		/**
		 * define compile check modes
		 */
		const COMPILECHECK_OFF = 0;
		const COMPILECHECK_ON  = 1;
		const DEBUG_INDIVIDUAL = 2;
		/**
		 * define debug modes
		 */
		const DEBUG_OFF     = 0;
		const DEBUG_ON      = 1;
		const FILTER_OUTPUT = 'output';
		/**
		 * filter types
		 */
		const FILTER_POST     = 'post';
		const FILTER_PRE      = 'pre';
		const FILTER_VARIABLE = 'variable';
		const PHP_ALLOW       = 3;
		/**
		 * modes for handling of "<?php ... ?>" tags in templates.
		 */
		const PHP_PASSTHRU    = 0;
		const PHP_QUOTE       = 1;
		const PHP_REMOVE      = 2; //-> print tags as plain text
		const PLUGIN_BLOCK    = 'block'; //-> escape tags as entities
		const PLUGIN_COMPILER = 'compiler'; //-> escape tags as entities
		/**
		 * plugin types
		 */
		const PLUGIN_FUNCTION          = 'function'; //-> escape tags as entities
		const PLUGIN_MODIFIER          = 'modifier';
		const PLUGIN_MODIFIERCOMPILER  = 'modifiercompiler';
		const RESOURCE_CACHE_AUTOMATIC = 1;
		/**
		 * Resource caching modes
		 * (not used since 3.1.30)
		 */
		const RESOURCE_CACHE_OFF      = 0;
		const RESOURCE_CACHE_ON       = 4;
		const RESOURCE_CACHE_TEMPLATE = 2;
		const SCOPE_GLOBAL            = 32;
		/**
		 * define variable scopes
		 */
		const SCOPE_LOCAL    = 1;
		const SCOPE_PARENT   = 2;
		const SCOPE_ROOT     = 8;
		const SCOPE_SMARTY   = 16; // cache template objects by rules
		const SCOPE_TPL_ROOT = 4; // cache all template objects
		/**
		 * smarty version
		 */
		const SMARTY_VERSION = '3.1.30';    // cache source and compiled resources

		/**#@-*/
		/**
		 * assigned global tpl vars
		 */
		public static $global_tpl_vars = [ ];

		/**
		 * error handler returned by set_error_handler() in Smarty::muteExpectedErrors()
		 */
		public static $_previous_error_handler = NULL;

		/**
		 * contains directories outside of SMARTY_DIR that are to be muted by muteExpectedErrors()
		 */
		public static $_muted_directories = [ ];

		/**
		 * Flag denoting if Multibyte String functions are available
		 */
		public static $_MBSTRING = SMARTY_MBSTRING;

		/**
		 * The character set to adhere to (e.g. "UTF-8")
		 */
		public static $_CHARSET = SMARTY_RESOURCE_CHAR_SET;

		/**
		 * The date format to be used internally
		 * (accepts date() and strftime())
		 */
		public static $_DATE_FORMAT = SMARTY_RESOURCE_DATE_FORMAT;

		/**
		 * Flag denoting if PCRE should run in UTF-8 mode
		 */
		public static $_UTF8_MODIFIER = 'u';

		/**
		 * Flag denoting if operating system is windows
		 */
		public static $_IS_WINDOWS = FALSE;

		/**#@+
		 * variables
		 */

		/**
		 * auto literal on delimiters with whitespace
		 * @var boolean
		 */
		public $auto_literal = TRUE;

		/**
		 * display error on not assigned variables
		 * @var boolean
		 */
		public $error_unassigned = FALSE;

		/**
		 * look up relative file path in include_path
		 * @var boolean
		 */
		public $use_include_path = FALSE;
		/**
		 * flag if template_dir is normalized
		 * @var bool
		 */
		public $_templateDirNormalized = FALSE;
		/**
		 * joined template directory string used in cache keys
		 * @var string
		 */
		public $_joined_template_dir = NULL;
		/**
		 * flag if config_dir is normalized
		 * @var bool
		 */
		public $_configDirNormalized = FALSE;
		/**
		 * joined config directory string used in cache keys
		 * @var string
		 */
		public $_joined_config_dir = NULL;
		/**
		 * default template handler
		 * @var callable
		 */
		public $default_template_handler_func = NULL;
		/**
		 * default config handler
		 * @var callable
		 */
		public $default_config_handler_func = NULL;
		/**
		 * default plugin handler
		 * @var callable
		 */
		public $default_plugin_handler_func = NULL;
		/**
		 * flag if template_dir is normalized
		 * @var bool
		 */
		public $_compileDirNormalized = FALSE;
		/**
		 * flag if plugins_dir is normalized
		 * @var bool
		 */
		public $_pluginsDirNormalized = FALSE;
		/**
		 * flag if template_dir is normalized
		 * @var bool
		 */
		public $_cacheDirNormalized = FALSE;
		/**
		 * force template compiling?
		 * @var boolean
		 */
		public $force_compile = FALSE;
		/**
		 * check template for modifications?
		 * @var boolean
		 */
		public $compile_check = TRUE;
		/**
		 * use sub dirs for compiled/cached files?
		 * @var boolean
		 */
		public $use_sub_dirs = FALSE;
		/**
		 * allow ambiguous resources (that are made unique by the resource handler)
		 * @var boolean
		 */
		public $allow_ambiguous_resources = FALSE;
		/**
		 * merge compiled includes
		 * @var boolean
		 */
		public $merge_compiled_includes = FALSE;
		/**
		 * force cache file creation
		 * @var boolean
		 */
		public $force_cache = FALSE;
		/**
		 * template left-delimiter
		 * @var string
		 */
		public $left_delimiter = "{";
		/**
		 * template right-delimiter
		 * @var string
		 */
		public $right_delimiter = "}";
		/**
		 * class name
		 * This should be instance of Smarty_Security.
		 * @var string
		 * @see Smarty_Security
		 */
		public $security_class = 'Smarty_Security';
		/**
		 * implementation of security class
		 * @var Smarty_Security
		 */
		public $security_policy = NULL;
		/**
		 * controls handling of PHP-blocks
		 * @var integer
		 */
		public $php_handling = self::PHP_PASSTHRU;
		/**
		 * controls if the php template file resource is allowed
		 * @var bool
		 */
		public $allow_php_templates = FALSE;
		/**
		 * debug mode
		 * Setting this to true enables the debug-console.
		 * @var boolean
		 */
		public $debugging = FALSE;
		/**
		 * This determines if debugging is enable-able from the browser.
		 * <ul>
		 *  <li>NONE => no debugging control allowed</li>
		 *  <li>URL => enable debugging when SMARTY_DEBUG is found in the URL.</li>
		 * </ul>
		 * @var string
		 */
		public $debugging_ctrl = 'NONE';
		/**
		 * Name of debugging URL-param.
		 * Only used when $debugging_ctrl is set to 'URL'.
		 * The name of the URL-parameter that activates debugging.
		 * @var string
		 */
		public $smarty_debug_id = 'SMARTY_DEBUG';

		/**#@+
		 * security
		 */
		/**
		 * Path of debug template.
		 * @var string
		 */
		public $debug_tpl = NULL;
		/**
		 * When set, smarty uses this value as error_reporting-level.
		 * @var int
		 */
		public $error_reporting = NULL;
		/**
		 * Controls whether variables with the same name overwrite each other.
		 * @var boolean
		 */
		public $config_overwrite = TRUE;
		/**
		 * Controls whether config values of on/true/yes and off/false/no get converted to boolean.
		 * @var boolean
		 */
		public $config_booleanize = TRUE;

		/**#@-*/
		/**
		 * Controls whether hidden config sections/vars are read from the file.
		 * @var boolean
		 */
		public $config_read_hidden = FALSE;
		/**
		 * locking concurrent compiles
		 * @var boolean
		 */
		public $compile_locking = TRUE;
		/**
		 * Controls whether cache resources should use locking mechanism
		 * @var boolean
		 */
		public $cache_locking = FALSE;
		/**
		 * seconds to wait for acquiring a lock before ignoring the write lock
		 * @var float
		 */
		public $locking_timeout = 10;
		/**
		 * resource type used if none given
		 * Must be an valid key of $registered_resources.
		 * @var string
		 */
		public $default_resource_type = 'file';

		/**#@+
		 * config var settings
		 */
		/**
		 * caching type
		 * Must be an element of $cache_resource_types.
		 * @var string
		 */
		public $caching_type = 'file';
		/**
		 * config type
		 * @var string
		 */
		public $default_config_type = 'file';
		/**
		 * check If-Modified-Since headers
		 * @var boolean
		 */
		public $cache_modified_check = FALSE;

		/**#@-*/

		/**#@+
		 * resource locking
		 */
		/**
		 * registered plugins
		 * @var array
		 */
		public $registered_plugins = [ ];
		/**
		 * registered objects
		 * @var array
		 */
		public $registered_objects = [ ];
		/**
		 * registered classes
		 * @var array
		 */
		public $registered_classes = [ ];

		/**#@-*/
		/**
		 * registered filters
		 * @var array
		 */
		public $registered_filters = [ ];
		/**
		 * registered resources
		 * @var array
		 */
		public $registered_resources = [ ];
		/**
		 * registered cache resources
		 * @var array
		 */
		public $registered_cache_resources = [ ];
		/**
		 * autoload filter
		 * @var array
		 */
		public $autoload_filters = [ ];
		/**
		 * default modifier
		 * @var array
		 */
		public $default_modifiers = [ ];
		/**
		 * autoescape variable output
		 * @var boolean
		 */
		public $escape_html = FALSE;
		/**
		 * start time for execution time calculation
		 * @var int
		 */
		public $start_time = 0;
		/**
		 * required by the compiler for BC
		 * @var string
		 */
		public $_current_file = NULL;
		/**
		 * internal flag to enable parser debugging
		 * @var bool
		 */
		public $_parserdebug = FALSE;
		/**
		 * This object type (Smarty = 1, template = 2, data = 4)
		 * @var int
		 */
		public $_objType = 1;
		/**
		 * Debug object
		 * @var Smarty_Internal_Debug
		 */
		public $_debug = NULL;
		/**
		 * template directory
		 * @var array
		 */
		protected $template_dir = [ './templates/' ];
		/**
		 * flags for normalized template directory entries
		 * @var array
		 */
		protected $_processedTemplateDir = [ ];
		/**
		 * config directory
		 * @var array
		 */
		protected $config_dir = [ './configs/' ];
		/**
		 * flags for normalized template directory entries
		 * @var array
		 */
		protected $_processedConfigDir = [ ];
		/**
		 * compile directory
		 * @var string
		 */
		protected $compile_dir = './templates_c/';
		/**
		 * plugins directory
		 * @var array
		 */
		protected $plugins_dir = [ ];
		/**
		 * cache directory
		 * @var string
		 */
		protected $cache_dir = './cache/';
		/**
		 * removed properties
		 * @var string[]
		 */
		private $obsoleteProperties = [ 'resource_caching', 'template_resource_caching', 'direct_access_security',
		'_dir_perms', '_file_perms', 'plugin_search_order',
		'inheritance_merge_compiled_includes', 'resource_cache_mode',
		];

		/**
		 * List of private properties which will call getter/setter on a direct access
		 * @var string[]
		 */
		private $accessMap = [ 'template_dir' => 'TemplateDir', 'config_dir' => 'ConfigDir',
		                       'plugins_dir'  => 'PluginsDir', 'compile_dir' => 'CompileDir',
		                       'cache_dir'    => 'CacheDir',
		];

		/**#@-*/

		/**
		 * Initialize new Smarty object
		 */
		public function __construct () {
			parent::__construct ();
			if ( is_callable ( 'mb_internal_encoding' ) ) {
				mb_internal_encoding ( Smarty::$_CHARSET );
			}
			$this->start_time = microtime ( TRUE );

			if ( isset( $_SERVER[ 'SCRIPT_NAME' ] ) ) {
				Smarty::$global_tpl_vars[ 'SCRIPT_NAME' ] = new Smarty_Variable( $_SERVER[ 'SCRIPT_NAME' ] );
			}

			// Check if we're running on windows
			Smarty::$_IS_WINDOWS = strtoupper ( substr ( PHP_OS, 0, 3 ) ) === 'WIN';
			// let PCRE (preg_*) treat strings as ISO-8859-1 if we're not dealing with UTF-8
			if ( Smarty::$_CHARSET !== 'UTF-8' ) {
				Smarty::$_UTF8_MODIFIER = '';
			}
		}

		/**
		 * <<magic>> Generic getter.
		 * Calls the appropriate getter function.
		 * Issues an E_USER_NOTICE if no valid getter is found.
		 * @param  string $name property name
		 * @return mixed
		 */
		public function __get ( $name ) {
			if ( isset( $this->accessMap[ $name ] ) ) {
				$method = 'get' . $this->accessMap[ $name ];

				return $this->{$method}();
			} elseif ( isset( $this->_cache[ $name ] ) ) {
				return $this->_cache[ $name ];
			} elseif ( in_array ( $name, $this->obsoleteProperties ) ) {
				return NULL;
			} else {
				trigger_error ( 'Undefined property: ' . get_class ( $this ) . '::$' . $name, E_USER_NOTICE );
			}

			return NULL;
		}

		/**
		 * <<magic>> Generic setter.
		 * Calls the appropriate setter function.
		 * Issues an E_USER_NOTICE if no valid setter is found.
		 * @param string $name property name
		 * @param mixed $value parameter passed to setter
		 */
		public function __set ( $name, $value ) {
			if ( isset( $this->accessMap[ $name ] ) ) {
				$method = 'set' . $this->accessMap[ $name ];
				$this->{$method}( $value );
			} elseif ( in_array ( $name, $this->obsoleteProperties ) ) {
				return;
			} else {
				if ( is_object ( $value ) && method_exists ( $value, $name ) ) {
					$this->$name = $value;
				} else {
					trigger_error ( 'Undefined property: ' . get_class ( $this ) . '::$' . $name, E_USER_NOTICE );
				}
			}
		}

		/**
		 * Enable error handler to mute expected messages
		 * @return void
		 */
		public static function muteExpectedErrors () {
			/*
				error muting is done because some people implemented custom error_handlers using
				http://php.net/set_error_handler and for some reason did not understand the following paragraph:

					It is important to remember that the standard PHP error handler is completely bypassed for the
					error types specified by error_types unless the callback function returns FALSE.
					error_reporting() settings will have no effect and your error handler will be called regardless -
					however you are still able to read the current value of error_reporting and act appropriately.
					Of particular note is that this value will be 0 if the statement that caused the error was
					prepended by the @ error-control operator.

				Smarty deliberately uses @filemtime() over file_exists() and filemtime() in some places. Reasons include
					- @filemtime() is almost twice as fast as using an additional file_exists()
					- between file_exists() and filemtime() a possible race condition is opened,
					  which does not exist using the simple @filemtime() approach.
			*/
			$error_handler = [ 'Smarty', 'mutingErrorHandler' ];
			$previous      = set_error_handler ( $error_handler );

			// avoid dead loops
			if ( $previous !== $error_handler ) {
				Smarty::$_previous_error_handler = $previous;
			}
		}

		/**
		 * Error Handler to mute expected messages
		 * @link http://php.net/set_error_handler
		 * @param  integer $errno Error level
		 * @param          $errstr
		 * @param          $errfile
		 * @param          $errline
		 * @param          $errcontext
		 * @return bool|void
		 */
		public static function mutingErrorHandler ( $errno, $errstr, $errfile, $errline, $errcontext ) {
			$_is_muted_directory = FALSE;

			// add the SMARTY_DIR to the list of muted directories
			if ( !isset( Smarty::$_muted_directories[ SMARTY_DIR ] ) ) {
				$smarty_dir = realpath ( SMARTY_DIR );
				if ( $smarty_dir !== FALSE ) {
					Smarty::$_muted_directories[ SMARTY_DIR ] =
					[ 'file' => $smarty_dir, 'length' => strlen ( $smarty_dir ), ];
				}
			}

			// walk the muted directories and test against $errfile
			foreach ( Smarty::$_muted_directories as $key => &$dir ) {
				if ( !$dir ) {
					// resolve directory and length for speedy comparisons
					$file = realpath ( $key );
					if ( $file === FALSE ) {
						// this directory does not exist, remove and skip it
						unset( Smarty::$_muted_directories[ $key ] );
						continue;
					}
					$dir = [ 'file' => $file, 'length' => strlen ( $file ), ];
				}
				if ( !strncmp ( $errfile, $dir[ 'file' ], $dir[ 'length' ] ) ) {
					$_is_muted_directory = TRUE;
					break;
				}
			}
			// pass to next error handler if this error did not occur inside SMARTY_DIR
			// or the error was within smarty but masked to be ignored
			if ( !$_is_muted_directory || ( $errno && $errno & error_reporting () ) ) {
				if ( Smarty::$_previous_error_handler ) {
					return call_user_func ( Smarty::$_previous_error_handler, $errno, $errstr, $errfile, $errline,
					$errcontext );
				} else {
					return FALSE;
				}
			}

			return;
		}

		/**
		 * Disable error handler muting expected messages
		 * @return void
		 */
		public static function unmuteExpectedErrors () {
			restore_error_handler ();
		}

		/**
		 * Empty template objects cache
		 */
		public function _clearTemplateCache () {
			$this->_cache[ 'isCached' ]   = [ ];
			$this->_cache[ 'tplObjects' ] = [ ];
		}

		/**
		 * Get unique template id
		 * @param string $template_name
		 * @param null|mixed $cache_id
		 * @param null|mixed $compile_id
		 * @param null $caching
		 * @param \Smarty_Internal_Template $template
		 * @return string
		 */
		public function _getTemplateId ( $template_name, $cache_id = NULL, $compile_id = NULL, $caching = NULL,
		Smarty_Internal_Template $template = NULL ) {
			$template_name = ( strpos ( $template_name, ':' ) === FALSE ) ? "{$this->default_resource_type}:{$template_name}" :
			$template_name;
			$cache_id      = $cache_id === NULL ? $this->cache_id : $cache_id;
			$compile_id    = $compile_id === NULL ? $this->compile_id : $compile_id;
			$caching       = (int) ( $caching === NULL ? $this->caching : $caching );

			if ( ( isset( $template ) && strpos ( $template_name, ':.' ) !== FALSE ) || $this->allow_ambiguous_resources ) {
				$_templateId =
				Smarty_Resource::getUniqueTemplateName ( ( isset( $template ) ? $template : $this ), $template_name ) .
				"#{$cache_id}#{$compile_id}#{$caching}";
			} else {
				$_templateId = $this->_joined_template_dir . "#{$template_name}#{$cache_id}#{$compile_id}#{$caching}";
			}
			if ( isset( $_templateId[ 150 ] ) ) {
				$_templateId = sha1 ( $_templateId );
			}

			return $_templateId;
		}

		/**
		 * Normalize template_dir or config_dir
		 * @param bool $isConfig true for config_dir
		 */
		private function _nomalizeTemplateConfig ( $isConfig ) {
			if ( $isConfig ) {
				$processed = &$this->_processedConfigDir;
				$dir       = &$this->config_dir;
			} else {
				$processed = &$this->_processedTemplateDir;
				$dir       = &$this->template_dir;
			}
			if ( !is_array ( $dir ) ) {
				$dir = (array) $dir;
			}
			foreach ( $dir as $k => $v ) {
				if ( !isset( $processed[ $k ] ) ) {
					$dir[ $k ]       = $v = $this->_realpath ( rtrim ( $v, "/\\" ) . DS, TRUE );
					$processed[ $k ] = TRUE;
				}
			}
			$isConfig ? $this->_configDirNormalized = TRUE : $this->_templateDirNormalized = TRUE;
			$isConfig ? $this->_joined_config_dir = join ( '#', $this->config_dir ) :
			$this->_joined_template_dir = join ( '#', $this->template_dir );
		}

		/**
		 * Normalize and set directory string
		 * @param string $dirName cache_dir or compile_dir
		 * @param string $dir filepath of folder
		 */
		private function _normalizeDir ( $dirName, $dir ) {
			$this->{$dirName} = $this->_realpath ( rtrim ( $dir, "/\\" ) . DS, TRUE );
			if ( !isset( Smarty::$_muted_directories[ $this->{$dirName} ] ) ) {
				Smarty::$_muted_directories[ $this->{$dirName} ] = NULL;
			}
		}

		/**
		 * Normalize path
		 *  - remove /./ and /../
		 *  - make it absolute if required
		 * @param string $path file path
		 * @param bool $realpath if true - convert to absolute
		 *                          false - convert to relative
		 *                          null - keep as it is but remove /./ /../
		 * @return string
		 */
		public function _realpath ( $path, $realpath = NULL ) {
			$nds = DS == '/' ? '\\' : '/';
			// normalize DS
			$path = str_replace ( $nds, DS, $path );
			preg_match ( '%^(?<root>(?:[[:alpha:]]:[\\\\]|/|[\\\\]{2}[[:alpha:]]+|[[:print:]]{2,}:[/]{2}|[\\\\])?)(?<path>(?:[[:print:]]*))$%',
			$path, $parts );
			$path = $parts[ 'path' ];
			if ( $parts[ 'root' ] == '\\' ) {
				$parts[ 'root' ] = substr ( getcwd (), 0, 2 ) . $parts[ 'root' ];
			} else {
				if ( $realpath !== NULL && !$parts[ 'root' ] ) {
					$path = getcwd () . DS . $path;
				}
			}
			// remove noop 'DS DS' and 'DS.DS' patterns
			$path = preg_replace ( '#([\\\\/]([.]?[\\\\/])+)#', DS, $path );
			// resolve '..DS' pattern, smallest first
			if ( strpos ( $path, '..' . DS ) != FALSE &&
			preg_match_all ( '#(([.]?[\\\\/])*([.][.])[\\\\/]([.]?[\\\\/])*)+#', $path, $match )
			) {
				$counts = [ ];
				foreach ( $match[ 0 ] as $m ) {
					$counts[] = (int) ( ( strlen ( $m ) - 1 ) / 3 );
				}
				sort ( $counts );
				foreach ( $counts as $count ) {
					$path = preg_replace ( '#(([\\\\/]([.]?[\\\\/])*[^\\\\/.]+){' . $count .
					'}[\\\\/]([.]?[\\\\/])*([.][.][\\\\/]([.]?[\\\\/])*){' . $count . '})(?=[^.])#',
					DS, $path );
				}
			}

			return $parts[ 'root' ] . $path;
		}

		/**
		 * Add config directory(s)
		 * @param string|array $config_dir directory(s) of config sources
		 * @param mixed $key key of the array element to assign the config dir to
		 * @return Smarty current Smarty instance for chaining
		 */
		public function addConfigDir ( $config_dir, $key = NULL ) {
			return $this->addTemplateDir ( $config_dir, $key, TRUE );
		}

		/**
		 * Adds directory of plugin files
		 * @param null|array $plugins_dir
		 * @return Smarty current Smarty instance for chaining
		 */
		public function addPluginsDir ( $plugins_dir ) {
			if ( empty( $this->plugins_dir ) ) {
				$this->plugins_dir[] = SMARTY_PLUGINS_DIR;
			}
			$this->plugins_dir           = array_merge ( $this->plugins_dir, (array) $plugins_dir );
			$this->_pluginsDirNormalized = FALSE;

			return $this;
		}

		/**
		 * Add template directory(s)
		 * @param  string|array $template_dir directory(s) of template sources
		 * @param  string $key of the array element to assign the template dir to
		 * @param bool $isConfig true for config_dir
		 * @return Smarty          current Smarty instance for chaining
		 */
		public function addTemplateDir ( $template_dir, $key = NULL, $isConfig = FALSE ) {
			if ( $isConfig ) {
				$processed                  = &$this->_processedConfigDir;
				$dir                        = &$this->config_dir;
				$this->_configDirNormalized = FALSE;
			} else {
				$processed                    = &$this->_processedTemplateDir;
				$dir                          = &$this->template_dir;
				$this->_templateDirNormalized = FALSE;
			}
			if ( is_array ( $template_dir ) ) {
				foreach ( $template_dir as $k => $v ) {
					if ( is_int ( $k ) ) {
						// indexes are not merged but appended
						$dir[] = $v;
					} else {
						// string indexes are overridden
						$dir[ $k ] = $v;
						unset( $processed[ $key ] );
					}
				}
			} else {
				if ( $key !== NULL ) {
					// override directory at specified index
					$dir[ $key ] = $template_dir;
					unset( $processed[ $key ] );
				} else {
					// append new directory
					$dir[] = $template_dir;
				}
			}

			return $this;
		}

		/**
		 * creates a template object
		 * @param  string $template the resource handle of the template file
		 * @param  mixed $cache_id cache id to be used with this template
		 * @param  mixed $compile_id compile id to be used with this template
		 * @param  object $parent next higher level of Smarty variables
		 * @param  boolean $do_clone flag is Smarty object shall be cloned
		 * @return object  template object
		 */
		public function createTemplate ( $template, $cache_id = NULL, $compile_id = NULL, $parent = NULL, $do_clone = TRUE ) {
			if ( $cache_id !== NULL && ( is_object ( $cache_id ) || is_array ( $cache_id ) ) ) {
				$parent   = $cache_id;
				$cache_id = NULL;
			}
			if ( $parent !== NULL && is_array ( $parent ) ) {
				$data   = $parent;
				$parent = NULL;
			} else {
				$data = NULL;
			}
			$_templateId = $this->_getTemplateId ( $template, $cache_id, $compile_id );
			$tpl         = NULL;
			if ( $this->caching && isset( $this->_cache[ 'isCached' ][ $_templateId ] ) ) {
				$tpl           = $do_clone ? clone $this->_cache[ 'isCached' ][ $_templateId ] :
				$this->_cache[ 'isCached' ][ $_templateId ];
				$tpl->tpl_vars = $tpl->config_vars = [ ];
			} else if ( !$do_clone && isset( $this->_cache[ 'tplObjects' ][ $_templateId ] ) ) {
				$tpl = clone $this->_cache[ 'tplObjects' ][ $_templateId ];
			} else {
				/* @var Smarty_Internal_Template $tpl */
				$tpl             = new $this->template_class( $template, $this, NULL, $cache_id, $compile_id, NULL, NULL );
				$tpl->templateId = $_templateId;
			}
			if ( $do_clone ) {
				$tpl->smarty = clone $tpl->smarty;
			}
			$tpl->parent = $parent ? $parent : $this;
			// fill data if present
			if ( !empty( $data ) && is_array ( $data ) ) {
				// set up variable values
				foreach ( $data as $_key => $_val ) {
					$tpl->tpl_vars[ $_key ] = new Smarty_Variable( $_val );
				}
			}
			if ( $this->debugging || $this->debugging_ctrl == 'URL' ) {
				$tpl->smarty->_debug = new Smarty_Internal_Debug();
				// check URL debugging control
				if ( !$this->debugging && $this->debugging_ctrl == 'URL' ) {
					$tpl->smarty->_debug->debugUrl ( $tpl->smarty );
				}
			}

			return $tpl;
		}

		/**
		 * Disable security
		 * @return Smarty current Smarty instance for chaining
		 */
		public function disableSecurity () {
			$this->security_policy = NULL;

			return $this;
		}

		/**
		 * Loads security class and enables security
		 * @param  string|Smarty_Security $security_class if a string is used, it must be class-name
		 * @return Smarty                 current Smarty instance for chaining
		 * @throws SmartyException        when an invalid class name is provided
		 */
		public function enableSecurity ( $security_class = NULL ) {
			Smarty_Security::enableSecurity ( $this, $security_class );

			return $this;
		}

		/**
		 * Get cache directory
		 * @return string path of cache directory
		 */
		public function getCacheDir () {
			if ( !$this->_cacheDirNormalized ) {
				$this->_normalizeDir ( 'cache_dir', $this->cache_dir );
				$this->_cacheDirNormalized = TRUE;
			}

			return $this->cache_dir;
		}

		/**
		 * Set cache directory
		 * @param  string $cache_dir directory to store cached templates in
		 * @return Smarty current Smarty instance for chaining
		 */
		public function setCacheDir ( $cache_dir ) {
			$this->_normalizeDir ( 'cache_dir', $cache_dir );
			$this->_cacheDirNormalized = TRUE;

			return $this;
		}

		/**
		 * Get compiled directory
		 * @return string path to compiled templates
		 */
		public function getCompileDir () {
			if ( !$this->_compileDirNormalized ) {
				$this->_normalizeDir ( 'compile_dir', $this->compile_dir );
				$this->_compileDirNormalized = TRUE;
			}

			return $this->compile_dir;
		}

		/**
		 * @param  string $compile_dir directory to store compiled templates in
		 * @return Smarty current Smarty instance for chaining
		 */
		public function setCompileDir ( $compile_dir ) {
			$this->_normalizeDir ( 'compile_dir', $compile_dir );
			$this->_compileDirNormalized = TRUE;

			return $this;
		}

		/**
		 * Get config directory
		 * @param mixed $index index of directory to get, null to get all
		 * @return array configuration directory
		 */
		public function getConfigDir ( $index = NULL ) {
			return $this->getTemplateDir ( $index, TRUE );
		}

		/**
		 * Set config directory
		 * @param $config_dir
		 * @return Smarty       current Smarty instance for chaining
		 */
		public function setConfigDir ( $config_dir ) {
			return $this->setTemplateDir ( $config_dir, TRUE );
		}

		/**
		 * Get plugin directories
		 * @return array list of plugin directories
		 */
		public function getPluginsDir () {
			if ( empty( $this->plugins_dir ) ) {
				$this->plugins_dir[]         = SMARTY_PLUGINS_DIR;
				$this->_pluginsDirNormalized = FALSE;
			}
			if ( !$this->_pluginsDirNormalized ) {
				if ( !is_array ( $this->plugins_dir ) ) {
					$this->plugins_dir = (array) $this->plugins_dir;
				}
				foreach ( $this->plugins_dir as $k => $v ) {
					$this->plugins_dir[ $k ] = $this->_realpath ( rtrim ( $v, "/\\" ) . DS, TRUE );
				}
				$this->_cache[ 'plugin_files' ] = [ ];
				$this->_pluginsDirNormalized    = TRUE;
			}

			return $this->plugins_dir;
		}

		/**
		 * Set plugins directory
		 * @param  string|array $plugins_dir directory(s) of plugins
		 * @return Smarty       current Smarty instance for chaining
		 */
		public function setPluginsDir ( $plugins_dir ) {
			$this->plugins_dir           = (array) $plugins_dir;
			$this->_pluginsDirNormalized = FALSE;

			return $this;
		}

		/**
		 * Get template directories
		 * @param mixed $index index of directory to get, null to get all
		 * @param bool $isConfig true for config_dir
		 * @return array list of template directories, or directory of $index
		 */
		public function getTemplateDir ( $index = NULL, $isConfig = FALSE ) {
			if ( $isConfig ) {
				$dir = &$this->config_dir;
			} else {
				$dir = &$this->template_dir;
			}
			if ( $isConfig ? !$this->_configDirNormalized : !$this->_templateDirNormalized ) {
				$this->_nomalizeTemplateConfig ( $isConfig );
			}
			if ( $index !== NULL ) {
				return isset( $dir[ $index ] ) ? $dir[ $index ] : NULL;
			}

			return $dir;
		}

		/**
		 * Set template directory
		 * @param  string|array $template_dir directory(s) of template sources
		 * @param bool $isConfig true for config_dir
		 * @return \Smarty current Smarty instance for chaining
		 */
		public function setTemplateDir ( $template_dir, $isConfig = FALSE ) {
			if ( $isConfig ) {
				$this->config_dir          = [ ];
				$this->_processedConfigDir = [ ];
			} else {
				$this->template_dir          = [ ];
				$this->_processedTemplateDir = [ ];
			}
			$this->addTemplateDir ( $template_dir, NULL, $isConfig );

			return $this;
		}

		/**
		 * Takes unknown classes and loads plugin files for them
		 * class name format: Smarty_PluginType_PluginName
		 * plugin filename format: plugintype.pluginname.php
		 * @param  string $plugin_name class plugin name to load
		 * @param  bool $check check if already loaded
		 * @throws SmartyException
		 * @return string |boolean filepath of loaded file or false
		 */
		public function loadPlugin ( $plugin_name, $check = TRUE ) {
			return $this->ext->loadPlugin->loadPlugin ( $this, $plugin_name, $check );
		}

		/**
		 * @param boolean $auto_literal
		 */
		public function setAutoLiteral ( $auto_literal ) {
			$this->auto_literal = $auto_literal;
		}

		/**
		 * @param string $caching_type
		 */
		public function setCachingType ( $caching_type ) {
			$this->caching_type = $caching_type;
		}

		/**
		 * @param boolean $compile_check
		 */
		public function setCompileCheck ( $compile_check ) {
			$this->compile_check = $compile_check;
		}

		/**
		 * @param boolean $compile_locking
		 */
		public function setCompileLocking ( $compile_locking ) {
			$this->compile_locking = $compile_locking;
		}

		/**
		 * @param boolean $config_booleanize
		 */
		public function setConfigBooleanize ( $config_booleanize ) {
			$this->config_booleanize = $config_booleanize;
		}

		/**
		 * @param boolean $config_overwrite
		 */
		public function setConfigOverwrite ( $config_overwrite ) {
			$this->config_overwrite = $config_overwrite;
		}

		/**
		 * @param boolean $config_read_hidden
		 */
		public function setConfigReadHidden ( $config_read_hidden ) {
			$this->config_read_hidden = $config_read_hidden;
		}

		/**
		 * @param boolean $debugging
		 */
		public function setDebugging ( $debugging ) {
			$this->debugging = $debugging;
		}

		/**
		 * @param string $default_resource_type
		 */
		public function setDefaultResourceType ( $default_resource_type ) {
			$this->default_resource_type = $default_resource_type;
		}

		/**
		 * @param int $error_reporting
		 */
		public function setErrorReporting ( $error_reporting ) {
			$this->error_reporting = $error_reporting;
		}

		/**
		 * @param boolean $escape_html
		 */
		public function setEscapeHtml ( $escape_html ) {
			$this->escape_html = $escape_html;
		}

		/**
		 * @param boolean $force_compile
		 */
		public function setForceCompile ( $force_compile ) {
			$this->force_compile = $force_compile;
		}

		/**
		 * @param string $left_delimiter
		 */
		public function setLeftDelimiter ( $left_delimiter ) {
			$this->left_delimiter = $left_delimiter;
		}

		/**
		 * @param boolean $merge_compiled_includes
		 */
		public function setMergeCompiledIncludes ( $merge_compiled_includes ) {
			$this->merge_compiled_includes = $merge_compiled_includes;
		}

		/**
		 * @param string $right_delimiter
		 */
		public function setRightDelimiter ( $right_delimiter ) {
			$this->right_delimiter = $right_delimiter;
		}

		/**
		 * @param boolean $use_sub_dirs
		 */
		public function setUseSubDirs ( $use_sub_dirs ) {
			$this->use_sub_dirs = $use_sub_dirs;
		}

		/**
		 * Check if a template resource exists
		 * @param  string $resource_name template name
		 * @return boolean status
		 */
		public function templateExists ( $resource_name ) {
			// create source object
			$source = Smarty_Template_Source::load ( NULL, $this, $resource_name );

			return $source->exists;
		}

		/**
		 * Test install
		 * @param null $errors
		 */
		public function testInstall ( &$errors = NULL ) {
			Smarty_Internal_TestInstall::testInstall ( $this, $errors );
		}
	}
