AppSolo-php
===========

A Tiny Single Page PHP Framework with Template Support.

In many cases, we need develope small web applications, for which a single page php file is enough.

Although it's lite, we still want some features from other best pratices:

  * A **Router** to map requset to function
  * A **View** can be separated from model/controller logic
  * A **Template** to start with

"AppSolo-php" is wrote for this purpose. It include features like:

  * A **Router** to map request
  * A **Tempalte** implementaion with pure PHP, allow `extend` and `block`
  * A **Demo** to start with
  * Allow generate output inline
  * Allow use from command line (`cli` mode)
  * Use `PATH_INFO` for nice looking URL



Quick Demo
-----------


```php
<?php

$schema = array(
  'route'=>array(
    array('GET', '^/view/(\d+)', 'view_article'),
    array('GET', '^/list',       'view_list'),
    array('GET', '^/',           'view_default')
  )
);

require('../core/AppSolo.php');

$app = AppSolo::make(array(
  'view.dir' => 'view'
));

$app->dispatch($schema);

exit();

//====================================================================//
// User Defined Actions Below                                         //
//====================================================================//

function view_article($id){

  AppView::assign('id',     $id);
  AppView::assign('title',  'AppSolo is Tiny');
  AppView::assign('author', 'noyesno.net');
  AppView::assign('body',   '<strong>Tidy is Good!</strong>');

  AppView::view('article.tpl.php');
}

function view_list(){
  echo "This is Article List";
}

function view_default(){
  echo "This is Default Page";
}
```


Tempalte Usage
---------------

A page template which extend a parent template:
```php
<? AppView::extend('parent.tpl.php'); ?>

This Content Will NOT be Output.

<? AppView::block('main') ?>
<h1><?= $title?></h1>
<p><?= $author?></p>
<div>
  <?= $body?>
</div>
<? AppView::block('/main') ?>
```

Parent template file:

```php
<!DOCTYPE html>
<html>
<head>
<meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible'>
<base href="http://pv/css/ui/"/>
<title><?= $title?></title>
<? AppView::block('head') ?>
<style>
#footer {
  color:gray;
  margin:1.5em 0;
  padding:1em;
}
</style>
<? AppView::block('/head') ?>
</head>
<body>

<div id="main">
<? AppView::block('main') ?>
Put Main Content Here...
<? AppView::block('/main') ?>
</div>

<div id="footer">
AppSolo-php is Tiny.
</div>
</body>
</html>
```
