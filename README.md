AppSolo-php
===========

A Tiny Single Page PHP Framework with Template Support

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
