<?php
    /**
     * Smarty Internal Plugin Compile Shared Inheritance
     * Shared methods for {extends} and {block} tags
     * @package    Smarty
     * @subpackage Compiler
     * @author     Uwe Tews
     */

/**
 * Smarty Internal Plugin Compile Shared Inheritance Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Shared_Inheritance extends Smarty_Internal_CompileBase {
    /**
     * Compile inheritance initialization code as prefix
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false $initChildSequence if true force child template
     */
    static function postCompile ( Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = FALSE ) {
        $compiler->prefixCompiledCode .= "<?php \$_smarty_tpl->_loadInheritance();\n\$_smarty_tpl->inheritance->init(\$_smarty_tpl, " .
        var_export ( $initChildSequence, TRUE ) . ");\n?>\n";
    }

    /**
     * Register post compile callback to compile inheritance initialization code
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false $initChildSequence if true force child template
     */
    public function registerInit ( Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = FALSE ) {
        if ( $initChildSequence || !isset( $compiler->_cache[ 'inheritanceInit' ] ) ) {
            $compiler->registerPostCompileCallback ( [ 'Smarty_Internal_Compile_Shared_Inheritance', 'postCompile' ],
            [ $initChildSequence ], 'inheritanceInit', $initChildSequence );

            $compiler->_cache[ 'inheritanceInit' ] = TRUE;
        }
    }
}