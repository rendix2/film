<?php
    /**
     * Smarty Internal TestInstall
     * Test Smarty installation
     * @package    Smarty
     * @subpackage Utilities
     * @author     Uwe Tews
     */

    /**
     * TestInstall class
     * @package    Smarty
     * @subpackage Utilities
     */
    class Smarty_Internal_TestInstall {
        /**
         * diagnose Smarty setup
         * If $errors is secified, the diagnostic report will be appended to the array, rather than being output.
         * @param \Smarty $smarty
         * @param  array $errors array to push results into rather than outputting them
         * @return bool status, true if everything is fine, false else
         */
        public static function testInstall ( Smarty $smarty, &$errors = NULL ) {
            $status = TRUE;

            if ( $errors === NULL ) {
                echo "<PRE>\n";
                echo "Smarty Installation test...\n";
                echo "Testing template directory...\n";
            }

            $_stream_resolve_include_path = function_exists ( 'stream_resolve_include_path' );

            // test if all registered template_dir are accessible
            foreach ( $smarty->getTemplateDir () as $template_dir ) {
                $_template_dir = $template_dir;
                $template_dir  = realpath ( $template_dir );
                // resolve include_path or fail existence
                if ( !$template_dir ) {
                    if ( $smarty->use_include_path && !preg_match ( '/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_template_dir ) ) {
                        // try PHP include_path
                        if ( $_stream_resolve_include_path ) {
                            $template_dir = stream_resolve_include_path ( $_template_dir );
                        } else {
                            $template_dir = $smarty->ext->_getIncludePath->getIncludePath ( $_template_dir, NULL, $smarty );
                        }

                        if ( $template_dir !== FALSE ) {
                            if ( $errors === NULL ) {
                                echo "$template_dir is OK.\n";
                            }

                            continue;
                        } else {
                            $status  = FALSE;
                            $message =
                            "FAILED: $_template_dir does not exist (and couldn't be found in include_path either)";
                            if ( $errors === NULL ) {
                                echo $message . ".\n";
                            } else {
                                $errors[ 'template_dir' ] = $message;
                            }

                            continue;
                        }
                    } else {
                        $status  = FALSE;
                        $message = "FAILED: $_template_dir does not exist";
                        if ( $errors === NULL ) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'template_dir' ] = $message;
                        }

                        continue;
                    }
                }

                if ( !is_dir ( $template_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $template_dir is not a directory";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'template_dir' ] = $message;
                    }
                } elseif ( !is_readable ( $template_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $template_dir is not readable";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'template_dir' ] = $message;
                    }
                } else {
                    if ( $errors === NULL ) {
                        echo "$template_dir is OK.\n";
                    }
                }
            }

            if ( $errors === NULL ) {
                echo "Testing compile directory...\n";
            }

            // test if registered compile_dir is accessible
            $__compile_dir = $smarty->getCompileDir ();
            $_compile_dir  = realpath ( $__compile_dir );
            if ( !$_compile_dir ) {
                $status  = FALSE;
                $message = "FAILED: {$__compile_dir} does not exist";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'compile_dir' ] = $message;
                }
            } elseif ( !is_dir ( $_compile_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_compile_dir} is not a directory";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'compile_dir' ] = $message;
                }
            } elseif ( !is_readable ( $_compile_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_compile_dir} is not readable";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'compile_dir' ] = $message;
                }
            } elseif ( !is_writable ( $_compile_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_compile_dir} is not writable";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'compile_dir' ] = $message;
                }
            } else {
                if ( $errors === NULL ) {
                    echo "{$_compile_dir} is OK.\n";
                }
            }

            if ( $errors === NULL ) {
                echo "Testing plugins directory...\n";
            }

            // test if all registered plugins_dir are accessible
            // and if core plugins directory is still registered
            $_core_plugins_dir       = realpath ( dirname ( __FILE__ ) . '/../plugins' );
            $_core_plugins_available = FALSE;
            foreach ( $smarty->getPluginsDir () as $plugin_dir ) {
                $_plugin_dir = $plugin_dir;
                $plugin_dir  = realpath ( $plugin_dir );
                // resolve include_path or fail existence
                if ( !$plugin_dir ) {
                    if ( $smarty->use_include_path && !preg_match ( '/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir ) ) {
                        // try PHP include_path
                        if ( $_stream_resolve_include_path ) {
                            $plugin_dir = stream_resolve_include_path ( $_plugin_dir );
                        } else {
                            $plugin_dir = $smarty->ext->_getIncludePath->getIncludePath ( $_plugin_dir, NULL, $smarty );
                        }

                        if ( $plugin_dir !== FALSE ) {
                            if ( $errors === NULL ) {
                                echo "$plugin_dir is OK.\n";
                            }

                            continue;
                        } else {
                            $status  = FALSE;
                            $message = "FAILED: $_plugin_dir does not exist (and couldn't be found in include_path either)";
                            if ( $errors === NULL ) {
                                echo $message . ".\n";
                            } else {
                                $errors[ 'plugins_dir' ] = $message;
                            }

                            continue;
                        }
                    } else {
                        $status  = FALSE;
                        $message = "FAILED: $_plugin_dir does not exist";
                        if ( $errors === NULL ) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'plugins_dir' ] = $message;
                        }

                        continue;
                    }
                }

                if ( !is_dir ( $plugin_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $plugin_dir is not a directory";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'plugins_dir' ] = $message;
                    }
                } elseif ( !is_readable ( $plugin_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $plugin_dir is not readable";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'plugins_dir' ] = $message;
                    }
                } elseif ( $_core_plugins_dir && $_core_plugins_dir == realpath ( $plugin_dir ) ) {
                    $_core_plugins_available = TRUE;
                    if ( $errors === NULL ) {
                        echo "$plugin_dir is OK.\n";
                    }
                } else {
                    if ( $errors === NULL ) {
                        echo "$plugin_dir is OK.\n";
                    }
                }
            }
            if ( !$_core_plugins_available ) {
                $status  = FALSE;
                $message = "WARNING: Smarty's own libs/plugins is not available";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } elseif ( !isset( $errors[ 'plugins_dir' ] ) ) {
                    $errors[ 'plugins_dir' ] = $message;
                }
            }

            if ( $errors === NULL ) {
                echo "Testing cache directory...\n";
            }

            // test if all registered cache_dir is accessible
            $__cache_dir = $smarty->getCacheDir ();
            $_cache_dir  = realpath ( $__cache_dir );
            if ( !$_cache_dir ) {
                $status  = FALSE;
                $message = "FAILED: {$__cache_dir} does not exist";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'cache_dir' ] = $message;
                }
            } elseif ( !is_dir ( $_cache_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_cache_dir} is not a directory";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'cache_dir' ] = $message;
                }
            } elseif ( !is_readable ( $_cache_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_cache_dir} is not readable";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'cache_dir' ] = $message;
                }
            } elseif ( !is_writable ( $_cache_dir ) ) {
                $status  = FALSE;
                $message = "FAILED: {$_cache_dir} is not writable";
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'cache_dir' ] = $message;
                }
            } else {
                if ( $errors === NULL ) {
                    echo "{$_cache_dir} is OK.\n";
                }
            }

            if ( $errors === NULL ) {
                echo "Testing configs directory...\n";
            }

            // test if all registered config_dir are accessible
            foreach ( $smarty->getConfigDir () as $config_dir ) {
                $_config_dir = $config_dir;
                // resolve include_path or fail existence
                if ( !$config_dir ) {
                    if ( $smarty->use_include_path && !preg_match ( '/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_config_dir ) ) {
                        // try PHP include_path
                        if ( $_stream_resolve_include_path ) {
                            $config_dir = stream_resolve_include_path ( $_config_dir );
                        } else {
                            $config_dir = $smarty->ext->_getIncludePath->getIncludePath ( $_config_dir, NULL, $smarty );
                        }

                        if ( $config_dir !== FALSE ) {
                            if ( $errors === NULL ) {
                                echo "$config_dir is OK.\n";
                            }

                            continue;
                        } else {
                            $status  = FALSE;
                            $message = "FAILED: $_config_dir does not exist (and couldn't be found in include_path either)";
                            if ( $errors === NULL ) {
                                echo $message . ".\n";
                            } else {
                                $errors[ 'config_dir' ] = $message;
                            }

                            continue;
                        }
                    } else {
                        $status  = FALSE;
                        $message = "FAILED: $_config_dir does not exist";
                        if ( $errors === NULL ) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'config_dir' ] = $message;
                        }

                        continue;
                    }
                }

                if ( !is_dir ( $config_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $config_dir is not a directory";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'config_dir' ] = $message;
                    }
                } elseif ( !is_readable ( $config_dir ) ) {
                    $status  = FALSE;
                    $message = "FAILED: $config_dir is not readable";
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'config_dir' ] = $message;
                    }
                } else {
                    if ( $errors === NULL ) {
                        echo "$config_dir is OK.\n";
                    }
                }
            }

            if ( $errors === NULL ) {
                echo "Testing sysplugin files...\n";
            }
            // test if sysplugins are available
            $source = SMARTY_SYSPLUGINS_DIR;
            if ( is_dir ( $source ) ) {
                $expectedSysplugins = [ 'smartycompilerexception.php'                               => TRUE, 'smartyexception.php' => TRUE,
                                        'smarty_cacheresource.php'                                  => TRUE, 'smarty_cacheresource_custom.php' => TRUE,
                                        'smarty_cacheresource_keyvaluestore.php'                    => TRUE, 'smarty_data.php' => TRUE,
                                        'smarty_internal_block.php'                                 => TRUE,
                                        'smarty_internal_cacheresource_file.php'                    => TRUE,
                                        'smarty_internal_compilebase.php'                           => TRUE,
                                        'smarty_internal_compile_append.php'                        => TRUE,
                                        'smarty_internal_compile_assign.php'                        => TRUE,
                                        'smarty_internal_compile_block.php'                         => TRUE,
                                        'smarty_internal_compile_break.php'                         => TRUE,
                                        'smarty_internal_compile_call.php'                          => TRUE,
                                        'smarty_internal_compile_capture.php'                       => TRUE,
                                        'smarty_internal_compile_config_load.php'                   => TRUE,
                                        'smarty_internal_compile_continue.php'                      => TRUE,
                                        'smarty_internal_compile_debug.php'                         => TRUE,
                                        'smarty_internal_compile_eval.php'                          => TRUE,
                                        'smarty_internal_compile_extends.php'                       => TRUE,
                                        'smarty_internal_compile_for.php'                           => TRUE,
                                        'smarty_internal_compile_foreach.php'                       => TRUE,
                                        'smarty_internal_compile_function.php'                      => TRUE,
                                        'smarty_internal_compile_if.php'                            => TRUE,
                                        'smarty_internal_compile_include.php'                       => TRUE,
                                        'smarty_internal_compile_include_php.php'                   => TRUE,
                                        'smarty_internal_compile_insert.php'                        => TRUE,
                                        'smarty_internal_compile_ldelim.php'                        => TRUE,
                                        'smarty_internal_compile_make_nocache.php'                  => TRUE,
                                        'smarty_internal_compile_nocache.php'                       => TRUE,
                                        'smarty_internal_compile_private_block_plugin.php'          => TRUE,
                                        'smarty_internal_compile_private_foreachsection.php'        => TRUE,
                                        'smarty_internal_compile_private_function_plugin.php'       => TRUE,
                                        'smarty_internal_compile_private_modifier.php'              => TRUE,
                                        'smarty_internal_compile_private_object_block_function.php' => TRUE,
                                        'smarty_internal_compile_private_object_function.php'       => TRUE,
                                        'smarty_internal_compile_private_php.php'                   => TRUE,
                                        'smarty_internal_compile_private_print_expression.php'      => TRUE,
                                        'smarty_internal_compile_private_registered_block.php'      => TRUE,
                                        'smarty_internal_compile_private_registered_function.php'   => TRUE,
                                        'smarty_internal_compile_private_special_variable.php'      => TRUE,
                                        'smarty_internal_compile_rdelim.php'                        => TRUE,
                                        'smarty_internal_compile_section.php'                       => TRUE,
                                        'smarty_internal_compile_setfilter.php'                     => TRUE,
                                        'smarty_internal_compile_shared_inheritance.php'            => TRUE,
                                        'smarty_internal_compile_while.php'                         => TRUE,
                                        'smarty_internal_configfilelexer.php'                       => TRUE,
                                        'smarty_internal_configfileparser.php'                      => TRUE,
                                        'smarty_internal_config_file_compiler.php'                  => TRUE,
                                        'smarty_internal_data.php'                                  => TRUE, 'smarty_internal_debug.php' => TRUE,
                                        'smarty_internal_extension_clear.php'                       => TRUE,
                                        'smarty_internal_extension_handler.php'                     => TRUE,
                                        'smarty_internal_method_addautoloadfilters.php'             => TRUE,
                                        'smarty_internal_method_adddefaultmodifiers.php'            => TRUE,
                                        'smarty_internal_method_append.php'                         => TRUE,
                                        'smarty_internal_method_appendbyref.php'                    => TRUE,
                                        'smarty_internal_method_assignbyref.php'                    => TRUE,
                                        'smarty_internal_method_assignglobal.php'                   => TRUE,
                                        'smarty_internal_method_clearallassign.php'                 => TRUE,
                                        'smarty_internal_method_clearallcache.php'                  => TRUE,
                                        'smarty_internal_method_clearassign.php'                    => TRUE,
                                        'smarty_internal_method_clearcache.php'                     => TRUE,
                                        'smarty_internal_method_clearcompiledtemplate.php'          => TRUE,
                                        'smarty_internal_method_clearconfig.php'                    => TRUE,
                                        'smarty_internal_method_compileallconfig.php'               => TRUE,
                                        'smarty_internal_method_compilealltemplates.php'            => TRUE,
                                        'smarty_internal_method_configload.php'                     => TRUE,
                                        'smarty_internal_method_createdata.php'                     => TRUE,
                                        'smarty_internal_method_getautoloadfilters.php'             => TRUE,
                                        'smarty_internal_method_getconfigvars.php'                  => TRUE,
                                        'smarty_internal_method_getdebugtemplate.php'               => TRUE,
                                        'smarty_internal_method_getdefaultmodifiers.php'            => TRUE,
                                        'smarty_internal_method_getglobal.php'                      => TRUE,
                                        'smarty_internal_method_getregisteredobject.php'            => TRUE,
                                        'smarty_internal_method_getstreamvariable.php'              => TRUE,
                                        'smarty_internal_method_gettags.php'                        => TRUE,
                                        'smarty_internal_method_gettemplatevars.php'                => TRUE,
                                        'smarty_internal_method_loadfilter.php'                     => TRUE,
                                        'smarty_internal_method_loadplugin.php'                     => TRUE,
                                        'smarty_internal_method_mustcompile.php'                    => TRUE,
                                        'smarty_internal_method_registercacheresource.php'          => TRUE,
                                        'smarty_internal_method_registerclass.php'                  => TRUE,
                                        'smarty_internal_method_registerdefaultconfighandler.php'   => TRUE,
                                        'smarty_internal_method_registerdefaultpluginhandler.php'   => TRUE,
                                        'smarty_internal_method_registerdefaulttemplatehandler.php' => TRUE,
                                        'smarty_internal_method_registerfilter.php'                 => TRUE,
                                        'smarty_internal_method_registerobject.php'                 => TRUE,
                                        'smarty_internal_method_registerplugin.php'                 => TRUE,
                                        'smarty_internal_method_registerresource.php'               => TRUE,
                                        'smarty_internal_method_setautoloadfilters.php'             => TRUE,
                                        'smarty_internal_method_setdebugtemplate.php'               => TRUE,
                                        'smarty_internal_method_setdefaultmodifiers.php'            => TRUE,
                                        'smarty_internal_method_unloadfilter.php'                   => TRUE,
                                        'smarty_internal_method_unregistercacheresource.php'        => TRUE,
                                        'smarty_internal_method_unregisterfilter.php'               => TRUE,
                                        'smarty_internal_method_unregisterobject.php'               => TRUE,
                                        'smarty_internal_method_unregisterplugin.php'               => TRUE,
                                        'smarty_internal_method_unregisterresource.php'             => TRUE,
                                        'smarty_internal_nocache_insert.php'                        => TRUE,
                                        'smarty_internal_parsetree.php'                             => TRUE,
                                        'smarty_internal_parsetree_code.php'                        => TRUE,
                                        'smarty_internal_parsetree_dq.php'                          => TRUE,
                                        'smarty_internal_parsetree_dqcontent.php'                   => TRUE,
                                        'smarty_internal_parsetree_tag.php'                         => TRUE,
                                        'smarty_internal_parsetree_template.php'                    => TRUE,
                                        'smarty_internal_parsetree_text.php'                        => TRUE,
                                        'smarty_internal_resource_eval.php'                         => TRUE,
                                        'smarty_internal_resource_extends.php'                      => TRUE,
                                        'smarty_internal_resource_file.php'                         => TRUE,
                                        'smarty_internal_resource_php.php'                          => TRUE,
                                        'smarty_internal_resource_registered.php'                   => TRUE,
                                        'smarty_internal_resource_stream.php'                       => TRUE,
                                        'smarty_internal_resource_string.php'                       => TRUE,
                                        'smarty_internal_runtime_cachemodify.php'                   => TRUE,
                                        'smarty_internal_runtime_capture.php'                       => TRUE,
                                        'smarty_internal_runtime_codeframe.php'                     => TRUE,
                                        'smarty_internal_runtime_filterhandler.php'                 => TRUE,
                                        'smarty_internal_runtime_foreach.php'                       => TRUE,
                                        'smarty_internal_runtime_getincludepath.php'                => TRUE,
                                        'smarty_internal_runtime_inheritance.php'                   => TRUE,
                                        'smarty_internal_runtime_make_nocache.php'                  => TRUE,
                                        'smarty_internal_runtime_tplfunction.php'                   => TRUE,
                                        'smarty_internal_runtime_updatecache.php'                   => TRUE,
                                        'smarty_internal_runtime_updatescope.php'                   => TRUE,
                                        'smarty_internal_runtime_writefile.php'                     => TRUE,
                                        'smarty_internal_smartytemplatecompiler.php'                => TRUE,
                                        'smarty_internal_template.php'                              => TRUE,
                                        'smarty_internal_templatebase.php'                          => TRUE,
                                        'smarty_internal_templatecompilerbase.php'                  => TRUE,
                                        'smarty_internal_templatelexer.php'                         => TRUE,
                                        'smarty_internal_templateparser.php'                        => TRUE,
                                        'smarty_internal_testinstall.php'                           => TRUE,
                                        'smarty_internal_undefined.php'                             => TRUE, 'smarty_resource.php' => TRUE,
                                        'smarty_resource_custom.php'                                => TRUE, 'smarty_resource_recompiled.php' => TRUE,
                                        'smarty_resource_uncompiled.php'                            => TRUE, 'smarty_security.php' => TRUE,
                                        'smarty_template_cached.php'                                => TRUE, 'smarty_template_compiled.php' => TRUE,
                                        'smarty_template_config.php'                                => TRUE,
                                        'smarty_template_resource_base.php'                         => TRUE,
                                        'smarty_template_source.php'                                => TRUE, 'smarty_undefined_variable.php' => TRUE,
                                        'smarty_variable.php'                                       => TRUE,
                ];
                $iterator           = new DirectoryIterator( $source );
                foreach ( $iterator as $file ) {
                    if ( !$file->isDot () ) {
                        $filename = $file->getFilename ();
                        if ( isset( $expectedSysplugins[ $filename ] ) ) {
                            unset( $expectedSysplugins[ $filename ] );
                        }
                    }
                }
                if ( $expectedSysplugins ) {
                    $status  = FALSE;
                    $message = "FAILED: files missing from libs/sysplugins: " . join ( ', ', array_keys ( $expectedSysplugins ) );
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'sysplugins' ] = $message;
                    }
                } elseif ( $errors === NULL ) {
                    echo "... OK\n";
                }
            } else {
                $status  = FALSE;
                $message = "FAILED: " . SMARTY_SYSPLUGINS_DIR . ' is not a directory';
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'sysplugins_dir_constant' ] = $message;
                }
            }

            if ( $errors === NULL ) {
                echo "Testing plugin files...\n";
            }
            // test if core plugins are available
            $source = SMARTY_PLUGINS_DIR;
            if ( is_dir ( $source ) ) {
                $expectedPlugins =
                [ 'block.textformat.php'                  => TRUE, 'function.counter.php' => TRUE, 'function.cycle.php' => TRUE,
                  'function.fetch.php'                    => TRUE, 'function.html_checkboxes.php' => TRUE,
                  'function.html_image.php'               => TRUE, 'function.html_options.php' => TRUE,
                  'function.html_radios.php'              => TRUE, 'function.html_select_date.php' => TRUE,
                  'function.html_select_time.php'         => TRUE, 'function.html_table.php' => TRUE,
                  'function.mailto.php'                   => TRUE, 'function.math.php' => TRUE, 'modifier.capitalize.php' => TRUE,
                  'modifier.date_format.php'              => TRUE, 'modifier.debug_print_var.php' => TRUE,
                  'modifier.escape.php'                   => TRUE, 'modifier.regex_replace.php' => TRUE,
                  'modifier.replace.php'                  => TRUE, 'modifier.spacify.php' => TRUE, 'modifier.truncate.php' => TRUE,
                  'modifiercompiler.cat.php'              => TRUE, 'modifiercompiler.count_characters.php' => TRUE,
                  'modifiercompiler.count_paragraphs.php' => TRUE, 'modifiercompiler.count_sentences.php' => TRUE,
                  'modifiercompiler.count_words.php'      => TRUE, 'modifiercompiler.default.php' => TRUE,
                  'modifiercompiler.escape.php'           => TRUE, 'modifiercompiler.from_charset.php' => TRUE,
                  'modifiercompiler.indent.php'           => TRUE, 'modifiercompiler.lower.php' => TRUE,
                  'modifiercompiler.noprint.php'          => TRUE, 'modifiercompiler.string_format.php' => TRUE,
                  'modifiercompiler.strip.php'            => TRUE, 'modifiercompiler.strip_tags.php' => TRUE,
                  'modifiercompiler.to_charset.php'       => TRUE, 'modifiercompiler.unescape.php' => TRUE,
                  'modifiercompiler.upper.php'            => TRUE, 'modifiercompiler.wordwrap.php' => TRUE,
                  'outputfilter.trimwhitespace.php'       => TRUE, 'shared.escape_special_chars.php' => TRUE,
                  'shared.literal_compiler_param.php'     => TRUE, 'shared.make_timestamp.php' => TRUE,
                  'shared.mb_str_replace.php'             => TRUE, 'shared.mb_unicode.php' => TRUE,
                  'shared.mb_wordwrap.php'                => TRUE, 'variablefilter.htmlspecialchars.php' => TRUE,
                ];
                $iterator        = new DirectoryIterator( $source );
                foreach ( $iterator as $file ) {
                    if ( !$file->isDot () ) {
                        $filename = $file->getFilename ();
                        if ( isset( $expectedPlugins[ $filename ] ) ) {
                            unset( $expectedPlugins[ $filename ] );
                        }
                    }
                }
                if ( $expectedPlugins ) {
                    $status  = FALSE;
                    $message = "FAILED: files missing from libs/plugins: " . join ( ', ', array_keys ( $expectedPlugins ) );
                    if ( $errors === NULL ) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'plugins' ] = $message;
                    }
                } elseif ( $errors === NULL ) {
                    echo "... OK\n";
                }
            } else {
                $status  = FALSE;
                $message = "FAILED: " . SMARTY_PLUGINS_DIR . ' is not a directory';
                if ( $errors === NULL ) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'plugins_dir_constant' ] = $message;
                }
            }

            if ( $errors === NULL ) {
                echo "Tests complete.\n";
                echo "</PRE>\n";
            }

            return $status;
        }
    }
