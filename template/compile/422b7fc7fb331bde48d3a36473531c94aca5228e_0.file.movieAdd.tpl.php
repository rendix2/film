<?php
    /* Smarty version 3.1.30, created on 2016-09-16 11:02:38
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/movieAdd.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57dbb52eb078d6_52426852',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '422b7fc7fb331bde48d3a36473531c94aca5228e' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/movieAdd.tpl',
                                            1 => 1473965105,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57dbb52eb078d6_52426852 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <form method="post" action="">
            <input type="text" name="csfdLink" value="<?php echo $_smarty_tpl->tpl_vars[ 'csfdLink' ]->value; ?>
" placeholder="http://www.csfd.cz/film/13-akta-x/prehled/" maxlength="200" size="100">
    <input type="submit" name="submit" value="Přidat film">
            </form><?php }
    }
