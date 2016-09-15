<?php
    /* Smarty version 3.1.30, created on 2016-09-14 13:48:26
	  from "C:\xampp\htdocs\rate\template\userLogin.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57d9390ac36a45_16206086',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '4dc87ba6b206ac8b33afc0c8ded4e131347aaa53' =>
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
        function content_57d9390ac36a45_16206086 ( Smarty_Internal_Template $_smarty_tpl ) {
            $_smarty_tpl->compiled->nocache_hash = '1969857d9390a5e3364_51884542';
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
