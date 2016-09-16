<?php
/* Smarty version 3.1.30, created on 2016-09-16 11:44:59
  from "/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/liveMoviesView.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, [
  'version' => '3.1.30',
  'unifunc' => 'content_57dbbf1b6c9d17_96872870',
  'has_nocache_code' => FALSE,
  'file_dependency' => 
  [
    'b0acd6cbbf46d99b9ecdda016d9db89e7bfb4d12' => 
    [
      0 => '/home/users/madammelulucz/madamme-lulu.cz/sub/film/template/liveMoviesView.tpl',
      1 => 1474019076,
      2 => 'file',
    ],
  ],
  'includes' => 
  [
  ],
],FALSE)) {
function content_57dbbf1b6c9d17_96872870 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value, 'movie');
if ($_from !== NULL) {
foreach ($_from as $_smarty_tpl->tpl_vars['movie']->value) {
?>

<div class="result-item-live" tabindex="0">
<div class="result-image-live" style="background-image: url('./images/<?php echo $_smarty_tpl->tpl_vars['movie']->value['movie_picture'];?>
.jpg')"></div>
<div class="result-info-live"><h3><?php echo $_smarty_tpl->tpl_vars['movie']->value['movie_name_czech'];?>
</h3>
<p><?php echo $_smarty_tpl->tpl_vars['movie']->value['movie_year'];?>
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
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

<?php }
}
