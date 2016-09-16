<?php
    /* Smarty version 3.1.30, created on 2016-09-16 10:58:35
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/movieSearch.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57dbb43be8ca10_84160923',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            'c322c96427615a3bff35b67e3ce6d35d79769458' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/movieSearch.tpl',
                                            1 => 1474016303,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57dbb43be8ca10_84160923 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <div id="main-panel">
                <div class="panel-text-wrap">
                    <h2>Zadejte název filmu, který se vám líbí</h2>
                </div>
                <div id="search-wrap">
                    <form method="post" action="?akce=search" id="myform">
                        <input type="search" name="search" id="search" class="search-live"
                               placeholder="Zadejte prosím název filmu" autocomplete="off"/>
                        <input id="search-button" name="test" type="submit" value="">
                        <div id="result-live"></div>
                        <p id="mysubmit">Zobrazit všechny výsledky</p>
                    </form>
                </div>
                <div class="panel-text-wrap">
                    <p id="search-info">Zadejte co nejpřesnější název filmu...</p>
                </div>
            </div>
            <div id="main-button">
                <a href="./?akce=register" class="main-button">RYCHLÁ REGISTRACE</a><a href="./?akce=login"
                                                                                       class="main-button">PŘIHLÁŠENÍ</a>
            </div><?php }
    }
