<?php
    /* Smarty version 3.1.30, created on 2016-09-14 12:09:29
	  from "C:\xampp\htdocs\rate\template\userLogin.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57d921d99a81d9_35346756',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '03c9e46b713ab6bfbb3a697bddded0695e3f61fa' =>
                                    [
                                            0 => 'C:\\xampp\\htdocs\\rate\\template\\userLogin.tpl',
                                            1 => 1473847766,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57d921d99a81d9_35346756 ( Smarty_Internal_Template $_smarty_tpl ) {
            $_smarty_tpl->compiled->nocache_hash = '2279957d921d93363c9_03209691';
            ?>

            <form method="post" action="">

            <label>Uživatelské jméno
                <input name="user_name" type="text" maxlength="100"
                       value="<?php echo $_smarty_tpl->tpl_vars[ 'user_name' ]->value; ?>
">
            </label>
            <label>Uživatelské heslo
                <input name="user_password" type="password" value="">
            </label>

            <input type="submit" name="submit" value="Přihlásit">
            </form><?php }
    }
