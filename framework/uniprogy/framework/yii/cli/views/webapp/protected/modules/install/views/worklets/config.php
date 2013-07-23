<h3>Permissions and Configuration</h3>
<p>1. Make sure directories and files listed below are given the right permissions:
<table>
<?php foreach($this->permissions as $item=>$p)
{
	?><tr><td><?php echo $item; ?></td><td><?php echo $p; ?></td></tr><?php
} ?>
</table>
</p>
<p>2. Edit this file: <?php echo app()->basePath .DS. 'config' .DS. 'public' .DS. 'instance.php'; ?></p>
<p>Example: your database name is "myDatabase", username is "myUsername", password is "myPassword".
DB Tables should get "myPrefix_" prefix and you want your application to be called "My Application".
Configuration would be similar to this:</p>
<div class='box'>
<?php 
$str = <<<EOF
<?php
 return array (
  // the name of your site
  'name' => '[s]My Application[/s]',
  'components' => 
  array (
    'db' => 
    array (
      // mysql connection string: change host and database name here (dbname)
      'connectionString' => 'mysql:host=localhost;dbname=[s]myDatabase[/s]',
      // DB username
      'username' => '[s]myUsername[/s]',
      // DB password
      'password' => '[s]myPassword[/s]',
      // DB tales prefix
      'tablePrefix' => '[s]myPrefix_[/s]',
    ),
    // special URL butify settings
    'urlManager' => 
    array (
      'urlFormat' => 'path',
      'showScriptName' => false,
      'rules' => 
      array (
        'page/<view>' => 'base/page',
      ),
    ),
  ),
);
EOF;
$str = highlight_string($str,true);
echo strtr($str,array('[s]'=>'<strong>','[/s]'=>'</strong>'));
?>
</div>
<p>Click the button below when you're done.</p>