<div class="panel <?= ($debug_bar->active) ? 'with-debugbar' : '' ?> left-panel cf <?php echo (!$has_frames ? 'empty' : '') ?>">
  <?php $tpl->render($panel_left) ?>
</div>