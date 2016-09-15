<?php
    /* Smarty version 3.1.30, created on 2016-09-14 13:55:16
	  from "C:\xampp\htdocs\rate\template\userRegister.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57d93aa4d587d3_89244166',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            'bc879605c478bd26e8db15db90fb7e59b7f29ef8' =>
                                    [
                                            0 => 'C:\\xampp\\htdocs\\rate\\template\\userRegister.tpl',
                                            1 => 1473854110,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57d93aa4d587d3_89244166 ( Smarty_Internal_Template $_smarty_tpl ) {
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
