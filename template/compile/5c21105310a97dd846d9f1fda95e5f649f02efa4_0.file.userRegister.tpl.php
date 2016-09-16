<?php
    /* Smarty version 3.1.30, created on 2016-09-15 22:12:04
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/userRegister.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57db0094e02fb4_64344406',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '5c21105310a97dd846d9f1fda95e5f649f02efa4' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/userRegister.tpl',
                                            1 => 1473965106,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57db0094e02fb4_64344406 ( Smarty_Internal_Template $_smarty_tpl ) {
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
            <label>Uživatelské heslo pro kontrolu
                <input name="user_password_check" type="password" value="" placeholder="Uživatelské heslo pro kontrolu">
            </label>
            <input type="submit" name="submit" value="Registrovat">

            </form><?php }
    }
