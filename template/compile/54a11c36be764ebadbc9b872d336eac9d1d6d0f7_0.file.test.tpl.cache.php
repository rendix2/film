<?php
    /* Smarty version 3.1.30, created on 2016-09-14 11:50:24
	  from "C:\xampp\htdocs\rate\template\test.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57d91d60135547_16586951',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '54a11c36be764ebadbc9b872d336eac9d1d6d0f7' =>
                                    [
                                            0 => 'C:\\xampp\\htdocs\\rate\\template\\test.tpl',
                                            1 => 1473846282,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57d91d60135547_16586951 ( Smarty_Internal_Template $_smarty_tpl ) {
            $_smarty_tpl->compiled->nocache_hash = '3137757d91d5f7ff2e2_68267836';
            ?>


            <?php
            $_from = $_smarty_tpl->smarty->ext->_foreach->init ( $_smarty_tpl, $_smarty_tpl->tpl_vars[ 'data' ]->value, 'item' );
            if ( $_from !== NULL ) {
                foreach ( $_from as $_smarty_tpl->tpl_vars[ 'item' ]->value ) {
                    ?>
                    <?php echo $_smarty_tpl->tpl_vars[ 'item' ]->value[ 'user_id' ]; ?>
                    - <?php echo $_smarty_tpl->tpl_vars[ 'item' ]->value[ 'user_name' ]; ?>
                    <br>
                    <?php
                }
            }
            $_smarty_tpl->smarty->ext->_foreach->restore ( $_smarty_tpl );
        }
    }
