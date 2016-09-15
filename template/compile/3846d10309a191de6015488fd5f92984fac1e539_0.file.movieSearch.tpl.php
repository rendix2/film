<?php
    /* Smarty version 3.1.30, created on 2016-09-15 13:07:10
	  from "C:\xampp\htdocs\rate\template\movieSearch.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57da80de829b30_04736934',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '3846d10309a191de6015488fd5f92984fac1e539' =>
                                    [
                                            0 => 'C:\\xampp\\htdocs\\rate\\template\\movieSearch.tpl',
                                            1 => 1473873728,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57da80de829b30_04736934 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>


            <form method="post" action="">

            <input type="text" name="search" value="<?php echo $_smarty_tpl->tpl_vars[ 'search' ]->value; ?>
">

            <input type="submit" name="submit" value="Hledat">
            </form><?php }
    }
