<? AppView::extend('parent.tpl.php'); ?>

This Content Will NOT be Output.

<? AppView::block('main') ?>
<h1><?= $title?></h1>
<p><?= $author?></p>
<div>
  <?= $body?>
</div>
<? AppView::block('/main') ?>

