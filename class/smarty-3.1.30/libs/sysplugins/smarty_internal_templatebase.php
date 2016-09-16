<?php
    /**
     * Smarty Internal Plugin Smarty Template  Base
     * This file contains the basic shared methods for template handling
     * @package    Smarty
     * @subpackage Template
     * @author     Uwe Tews
     */

/**
 * Class with shared smarty/template methods
 *
 * @package      Smarty
 * @subpackage   Template
 *
 * @property Smarty $smarty
 *
 * The following methods will be dynamically loaded by the extension handler when they are called.
 * They are located in a corresponding Smarty_Internal_Method_xxxx class
 *
 * @method Smarty_Internal_TemplateBase addAutoloadFilters( mixed $filters, string $type = NULL )
 * @method Smarty_Internal_TemplateBase addDefaultModifier( mixed $modifiers )
 * @method Smarty_Internal_TemplateBase createData( Smarty_Internal_Data $parent = NULL, string $name = NULL )
 * @method array getAutoloadFilters( string $type = NULL )
 * @method string getDebugTemplate()
 * @method array getDefaultModifier()
 * @method array getTags( mixed $template = NULL )
 * @method object getRegisteredObject( string $object_name )
 * @method Smarty_Internal_TemplateBase registerCacheResource( string $name, Smarty_CacheResource $resource_handler )
 * @method Smarty_Internal_TemplateBase registerClass( string $class_name, string $class_impl )
 * @method Smarty_Internal_TemplateBase registerDefaultConfigHandler( callback $callback )
 * @method Smarty_Internal_TemplateBase registerDefaultPluginHandler( callback $callback )
 * @method Smarty_Internal_TemplateBase registerDefaultTemplateHandler( callback $callback )
 * @method Smarty_Internal_TemplateBase registerResource( string $name, mixed $resource_handler )
 * @method Smarty_Internal_TemplateBase setAutoloadFilters( mixed $filters, string $type = NULL )
 * @method Smarty_Internal_TemplateBase setDebugTemplate( string $tpl_name )
 * @method Smarty_Internal_TemplateBase setDefaultModifier( mixed $modifiers )
 * @method Smarty_Internal_TemplateBase unloadFilter( string $type, string $name )
 * @method Smarty_Internal_TemplateBase unregisterCacheResource( string $name )
 * @method Smarty_Internal_TemplateBase unregisterObject( string $object_name )
 * @method Smarty_Internal_TemplateBase unregisterPlugin( string $type, string $name )
 * @method Smarty_Internal_TemplateBase unregisterFilter( string $type, mixed $callback )
 * @method Smarty_Internal_TemplateBase unregisterResource( string $name )
 */
    abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data {
        /**
         * Set this if you want different sets of cache files for the same
         * templates.
         * @var string
         */
        public $cache_id = NULL;

        /**
         * Set this if you want different sets of compiled files for the same
         * templates.
         * @var string
         */
        public $compile_id = NULL;

        /**
         * caching enabled
         * @var boolean
         */
        public $caching = FALSE;

        /**
         * cache lifetime in seconds
         * @var integer
         */
        public $cache_lifetime = 3600;

        /**
         * universal cache
         *
         * @var array()
         */
        public $_cache = [ ];

        /**
         * fetches a rendered Smarty template
         *
         * @param  string $template the resource handle of the template file or template object
         * @param  mixed $cache_id cache id to be used with this template
         * @param  mixed $compile_id compile id to be used with this template
         * @param  object $parent next higher level of Smarty variables
         * @param  string $function function type 0 = fetch,  1 = display, 2 = isCache
         *
         * @return mixed
         * @throws \Exception
         * @throws \SmartyException
         */
        private function _execute ( $template, $cache_id, $compile_id, $parent, $function ) {
            $smarty   = $this->_objType == 1 ? $this : $this->smarty;
            $saveVars = TRUE;
            if ( $template === NULL ) {
                if ( $this->_objType != 2 ) {
                    throw new SmartyException( $function . '():Missing \'$template\' parameter' );
                } else {
                    $template = $this;
                }
            } elseif ( is_object ( $template ) ) {
                if ( !isset( $template->_objType ) || $template->_objType != 2 ) {
                    throw new SmartyException( $function . '():Template object expected' );
                }
            } else {
                // get template object
                /* @var Smarty_Internal_Template $template */
                $saveVars = FALSE;

                $template = $smarty->createTemplate ( $template, $cache_id, $compile_id, $parent ? $parent : $this, FALSE );
                if ( $this->_objType == 1 ) {
                    // set caching in template object
                    $template->caching = $this->caching;
                }
            }
            // fetch template content
            $level = ob_get_level ();
            try {
                $_smarty_old_error_level =
                isset( $smarty->error_reporting ) ? error_reporting ( $smarty->error_reporting ) : NULL;
                if ( $function == 2 ) {
                    if ( $template->caching ) {
                        // return cache status of template
                        if ( !isset( $template->cached ) ) {
                            $template->loadCached ();
                        }
                        $result                                                                 = $template->cached->isCached ( $template );
                        $template->smarty->_cache[ 'isCached' ][ $template->_getTemplateId () ] = $template;
                    } else {
                        return FALSE;
                    }
                } else {
                    if ( $saveVars ) {
                        $savedTplVars    = $template->tpl_vars;
                        $savedConfigVars = $template->config_vars;
                    }
                    ob_start ();
                    $template->_mergeVars ();
                    if ( !empty( Smarty::$global_tpl_vars ) ) {
                        $template->tpl_vars = array_merge ( Smarty::$global_tpl_vars, $template->tpl_vars );
                    }
                    $result = $template->render ( FALSE, $function );
                    $template->_cleanUp ();
                    if ( $saveVars ) {
                        $template->tpl_vars    = $savedTplVars;
                        $template->config_vars = $savedConfigVars;
                    } else {
                        if ( !$function && !isset( $smarty->_cache[ 'tplObjects' ][ $template->templateId ] ) ) {
                            $template->parent                                        = NULL;
                            $template->tpl_vars                                      = $template->config_vars = [ ];
                            $smarty->_cache[ 'tplObjects' ][ $template->templateId ] = $template;
                        }
                    }
                }
                if ( isset( $_smarty_old_error_level ) ) {
                    error_reporting ( $_smarty_old_error_level );
                }

                return $result;
            } catch ( Exception $e ) {
                while ( ob_get_level () > $level ) {
                    ob_end_clean ();
                }
                if ( isset( $_smarty_old_error_level ) ) {
                    error_reporting ( $_smarty_old_error_level );
                }
                throw $e;
            }
        }

        /**
         * displays a Smarty template
         *
         * @param string $template the resource handle of the template file or template object
         * @param mixed $cache_id cache id to be used with this template
         * @param mixed $compile_id compile id to be used with this template
         * @param object $parent next higher level of Smarty variables
         */
        public function display ( $template = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL ) {
            // display template
            $this->_execute ( $template, $cache_id, $compile_id, $parent, 1 );
        }

        /**
         * fetches a rendered Smarty template
         *
         * @param  string $template the resource handle of the template file or template object
         * @param  mixed $cache_id cache id to be used with this template
         * @param  mixed $compile_id compile id to be used with this template
         * @param  object $parent next higher level of Smarty variables
         *
         * @throws Exception
         * @throws SmartyException
         * @return string rendered template output
         */
        public function fetch ( $template = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL ) {
            $result = $this->_execute ( $template, $cache_id, $compile_id, $parent, 0 );

            return $result === NULL ? ob_get_clean () : $result;
        }

        /**
         * test if cache is valid
         *
         * @api  Smarty::isCached()
         * @link http://www.smarty.net/docs/en/api.is.cached.tpl
         *
         * @param  null|string|\Smarty_Internal_Template $template the resource handle of the template file or template object
         * @param  mixed $cache_id cache id to be used with this template
         * @param  mixed $compile_id compile id to be used with this template
         * @param  object $parent next higher level of Smarty variables
         *
         * @return boolean       cache status
         */
        public function isCached ( $template = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL ) {
            return $this->_execute ( $template, $cache_id, $compile_id, $parent, 2 );
        }

        /**
         * load a filter of specified type and name
         *
         * @api  Smarty::loadFilter()
         * @link http://www.smarty.net/docs/en/api.load.filter.tpl
         *
         * @param  string $type filter type
         * @param  string $name filter name
         *
         * @return bool
         * @throws SmartyException if filter could not be loaded
         */
        public function loadFilter ( $type, $name ) {
            return $this->ext->loadFilter->loadFilter ( $this, $type, $name );
        }

        /**
         * Registers a filter function
         *
         * @api  Smarty::registerFilter()
         * @link http://www.smarty.net/docs/en/api.register.filter.tpl
         *
         * @param  string $type filter type
         * @param  callback $callback
         * @param  string|null $name optional filter name
         *
         * @return \Smarty|\Smarty_Internal_Template
         * @throws \SmartyException
         */
        public function registerFilter ( $type, $callback, $name = NULL ) {
            return $this->ext->registerFilter->registerFilter ( $this, $type, $callback, $name );
        }

        /**
         * Registers object to be used in templates
         *
         * @api  Smarty::registerObject()
         * @link http://www.smarty.net/docs/en/api.register.object.tpl
         *
         * @param  string $object_name
         * @param  object $object the referenced PHP object to register
         * @param  array $allowed_methods_properties list of allowed methods (empty = all)
         * @param  bool $format smarty argument format, else traditional
         * @param  array $block_methods list of block-methods
         *
         * @return \Smarty|\Smarty_Internal_Template
         * @throws \SmartyException
         */
        public function registerObject ( $object_name, $object, $allowed_methods_properties = [ ], $format = TRUE,
        $block_methods = [ ] ) {
            return $this->ext->registerObject->registerObject ( $this, $object_name, $object, $allowed_methods_properties,
            $format, $block_methods );
        }

        /**
         * Registers plugin to be used in templates
         *
         * @api  Smarty::registerPlugin()
         * @link http://www.smarty.net/docs/en/api.register.plugin.tpl
         *
         * @param  string $type plugin type
         * @param  string $name name of template tag
         * @param  callback $callback PHP callback to register
         * @param  bool $cacheable if true (default) this function is cache able
         * @param  mixed $cache_attr caching attributes if any
         *
         * @return \Smarty|\Smarty_Internal_Template
         * @throws SmartyException              when the plugin tag is invalid
         */
        public function registerPlugin ( $type, $name, $callback, $cacheable = TRUE, $cache_attr = NULL ) {
            return $this->ext->registerPlugin->registerPlugin ( $this, $type, $name, $callback, $cacheable, $cache_attr );
        }

        /**
         * @param string $cache_id
         */
        public function setCacheId ( $cache_id ) {
            $this->cache_id = $cache_id;
        }

        /**
         * @param int $cache_lifetime
         */
        public function setCacheLifetime ( $cache_lifetime ) {
            $this->cache_lifetime = $cache_lifetime;
        }

        /**
         * @param boolean $caching
         */
        public function setCaching ( $caching ) {
            $this->caching = $caching;
        }

        /**
         * @param string $compile_id
         */
        public function setCompileId ( $compile_id ) {
            $this->compile_id = $compile_id;
        }

    }

