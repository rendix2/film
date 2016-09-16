<?php
    /* Smarty version 3.1.30, created on 2016-09-16 14:54:26
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/moviesView.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57dbeb82b471d1_05741081',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '4a712e945d4fecaba595b5383e222afaf0ef814e' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/moviesView.tpl',
                                            1 => 1474030463,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57dbeb82b471d1_05741081 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <div id="results" class="shadow">
            <div id="results-search-panel">
                <div id="search-module" class="shadow">
                    <form method="post" action="?akce=search" id="myform">
                        <input type="search" name="search" id="search" placeholder="Zadejte prosím název filmu"
                               autocomplete="off"/><input id="search-button" name="test" type="submit" value="">
                    </form>
                </div>
            </div>
            <div id="results-panel">
                <?php if ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) == 1 ) { ?>
                    <span class="bold">Byla nalezena 1 položka.</span>
                <?php } elseif ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) > 1 && count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) < 5 ) { ?>
                    <span class="bold">Byly nalezeny <?php echo count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ); ?>
                        položky.</span>
                <?php } elseif ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) >= 5 ) { ?>
                    <span class="bold">Bylo nalezeno <?php echo count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ); ?>
                        položek.</span>
                <?php } ?>
                <div class="panel-wrap"><span class="bold">Řazení</span>
                    <form id="sort">
                        <input type="radio" name="sort" id="m-name" value="m-name" checked><label
                                for="m-name">Název</label>
                        <input type="radio" name="sort" id="m-year" value="m-year"><label for="m-year">Rok</label>
                    </form>
                </div>
            </div>
            <div class="space-killer">
                <?php
                    $_from = $_smarty_tpl->smarty->ext->_foreach->init ( $_smarty_tpl, $_smarty_tpl->tpl_vars[ 'data' ]->value, 'movie' );
                    if ( $_from !== NULL ) {
                        foreach ( $_from as $_smarty_tpl->tpl_vars[ 'movie' ]->value ) {
                            ?>
                            <div class="result-marginer">
                                <div class="result-item">
                                    <div class="result-image"
                                         style="background-image: url('./images/<?php echo $_smarty_tpl->tpl_vars[ 'movie' ]->value[ 'movie_picture' ]; ?>
                                                 .jpg')"></div>
                                    <div class="result-info">
                                        <h3><?php echo $_smarty_tpl->tpl_vars[ 'movie' ]->value[ 'movie_name_czech' ]; ?>
                                        </h3>
                                        <p><?php echo $_smarty_tpl->tpl_vars[ 'movie' ]->value[ 'movie_year' ]; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }
                    } else {
                        ?>

                        Bohužel nic jsem nenašel :(((
                        <?php
                    }
                    $_smarty_tpl->smarty->ext->_foreach->restore ( $_smarty_tpl );
                ?>

            </div>
            </div><?php }
    }
