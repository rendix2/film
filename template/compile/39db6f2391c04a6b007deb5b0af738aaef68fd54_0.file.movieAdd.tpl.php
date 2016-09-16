<?php
    /* Smarty version 3.1.30, created on 2016-09-15 11:03:52
	  from "C:\xampp\htdocs\rate\template\movieAdd.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57da63f81b5de5_85491138',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '39db6f2391c04a6b007deb5b0af738aaef68fd54' =>
                                    [
                                            0 => 'C:\\xampp\\htdocs\\rate\\template\\movieAdd.tpl',
                                            1 => 1473926003,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57da63f81b5de5_85491138 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <form method="post" action="">
            <input type="text" name="csfdLink" value="<?php echo $_smarty_tpl->tpl_vars[ 'csfdLink' ]->value; ?>
" placeholder="http://www.csfd.cz/film/13-akta-x/prehled/" maxlength="200" size="100">
    <input type="submit" name="submit" value="Přidat film">
</form><?php }
}
