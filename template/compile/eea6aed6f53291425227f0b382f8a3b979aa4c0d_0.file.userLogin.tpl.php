<?php
    /* Smarty version 3.1.30, created on 2016-09-16 11:02:48
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/userLogin.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57dbb538480a97_92296856',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            'eea6aed6f53291425227f0b382f8a3b979aa4c0d' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/userLogin.tpl',
                                            1 => 1473965106,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57dbb538480a97_92296856 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <form method="post" action="">
            <label>Uživatelské jméno
                <input name="user_name" type="text" maxlength="100"
                       value="<?php echo $_smarty_tpl->tpl_vars[ 'user_name' ]->value; ?>
" placeholder="Uživatelské jméno">
    </label>
    <label>Uživatelské heslo
        <input name="user_password" type="password" value="" placeholder="Uživatelské heslo">
    </label>
            <input type="submit" name="submit" value="Přihlásit">
            </form><?php }
    }
