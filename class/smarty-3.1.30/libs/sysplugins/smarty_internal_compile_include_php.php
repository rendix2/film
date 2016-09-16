<?php
    /**
     * Smarty Internal Plugin Compile Include PHP
     * Compiles the {include_php} tag
     * @package    Smarty
     * @subpackage Compiler
     * @author     Uwe Tews
     */

/**
 * Smarty Internal Plugin Compile Insert Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Include_Php extends Smarty_Internal_CompileBase {
    /**
     * Attribute definition: Overwrites base class.
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = [ 'file' ];

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = [ 'file' ];

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = [ 'once', 'assign' ];

    /**
     * Compiles code for the {include_php} tag
     *
     * @param  array $args array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile ( $args, Smarty_Internal_TemplateCompilerBase $compiler ) {
        if ( !( $compiler->smarty instanceof SmartyBC ) ) {
            throw new SmartyException( "{include_php} is deprecated, use SmartyBC class to enable" );
        }
        // check and get attributes
        $_attr = $this->getAttributes ( $compiler, $args );

        /** @var Smarty_Internal_Template $_smarty_tpl
         * used in evaluated code
         */
        $_smarty_tpl = $compiler->template;
        $_filepath   = FALSE;
        $_file       = NULL;
        eval( '$_file = @' . $_attr[ 'file' ] . ';' );
        if ( !isset( $compiler->smarty->security_policy ) && file_exists ( $_file ) ) {
            $_filepath = $compiler->smarty->_realpath ( $_file, TRUE );
        } else {
            if ( isset( $compiler->smarty->security_policy ) ) {
                $_dir = $compiler->smarty->security_policy->trusted_dir;
            } else {
                $_dir = $compiler->smarty->trusted_dir;
            }
            if ( !empty( $_dir ) ) {
                foreach ( (array) $_dir as $_script_dir ) {
                    $_path = $compiler->smarty->_realpath ( $_script_dir . DS . $_file, TRUE );
                    if ( file_exists ( $_path ) ) {
                        $_filepath = $_path;
                        break;
                    }
                }
            }
        }
        if ( $_filepath == FALSE ) {
            $compiler->trigger_template_error ( "{include_php} file '{$_file}' is not readable", NULL, TRUE );
        }

        if ( isset( $compiler->smarty->security_policy ) ) {
            $compiler->smarty->security_policy->isTrustedPHPDir ( $_filepath );
        }

        if ( isset( $_attr[ 'assign' ] ) ) {
            // output will be stored in a smarty variable instead of being displayed
            $_assign = $_attr[ 'assign' ];
        }
        $_once = '_once';
        if ( isset( $_attr[ 'once' ] ) ) {
            if ( $_attr[ 'once' ] == 'false' ) {
                $_once = '';
            }
        }

        if ( isset( $_assign ) ) {
            return "<?php ob_start();\ninclude{$_once} ('{$_filepath}');\n\$_smarty_tpl->assign({$_assign},ob_get_clean());\n?>";
        } else {
            return "<?php include{$_once} ('{$_filepath}');?>\n";
        }
    }
}
