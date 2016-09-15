<?php
    /* Smarty version 3.1.30, created on 2016-09-15 17:35:25
	  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/moviesView.tpl" */

    /* @var Smarty_Internal_Template $_smarty_tpl */
    if ( $_smarty_tpl->_decodeProperties ( $_smarty_tpl, [
            'version'          => '3.1.30',
            'unifunc'          => 'content_57dabfbd2eb784_74275587',
            'has_nocache_code' => FALSE,
            'file_dependency'  =>
                    [
                            '4a712e945d4fecaba595b5383e222afaf0ef814e' =>
                                    [
                                            0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/moviesView.tpl',
                                            1 => 1473944967,
                                            2 => 'file',
                                    ],
                    ],
            'includes'         =>
                    [
                    ],
    ], FALSE )
    ) {
        function content_57dabfbd2eb784_74275587 ( Smarty_Internal_Template $_smarty_tpl ) {
            ?>
            <div id="results">
            <div id="results-panel">
                <?php if ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) == 1 ) { ?>
                    <h2>Byla nalezena 1 položka.</h2>
                <?php } elseif ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) > 1 && count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) < 5 ) { ?>
                    <h2>Byly nalezeny <?php echo count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ); ?>
                        položky.</h2>
                <?php } elseif ( count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ) >= 5 ) { ?>
                    <h2>Bylo nalezeno <?php echo count ( $_smarty_tpl->tpl_vars[ 'data' ]->value ); ?>
                        položek.</h2>
                <?php } ?>
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
                                         style="background-image: url('<?php echo $_smarty_tpl->tpl_vars[ 'movie' ]->value[ 'movie_picture' ]; ?>
                                                 ')"></div>
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
