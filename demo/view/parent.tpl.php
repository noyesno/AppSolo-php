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
