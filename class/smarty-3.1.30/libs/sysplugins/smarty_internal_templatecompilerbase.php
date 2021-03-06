<?php

    /**
     * Smarty Internal Plugin Smarty Template Compiler Base
     * This file contains the basic classes and methods for compiling Smarty templates with lexer/parser
     * @package    Smarty
     * @subpackage Compiler
     * @author     Uwe Tews
     */

/**
 * Main abstract compiler class

 * @package    Smarty
 * @subpackage Compiler

 * @property Smarty_Internal_SmartyTemplateCompiler $prefixCompiledCode  = ''
 * @property Smarty_Internal_SmartyTemplateCompiler $postfixCompiledCode = ''
 * @method registerPostCompileCallback( $callback, $parameter = [ ], $key = NULL, $replace = FALSE )
 * @method unregisterPostCompileCallback( $key )
 */
    abstract class Smarty_Internal_TemplateCompilerBase {

        /**
         * compile tag objects cache
         *
         * @var array
         */
        static $_tag_objects = [ ];
        /**
         * counter for prefix variable number
         * @var int
         */
        public static $prefixVariableNumber = 0;
        /**
         * Smarty object
         * @var Smarty
         */
        public $smarty = NULL;
        /**
         * Parser object
         * @var Smarty_Internal_Templateparser
         */
        public $parser = NULL;
        /**
         * hash for nocache sections
         * @var mixed
         */
        public $nocache_hash = NULL;
        /**
         * suppress generation of nocache code
         * @var bool
         */
        public $suppressNocacheProcessing = FALSE;
        /**
         * tag stack
         * @var array
         */
        public $_tag_stack = [ ];
        /**
         * current template
         * @var Smarty_Internal_Template
         */
        public $template = NULL;
        /**
         * merged included sub template data
         * @var array
         */
        public $mergedSubTemplatesData = [ ];
        /**
         * merged sub template code
         * @var array
         */
        public $mergedSubTemplatesCode = [ ];
        /**
         * collected template properties during compilation
         * @var array
         */
        public $templateProperties = [ ];
        /**
         * source line offset for error messages
         * @var int
         */
        public $trace_line_offset = 0;
        /**
         * trace uid
         * @var string
         */
        public $trace_uid = '';
        /**
         * trace file path
         * @var string
         */
        public $trace_filepath = '';
        /**
         * stack for tracing file and line of nested {block} tags
         * @var array
         */
        public $trace_stack = [ ];
        /**
         * plugins loaded by default plugin handler
         * @var array
         */
        public $default_handler_plugins = [ ];
        /**
         * saved preprocessed modifier list
         * @var mixed
         */
        public $default_modifier_list = NULL;
        /**
         * force compilation of complete template as nocache
         * @var boolean
         */
        public $forceNocache = FALSE;
        /**
         * flag if compiled template file shall we written
         * @var bool
         */
        public $write_compiled_code = TRUE;
        /**
         * Template functions
         * @var array
         */
        public $tpl_function = [ ];
        /**
         * called sub functions from template function
         * @var array
         */
        public $called_functions = [ ];
        /**
         * compiled template or block function code
         * @var string
         */
        public $blockOrFunctionCode = '';
        /**
         * php_handling setting either from Smarty or security
         * @var int
         */
        public $php_handling = 0;
        /**
         * flags for used modifier plugins
         * @var array
         */
        public $modifier_plugins = [ ];
        /**
         * type of already compiled modifier
         * @var array
         */
        public $known_modifier_type = [ ];
        /**
         * parent compiler object for merged subtemplates and template functions
         * @var Smarty_Internal_TemplateCompilerBase
         */
        public $parent_compiler = NULL;
        /**
         * Flag true when compiling nocache section
         * @var bool
         */
        public $nocache = FALSE;
        /**
         * Flag true when tag is compiled as nocache
         * @var bool
         */
        public $tag_nocache = FALSE;
        /**
         * Compiled tag prefix code
         * @var array
         */
        public $prefix_code = [ ];
        /**
         * Prefix code  stack
         * @var array
         */
        public $prefixCodeStack = [ ];
        /**
         * Tag has compiled code
         * @var bool
         */
        public $has_code = FALSE;
        /**
         * A variable string was compiled
         * @var bool
         */
        public $has_variable_string = FALSE;
        /**
         * Tag creates output
         * @var bool
         */
        public $has_output = FALSE;
        /**
         * Stack for {setfilter} {/setfilter}
         * @var array
         */
        public $variable_filter_stack = [ ];
        /**
         * variable filters for {setfilter} {/setfilter}
         * @var array
         */
        public $variable_filters = [ ];
        /**
         * Nesting count of looping tags like {foreach}, {for}, {section}, {while}
         * @var int
         */
        public $loopNesting = 0;
        /**
         * Strip preg pattern
         * @var string
         */
        public $stripRegEx = '![\t ]*[\r\n]+[\t ]*!';
        /**
         * plugin search order
         * @var array
         */
        public $plugin_search_order = [ 'function', 'block', 'compiler', 'class' ];
        /**
         * General storage area for tag compiler plugins
         * @var array
         */
        public $_cache = [ ];

        /**
         * method to compile a Smarty template
         *
         * @param mixed $_content template source
     * @param bool  $isTemplateSource
         *
         * @return bool true if compiling succeeded, false if it failed
         */
        abstract protected function doCompile($_content, $isTemplateSource = FALSE );

        /**
         * Initialize compiler
         * @param Smarty $smarty global instance
         */
        public function __construct ( Smarty $smarty ) {
            $this->smarty       = $smarty;
            $this->nocache_hash = str_replace ( [ '.', ',' ], '_', uniqid ( rand (), TRUE ) );
        }

        /**
         * Append code segments and remove unneeded ?> <?php transitions
         *
         * @param string $left
         * @param string $right
         *
         * @return string
         */
        public function appendCode ( $left, $right ) {
            if ( preg_match ( '/\s*\?>\s*$/', $left ) && preg_match ( '/^\s*<\?php\s+/', $right ) ) {
                $left = preg_replace ( '/\s*\?>\s*$/', "\n", $left );
                $left .= preg_replace ( '/^\s*<\?php\s+/', '', $right );
            } else {
                $left .= $right;
            }

            return $left;
        }

        /**
         * append  code to prefix buffer
         * @param string $code
         */
        public function appendPrefixCode ( $code ) {
            $this->prefix_code[] = $code;
        }

        /**
         * lazy loads internal compile plugin for tag and calls the compile method
         * compile objects cached for reuse.
         * class name format:  Smarty_Internal_Compile_TagName
         * plugin filename format: Smarty_Internal_TagName.php
         *
         * @param  string $tag tag name
         * @param  array $args list of tag attributes
         * @param  mixed $param1 optional parameter
         * @param  mixed $param2 optional parameter
         * @param  mixed $param3 optional parameter
         *
         * @return string compiled code
         */
        public function callTagCompiler ( $tag, $args, $param1 = NULL, $param2 = NULL, $param3 = NULL ) {
            // re-use object if already exists
            if ( !isset( self::$_tag_objects[ $tag ] ) ) {
                // lazy load internal compiler plugin
                $_tag       = explode ( '_', $tag );
                $_tag       = array_map ( 'ucfirst', $_tag );
                $class_name = 'Smarty_Internal_Compile_' . implode ( '_', $_tag );
                if ( class_exists ( $class_name ) &&
                ( !isset( $this->smarty->security_policy ) || $this->smarty->security_policy->isTrustedTag ( $tag, $this ) )
                ) {
                    self::$_tag_objects[ $tag ] = new $class_name;
                } else {
                    self::$_tag_objects[ $tag ] = FALSE;

                    return FALSE;
                }
            }

            // compile this tag
            return self::$_tag_objects[ $tag ] === FALSE ? FALSE :
            self::$_tag_objects[ $tag ]->compile ( $args, $this, $param1, $param2, $param3 );
        }

        /**
         * compile config variable
         * @param string $variable
         * @return string
         */
        public function compileConfigVariable ( $variable ) {
            // return '$_smarty_tpl->config_vars[' . $variable . ']';
            return '$_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, ' . $variable . ')';
        }

        /**
         * Compile Tag
         * This is a call back from the lexer/parser
         *
         * Save current prefix code
         * Compile tag
         * Merge tag prefix code with saved one
         * (required nested tags in attributes)
         *
         * @param  string $tag tag name
         * @param  array $args array with tag attributes
         * @param  array $parameter array with compilation parameter
         *
         * @throws SmartyCompilerException
         * @throws SmartyException
         * @return string compiled code
         */
        public function compileTag ( $tag, $args, $parameter = [ ] ) {
            $this->prefixCodeStack[] = $this->prefix_code;
            $this->prefix_code       = [ ];
            $result                  = $this->compileTag2 ( $tag, $args, $parameter );
            $this->prefix_code       = array_merge ( $this->prefix_code, array_pop ( $this->prefixCodeStack ) );

            return $result;
        }

        /**
         * Compile Tag
         *
         * @param  string $tag tag name
         * @param  array $args array with tag attributes
         * @param  array $parameter array with compilation parameter
         *
         * @throws SmartyCompilerException
         * @throws SmartyException
         * @return string compiled code
         */
        private function compileTag2 ( $tag, $args, $parameter ) {
            $plugin_type = '';
            // $args contains the attributes parsed and compiled by the lexer/parser
            // assume that tag does compile into code, but creates no HTML output
            $this->has_code   = TRUE;
            $this->has_output = FALSE;
            // log tag/attributes
            if ( isset( $this->smarty->_cache[ 'get_used_tags' ] ) ) {
                $this->template->_cache[ 'used_tags' ][] = [ $tag, $args ];
            }
            // check nocache option flag
            foreach ( $args as $arg ) {
                if ( !is_array ( $arg ) ) {
                    if ( $arg == "'nocache'") {
                        $this->tag_nocache = TRUE;
                    }
                } else {
                    foreach ( $arg as $k => $v ) {
                        if ( $k == "'nocache'" && ( trim ( $v, "'\" " ) == 'true' ) ) {
                            $this->tag_nocache = TRUE;
                        }
                    }
                }
            }
            // compile the smarty tag (required compile classes to compile the tag are auto loaded)
        if (($_output = $this->callTagCompiler($tag, $args, $parameter ) ) === FALSE ) {
            if ( isset( $this->parent_compiler->tpl_function[ $tag ] ) ) {
                // template defined by {template} tag
                $args[ '_attr' ][ 'name' ] = "'" . $tag . "'";
                $_output                   = $this->callTagCompiler ( 'call', $args, $parameter );
            }
        }
            if ( $_output !== FALSE ) {
                if ( $_output !== TRUE ) {
                    // did we get compiled code
                    if ($this->has_code ) {
                        // Does it create output?
                        if ( $this->has_output ) {
                            $_output .= "\n";
                        }

                        // return compiled code
                        return $_output;
                    }
                }

                // tag did not produce compiled code
                return NULL;
            } else {
                // map_named attributes
            if ( isset( $args[ '_attr' ] ) ) {
                foreach ( $args[ '_attr' ] as $key => $attribute ) {
                    if ( is_array ( $attribute ) ) {
                        $args = array_merge ( $args, $attribute );
                    }
                }
            }
                // not an internal compiler tag
                if ( strlen ( $tag ) < 6 || substr ( $tag, -5 ) != 'close') {
                // check if tag is a registered object
                    if ( isset( $this->smarty->registered_objects[ $tag ] ) && isset( $parameter[ 'object_method' ] ) ) {
                        $method = $parameter[ 'object_method' ];
                        if ( !in_array ( $method, $this->smarty->registered_objects[ $tag ][ 3 ] ) &&
                        ( empty( $this->smarty->registered_objects[ $tag ][ 1 ] ) ||
                        in_array ( $method, $this->smarty->registered_objects[ $tag ][ 1 ] ) )
                        ) {
                            return $this->callTagCompiler ( 'private_object_function', $args, $parameter, $tag, $method );
                        } elseif ( in_array ( $method, $this->smarty->registered_objects[ $tag ][ 3 ] ) ) {
                            return $this->callTagCompiler ( 'private_object_block_function', $args, $parameter, $tag,
                            $method );
                        } else {
                            // throw exception
                            $this->trigger_template_error ( 'not allowed method "' . $method . '" in registered object "' .
                            $tag . '"', NULL, TRUE );
                        }
                    }
                    // check if tag is registered
                    foreach ( array ( Smarty::PLUGIN_COMPILER, Smarty::PLUGIN_FUNCTION, Smarty::PLUGIN_BLOCK, ) as $plugin_type ) {
                        if ( isset( $this->smarty->registered_plugins[ $plugin_type ][ $tag ] ) ) {
                            // if compiler function plugin call it now
                            if ( $plugin_type == Smarty::PLUGIN_COMPILER ) {
                                $new_args = [ ];
                                foreach ( $args as $key => $mixed ) {
                                    if ( is_array ( $mixed ) ) {
                                        $new_args = array_merge ( $new_args, $mixed );
                                    } else {
                                        $new_args[ $key ] = $mixed;
                                    }
                                }
                                if ( !$this->smarty->registered_plugins[ $plugin_type ][ $tag ][ 1 ]) {
                                $this->tag_nocache = true;
                                }

                                return call_user_func_array ( $this->smarty->registered_plugins[ $plugin_type ][ $tag ][ 0 ],
                                [ $new_args, $this ] );
                            }
                            // compile registered function or block function
                            if ( $plugin_type == Smarty::PLUGIN_FUNCTION || $plugin_type == Smarty::PLUGIN_BLOCK ) {
                                return $this->callTagCompiler ( 'private_registered_' . $plugin_type, $args, $parameter,
                                $tag );
                            }
                        }
                    }
                    // check plugins from plugins folder
                    foreach ( $this->plugin_search_order as $plugin_type ) {
                        if ( $plugin_type == Smarty::PLUGIN_COMPILER &&
                        $this->smarty->loadPlugin ( 'smarty_compiler_' . $tag ) &&
                        ( !isset( $this->smarty->security_policy ) ||
                        $this->smarty->security_policy->isTrustedTag ( $tag, $this ) )
                        ) {
                            $plugin = 'smarty_compiler_' . $tag;
                            if ( is_callable ( $plugin ) ) {
                                // convert arguments format for old compiler plugins
                                $new_args = [ ];
                                foreach ( $args as $key => $mixed ) {
                                    if ( is_array ( $mixed ) ) {
                                        $new_args = array_merge ( $new_args, $mixed );
                                    } else {
                                        $new_args[ $key ] = $mixed;
                                    }
                                }

                                return $plugin( $new_args, $this->smarty );
                            }
                            if (class_exists($plugin, FALSE ) ) {
                                $plugin_object = new $plugin;
                                if ( method_exists ( $plugin_object, 'compile' ) ) {
                                    return $plugin_object->compile ( $args, $this );
                                }
                            }
                            throw new SmartyException( "Plugin \"{$tag}\" not callable" );
                        } else {
                        if ($function = $this->getPlugin ( $tag, $plugin_type ) ) {
                            if ( !isset( $this->smarty->security_policy ) ||
                            $this->smarty->security_policy->isTrustedTag ( $tag, $this )
                            ) {
                                return $this->callTagCompiler ( 'private_' . $plugin_type . '_plugin', $args, $parameter,
                                $tag, $function );
                            }
                        }
                        }
                    }
                    if ( is_callable ( $this->smarty->default_plugin_handler_func)) {
                    $found = false;
                    // look for already resolved tags
                        foreach ( $this->plugin_search_order as $plugin_type ) {
                            if ( isset( $this->default_handler_plugins[ $plugin_type ][ $tag ] ) ) {
                                $found = TRUE;
                                break;
                            }
                        }
                        if ( !$found ) {
                            // call default handler
                            foreach ( $this->plugin_search_order as $plugin_type ) {
                                if ( $this->getPluginFromDefaultHandler ( $tag, $plugin_type ) ) {
                                    $found = TRUE;
                                    break;
                                }
                            }
                        }
                        if ( $found ) {
                            // if compiler function plugin call it now
                        if ($plugin_type == Smarty::PLUGIN_COMPILER) {
                            $new_args = [ ];
                            foreach ( $args as $mixed ) {
                                $new_args = array_merge ( $new_args, $mixed );
                            }

                            return call_user_func_array ( $this->default_handler_plugins[ $plugin_type ][ $tag ][ 0 ],
                            [ $new_args, $this ] );
                        } else {
                            return $this->callTagCompiler ( 'private_registered_' . $plugin_type, $args, $parameter,
                            $tag);
                        }
                        }
                    }
                } else {
                    // compile closing tag of block function
                    $base_tag = substr ($tag, 0, - 5);
                // check if closing tag is a registered object
                    if ( isset( $this->smarty->registered_objects[ $base_tag ] ) && isset( $parameter[ 'object_method' ] ) ) {
                        $method = $parameter[ 'object_method' ];
                        if ( in_array ( $method, $this->smarty->registered_objects[ $base_tag ][ 3 ] ) ) {
                            return $this->callTagCompiler ( 'private_object_block_function', $args, $parameter, $tag,
                            $method );
                        } else {
                            // throw exception
                            $this->trigger_template_error ( 'not allowed closing tag method "' . $method .
                            '" in registered object "' . $base_tag . '"', NULL, TRUE );
                        }
                    }
                    // registered block tag ?
                    if ( isset( $this->smarty->registered_plugins[ Smarty::PLUGIN_BLOCK ][ $base_tag ] ) ||
                    isset( $this->default_handler_plugins[ Smarty::PLUGIN_BLOCK ][ $base_tag ] )
                    ) {
                        return $this->callTagCompiler ( 'private_registered_block', $args, $parameter, $tag );
                    }
                    // registered function tag ?
                    if ( isset( $this->smarty->registered_plugins[ Smarty::PLUGIN_FUNCTION ][ $tag ] ) ) {
                        return $this->callTagCompiler ( 'private_registered_function', $args, $parameter, $tag );
                    }
                    // block plugin?
                    if ( $function = $this->getPlugin ( $base_tag, Smarty::PLUGIN_BLOCK ) ) {
                        return $this->callTagCompiler ( 'private_block_plugin', $args, $parameter, $tag, $function );
                    }
                    // function plugin?
                    if ( $function = $this->getPlugin ( $tag, Smarty::PLUGIN_FUNCTION ) ) {
                        if ( !isset( $this->smarty->security_policy ) ||
                        $this->smarty->security_policy->isTrustedTag ( $tag, $this )
                        ) {
                            return $this->callTagCompiler ( 'private_function_plugin', $args, $parameter, $tag, $function );
                        }
                    }
                // registered compiler plugin ?
                    if ( isset( $this->smarty->registered_plugins[ Smarty::PLUGIN_COMPILER ][ $tag ])) {
                        // if compiler function plugin call it now
                        $args = [ ];
                        if ( !$this->smarty->registered_plugins[ Smarty::PLUGIN_COMPILER ][ $tag ][ 1 ] ) {
                            $this->tag_nocache = TRUE;
                        }

                        return call_user_func_array ( $this->smarty->registered_plugins[ Smarty::PLUGIN_COMPILER ][ $tag ][ 0 ],
                        array($args, $this ) );
                    }
                    if ( $this->smarty->loadPlugin ( 'smarty_compiler_' . $tag ) ) {
                        $plugin = 'smarty_compiler_' . $tag;
                        if ( is_callable ( $plugin ) ) {
                            return $plugin( $args, $this->smarty );
                        }
                        if ( class_exists ( $plugin, FALSE ) ) {
                            $plugin_object = new $plugin;
                            if ( method_exists ( $plugin_object, 'compile')) {
                                return $plugin_object->compile ( $args, $this );
                            }
                        }
                        throw new SmartyException( "Plugin \"{$tag}\" not callable" );
                    }
                }
                $this->trigger_template_error("unknown tag \"" . $tag . "\"", NULL, TRUE );
            }
        }

        /**
         * Method to compile a Smarty template
 * @param  Smarty_Internal_Template $template template object to compile
         * @param  bool $nocache true is shall be compiled in nocache mode
         * @param null|Smarty_Internal_TemplateCompilerBase $parent_compiler
         * @return bool true if compiling succeeded, false if it failed
         * @throws \Exception
         */
        public function compileTemplate ( Smarty_Internal_Template $template, $nocache = NULL,
        Smarty_Internal_TemplateCompilerBase $parent_compiler = NULL ) {
            // get code frame of compiled template
            $_compiled_code = $template->smarty->ext->_codeFrame->create ( $template,
            $this->compileTemplateSource ( $template, $nocache,
            $parent_compiler ),
            $this->postFilter ( $this->blockOrFunctionCode ) .
            join ( '', $this->mergedSubTemplatesCode ), FALSE,
            $this );

            return $_compiled_code;
        }

        /**
         * Compile template source and run optional post filter
         *
         * @param \Smarty_Internal_Template $template
         * @param null|bool $nocache flag if template must be compiled in nocache mode
         * @param \Smarty_Internal_TemplateCompilerBase $parent_compiler
         *
         * @return string
         * @throws \Exception
         */
        public function compileTemplateSource ( Smarty_Internal_Template $template, $nocache = NULL,
        Smarty_Internal_TemplateCompilerBase $parent_compiler = NULL ) {
            try {
                // save template object in compiler class
                $this->template = $template;
                if ( property_exists ( $this->template->smarty, 'plugin_search_order' ) ) {
                    $this->plugin_search_order = $this->template->smarty->plugin_search_order;
                }
                if ( $this->smarty->debugging ) {
                    if ( !isset( $this->smarty->_debug ) ) {
                        $this->smarty->_debug = new Smarty_Internal_Debug();
                    }
                    $this->smarty->_debug->start_compile ( $this->template );
                }
                if ( isset( $this->template->smarty->security_policy ) ) {
                    $this->php_handling = $this->template->smarty->security_policy->php_handling;
                } else {
                    $this->php_handling = $this->template->smarty->php_handling;
                }
                $this->parent_compiler = $parent_compiler ? $parent_compiler : $this;
                $nocache               = isset( $nocache ) ? $nocache : FALSE;
                if ( empty( $template->compiled->nocache_hash) ) {
                    $template->compiled->nocache_hash = $this->nocache_hash;
                } else {
                    $this->nocache_hash = $template->compiled->nocache_hash;
                }
                // flag for nocache sections
                $this->nocache     = $nocache;
                $this->tag_nocache = FALSE;
                // reset has nocache code flag
                $this->template->compiled->has_nocache_code = FALSE;
                $this->has_variable_string                  = FALSE;
                $this->prefix_code                          = [ ];
                // add file dependency
                if ( $this->smarty->merge_compiled_includes || $this->template->source->handler->checkTimestamps () ) {
                    $this->parent_compiler->template->compiled->file_dependency[ $this->template->source->uid ] =
                    [ $this->template->source->filepath, $this->template->source->getTimeStamp (),
                    $this->template->source->type,
                    ];
                }
                $this->smarty->_current_file = $this->template->source->filepath;
                // get template source
                if ( !empty( $this->template->source->components ) ) {
                    // we have array of inheritance templates by extends: resource
                    // generate corresponding source code sequence
                    $_content =
                    Smarty_Internal_Compile_Extends::extendsSourceArrayCode ( $this->template->source->components );
                } else {
                    // get template source
                    $_content = $this->template->source->getContent ();
                }
                $_compiled_code = $this->postFilter ( $this->doCompile ( $this->preFilter ( $_content ), TRUE ) );
            } catch ( Exception $e ) {
                if ( $this->smarty->debugging ) {
                    $this->smarty->_debug->end_compile ( $this->template );
                }
                $this->_tag_stack = array ();
                // free memory
                $this->parent_compiler = NULL;
                $this->template        = NULL;
                $this->parser          = NULL;
                throw $e;
            }
            if ( $this->smarty->debugging ) {
                $this->smarty->_debug->end_compile ( $this->template );
            }
            $this->parent_compiler = NULL;
            $this->parser          = NULL;

            return $_compiled_code;
        }

        /**
         * compile variable
         * @param string $variable
         * @return string
         */
        public function compileVariable ( $variable ) {
            if ( strpos ( $variable, '(' ) == 0 ) {
                // not a variable variable
                $var               = trim ( $variable, '\'' );
                $this->tag_nocache = $this->tag_nocache |
                $this->template->ext->getTemplateVars->_getVariable ( $this->template, $var, NULL, TRUE,
                FALSE )->nocache
                ;
                // todo $this->template->compiled->properties['variables'][$var] = $this->tag_nocache | $this->nocache;
            }

            return '$_smarty_tpl->tpl_vars[' . $variable . ']->value';
        }

        /**
         * @param array $_attr tag attributes
         * @param array $validScopes
         *
         * @return int|string
         * @throws \SmartyCompilerException
         */
        public function convertScope ( $_attr, $validScopes ) {
            $_scope = 0;
            if ( isset( $_attr[ 'scope' ] ) ) {
                $_scopeName = trim ( $_attr[ 'scope' ], "'\"" );
                if ( is_numeric ( $_scopeName ) && in_array ( $_scopeName, $validScopes ) ) {
                    $_scope = $_scopeName;
                } elseif ( is_string ( $_scopeName ) ) {
                    $_scopeName = trim ( $_scopeName, "'\"" );
                    $_scope     = isset( $validScopes[ $_scopeName ] ) ? $validScopes[ $_scopeName ] : FALSE;
                } else {
                    $_scope = FALSE;
                }
                if ( $_scope === FALSE ) {
                    $err = var_export ( $_scopeName, TRUE );
                    $this->trigger_template_error ( "illegal value '{$err}' for \"scope\" attribute", NULL, true);
                }
            }

            return $_scope;
        }

        /**
         * Get Id
         * @param string $input
         * @return bool|string
         */
        public function getId ( $input ) {
            if ( preg_match ( '~^([\'"]*)([0-9]*[a-zA-Z_]\w*)\1$~', $input, $match ) ) {
                return $match[ 2 ];
            }

            return FALSE;
        }

        /**
         * Get new prefix variable name
         * @return string
         */
        public function getNewPrefixVariable () {
            self::$prefixVariableNumber++;

            return $this->getPrefixVariable ();
        }

        /**
         * Check for plugins and return function name
         * @param         $plugin_name
         * @param  string $plugin_type type of plugin
         * @return string call name of function
         */
        public function getPlugin ( $plugin_name, $plugin_type ) {
            $function = NULL;
            if ( $this->template->caching && ( $this->nocache || $this->tag_nocache ) ) {
                if ( isset( $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ] ) ) {
                    $function =
                    $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ][ 'function' ];
                } elseif ( isset( $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ])) {
                $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ] =
                $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ];
                    $function                                                                                             =
                    $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ][ 'function' ];
            }
        } else {
            if ( isset( $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ])) {
                $function =
                    $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ][ 'function' ];
            } elseif ( isset( $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ])) {
                $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ] =
                $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ];
                $function                                                                                                  =
                $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ][ 'function' ];
            }
        }
        if (isset($function ) ) {
            if ($plugin_type == 'modifier') {
                $this->modifier_plugins[ $plugin_name ] = true;
            }

            return $function;
        }
            // loop through plugin dirs and find the plugin
            $function = 'smarty_' . $plugin_type . '_' . $plugin_name;
            $file     = $this->smarty->loadPlugin($function, FALSE );

            if ( is_string ( $file ) ) {
                if ( $this->template->caching && ( $this->nocache || $this->tag_nocache ) ) {
                    $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ][ 'file' ]     =
                    $file;
                    $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ $plugin_type ][ 'function' ] =
                    $function;
                } else {
                $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ][ 'file' ]     =
                $file;
                $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ $plugin_type ][ 'function' ] =
                $function;
                }
                if ($plugin_type == 'modifier' ) {
                    $this->modifier_plugins[ $plugin_name ] = TRUE;
                }

                return $function;
            }
            if (is_callable($function)) {
                // plugin function is defined in the script
                return $function;
            }

            return FALSE;
        }

    /**
     * Check for plugins by default plugin handler
     * @param  string $tag name of tag
     * @param  string $plugin_type type of plugin
     * @return boolean true if found
     */
        public function getPluginFromDefaultHandler ( $tag, $plugin_type ) {
            $callback  = NULL;
            $script    = NULL;
            $cacheable = TRUE;
            $result    = call_user_func_array ( $this->smarty->default_plugin_handler_func,
            [ $tag, $plugin_type, $this->template, &$callback, &$script, &$cacheable, ] );
            if ( $result ) {
                $this->tag_nocache = $this->tag_nocache || !$cacheable;
                if ( $script !== NULL ) {
                    if ( is_file ( $script ) ) {
                        if ( $this->template->caching && ( $this->nocache || $this->tag_nocache ) ) {
                            $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $tag ][ $plugin_type ][ 'file' ]     =
                            $script;
                            $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $tag ][ $plugin_type ][ 'function' ] =
                            $callback;
                        } else {
                        $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $tag ][ $plugin_type ][ 'file' ]         =
                        $script;
                            $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $tag ][ $plugin_type ][ 'function' ] =
                            $callback;
                        }
                        require_once $script;
                    } else {
                        $this->trigger_template_error ( "Default plugin handler: Returned script file \"{$script}\" for \"{$tag}\" not found");
                }
            }
                if ( is_callable ( $callback ) ) {
                    $this->default_handler_plugins[ $plugin_type ][ $tag ] = [ $callback, TRUE, [ ] ];

                    return TRUE;
                } else {
                    $this->trigger_template_error("Default plugin handler: Returned callback for \"{$tag}\" not callable" );
                }
            }

            return FALSE;
        }

        /**
         * get prefix code string
         *
         * @return string
         */
        public function getPrefixCode () {
            $code                    = '';
            $prefixArray             = array_merge ( $this->prefix_code, array_pop($this->prefixCodeStack ) );
            $this->prefixCodeStack[] = [ ];
            foreach ( $prefixArray as $c ) {
                $code = $this->appendCode ( $code, $c );
            }
            $this->prefix_code = [ ];

            return $code;
        }

        /**
         * Get current prefix variable name
         * @return string
         */
        public function getPrefixVariable () {
            return '$_prefixVariable' . self::$prefixVariableNumber;
        }

        /**
         * Return var_export() value with all white spaces removed
         *
         * @param  mixed $value
         *
         * @return string
         */
        public function getVarExport ( $value ) {
            return preg_replace ( '/\s/', '', var_export ( $value, TRUE ) );
        }

        /**
         * Get variable name from string
         * @param string $input
         * @return bool|string
         */
        public function getVariableName ( $input ) {
            if ( preg_match ( '~^[$]_smarty_tpl->tpl_vars\[[\'"]*([0-9]*[a-zA-Z_]\w*)[\'"]*\]->value$~', $input, $match ) ) {
                return $match[ 1 ];
            }

            return FALSE;
        }

        /**
         * Check if $value contains variable elements
         * @param mixed $value
         * @return bool|int
         */
        public function isVariable ( $value ) {
            if ( is_string ( $value ) ) {
                return preg_match ( '/[$(]/', $value );
            }
            if ( is_bool ( $value ) || is_numeric ( $value ) ) {
                return FALSE;
            }
            if ( is_array ( $value ) ) {
                foreach ( $value as $k => $v ) {
                    if ( $this->isVariable ( $k ) || $this->isVariable ( $v ) ) {
                        return TRUE;
                    }
                }

                return FALSE;
            }

            return FALSE;
        }

        /**
         * Generate nocache code string
         * @param string $code PHP code
         * @return string
         */
        public function makeNocacheCode ( $code ) {
        return "echo '/*%%SmartyNocache:{$this->nocache_hash}%%*/<?php " .
        str_replace ( "^#^", "'", addcslashes ( $code, '\'\\' ) ) .
        "?>/*/%%SmartyNocache:{$this->nocache_hash}%%*/';\n";
        }

        /**
         * Optionally process compiled code by post filter
         * @param string $code compiled code
         * @return string
         * @throws \SmartyException
         */
        public function postFilter ( $code ) {
            // run post filter if on code
            if ( !empty( $code ) &&
            ( isset( $this->smarty->autoload_filters[ 'post' ] ) || isset( $this->smarty->registered_filters[ 'post' ] ) )
            ) {
                return $this->smarty->ext->_filterHandler->runFilter ( 'post', $code, $this->template );
            } else {
                return $code;
            }
        }

        /**
         * Run optional prefilter
         * @param string $_content template source
         * @return string
         * @throws \SmartyException
         */
        public function preFilter ( $_content ) {
            // run pre filter if required
            if ($_content != '' &&
            ( ( isset( $this->smarty->autoload_filters[ 'pre' ] ) || isset( $this->smarty->registered_filters[ 'pre' ] ) ) )
            ) {
                return $this->smarty->ext->_filterHandler->runFilter ( 'pre', $_content, $this->template );
            } else {
                return $_content;
            }
        }

        /**
         * Inject inline code for nocache template sections
         * This method gets the content of each template element from the parser.
         * If the content is compiled code and it should be not cached the code is injected
         * into the rendered output.
         * @param  string $content content of template element
         * @param  boolean $is_code true if content is compiled code
         * @return string  content
         */
        public function processNocacheCode ( $content, $is_code ) {
            // If the template is not evaluated and we have a nocache section and or a nocache tag
            if ( $is_code && !empty( $content ) ) {
                // generate replacement code
                if ( ( !( $this->template->source->handler->recompiled ) || $this->forceNocache ) && $this->template->caching &&
                !$this->suppressNocacheProcessing && ( $this->nocache || $this->tag_nocache )
                ) {
                    $this->template->compiled->has_nocache_code = TRUE;
                    $_output                                    = addcslashes ( $content, '\'\\' );
                    $_output                                    = str_replace ( "^#^", "'", $_output );
                    $_output                                    = "<?php echo '/*%%SmartyNocache:{$this->nocache_hash}%%*/" . $_output .
                    "/*/%%SmartyNocache:{$this->nocache_hash}%%*/';?>\n";
                    // make sure we include modifier plugins for nocache code
                    foreach ( $this->modifier_plugins as $plugin_name => $dummy ) {
                        if ( isset( $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ 'modifier' ] ) ) {
                            $this->parent_compiler->template->compiled->required_plugins[ 'nocache' ][ $plugin_name ][ 'modifier' ] =
                            $this->parent_compiler->template->compiled->required_plugins[ 'compiled' ][ $plugin_name ][ 'modifier' ];
                        }
                    }
                } else {
                    $_output = $content;
                }
            } else {
                $_output = $content;
        }
        $this->modifier_plugins = array();
        $this->suppressNocacheProcessing = false;
        $this->tag_nocache = false;

        return $_output;
    }

    /**
     * This method is called from parser to process a text content section
     * - remove text from inheritance child templates as they may generate output
     * - strip text if strip is enabled
     * @param string $text
     * @return null|\Smarty_Internal_ParseTree_Text
     */
        public function processText ( $text ) {
            if ( (string) $text != '' ) {
                $store  = [ ];
                $_store = 0;
                if ( $this->parser->strip ) {
                    if ( strpos ( $text, '<' ) !== FALSE ) {
                        // capture html elements not to be messed with
                        $_offset = 0;
                        if ( preg_match_all ( '#(<script[^>]*>.*?</script[^>]*>)|(<textarea[^>]*>.*?</textarea[^>]*>)|(<pre[^>]*>.*?</pre[^>]*>)#is',
                        $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) ) {
                            foreach ( $matches as $match ) {
                                $store[] = $match[ 0 ][ 0 ];
                                $_length = strlen ( $match[ 0 ][ 0 ] );
                                $replace = '@!@SMARTY:' . $_store . ':SMARTY@!@';
                                $text    = substr_replace ( $text, $replace, $match[ 0 ][ 1 ] - $_offset, $_length );

                                $_offset += $_length - strlen ( $replace );
                                $_store++;
                            }
                        }
                        $expressions = array (// replace multiple spaces between tags by a single space
                        '#(:SMARTY@!@|>)[\040\011]+(?=@!@SMARTY:|<)#s'                            => '\1 \2',
                            // remove newline between tags
                        '#(:SMARTY@!@|>)[\040\011]*[\n]\s*(?=@!@SMARTY:|<)#s'                     => '\1\2',
                            // remove multiple spaces between attributes (but not in attribute values!)
                        '#(([a-z0-9]\s*=\s*("[^"]*?")|(\'[^\']*?\'))|<[a-z0-9_]+)\s+([a-z/>])#is' => '\1 \5',
                        '#>[\040\011]+$#Ss'                                                       => '> ', '#>[\040\011]*[\n]\s*$#Ss' => '>',
                        $this->stripRegEx                                                         => '',
                        );

                        $text    = preg_replace ( array_keys ( $expressions ), array_values ( $expressions ), $text );
                        $_offset = 0;
                        if ( preg_match_all ( '#@!@SMARTY:([0-9]+):SMARTY@!@#is', $text, $matches,
                        PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) ) {
                            foreach ( $matches as $match ) {
                                $_length = strlen ( $match[ 0 ][ 0 ] );
                                $replace = $store[ $match[ 1 ][ 0 ] ];
                                $text    = substr_replace ( $text, $replace, $match[ 0 ][ 1 ] + $_offset, $_length );

                                $_offset += strlen ( $replace ) - $_length;
                                $_store++;
                            }
                        }
                    } else {
                        $text = preg_replace ( $this->stripRegEx, '', $text );
                    }
                }

                return new Smarty_Internal_ParseTree_Text( $text );
            }

            return NULL;
        }

        /**
         * Set nocache flag in variable or create new variable
     *
     * @param string $varName
         */
        public function setNocacheInVariable ( $varName)
    {
        // create nocache var to make it know for further compiling
        if ( $_var = $this->getId ( $varName ) ) {
            if ( isset( $this->template->tpl_vars[ $_var ] ) ) {
                $this->template->tpl_vars[ $_var ]          = clone $this->template->tpl_vars[ $_var ];
                $this->template->tpl_vars[ $_var ]->nocache = TRUE;
            } else {
                $this->template->tpl_vars[ $_var ] = new Smarty_Variable( NULL, TRUE );
            }
        }
    }

        /**
         * display compiler error messages without dying
         * If parameter $args is empty it is a parser detected syntax error.
         * In this case the parser is called to obtain information about expected tokens.
         * If parameter $args contains a string this is used as error message
         * @param  string $args individual error message or null
         * @param  string $line line-number
         * @param null|bool $tagline if true the line number of last tag
         * @throws \SmartyCompilerException when an unexpected token is found
         */
        public function trigger_template_error ( $args = NULL, $line = NULL, $tagline = NULL ) {
            $lex = $this->parser->lex;
            if ( $tagline === TRUE ) {
                // get line number of Tag
                $line = $lex->taglineno;
            } elseif ( !isset( $line ) ) {
                // get template source line which has error
                $line = $lex->line;
            } else {
                $line = (int) $line;
            }

            if ( in_array ( $this->template->source->type, [ 'eval', 'string' ] ) ) {
                $templateName = $this->template->source->type . ':' . trim ( preg_replace ( '![\t\r\n]+!', ' ',
                strlen ( $lex->data ) > 40 ?
                substr ( $lex->data, 0, 40 ) .
                '...' : $lex->data ) );
            } else {
                $templateName = $this->template->source->type . ':' . $this->template->source->filepath;
            }

            //        $line += $this->trace_line_offset;
            $match      = preg_split ( "/\n/", $lex->data );
            $error_text =
            'Syntax error in template "' . ( empty( $this->trace_filepath ) ? $templateName : $this->trace_filepath ) .
            '"  on line ' . ( $line + $this->trace_line_offset ) . ' "' .
            trim ( preg_replace ( '![\t\r\n]+!', ' ', $match[ $line - 1 ] ) ) . '" ';
            if ( isset( $args ) ) {
                // individual error message
                $error_text .= $args;
            } else {
                $expect = [ ];
                // expected token from parser
                $error_text .= ' - Unexpected "' . $lex->value . '"';
                if ( count ( $this->parser->yy_get_expected_tokens ( $this->parser->yymajor ) ) <= 4 ) {
                    foreach ( $this->parser->yy_get_expected_tokens ( $this->parser->yymajor ) as $token ) {
                        $exp_token = $this->parser->yyTokenName[ $token ];
                        if ( isset( $lex->smarty_token_names[ $exp_token ] ) ) {
                            // token type from lexer
                            $expect[] = '"' . $lex->smarty_token_names[ $exp_token ] . '"';
                        } else {
                            // otherwise internal token name
                            $expect[] = $this->parser->yyTokenName[ $token ];
                        }
                    }
                    $error_text .= ', expected one of: ' . implode ( ' , ', $expect );
                }
            }
            $e         = new SmartyCompilerException( $error_text );
            $e->line   = $line;
            $e->source = trim ( preg_replace ( '![\t\r\n]+!', ' ', $match[ $line - 1 ] ) );
            $e->desc = $args;
        $e->template = $this->template->source->filepath;
            throw $e;
        }

    }
