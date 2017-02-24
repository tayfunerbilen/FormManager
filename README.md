# FormManager
Manage your forms quickly and safely.

Usage
====================
Include class file started it and create your own form quickly.

```php
<?php

  // include class file
  require 'formmanager.class.php';
  
  $form = new FormManager();
  
  // create your own form quickly
  $form->start();
  $form->input('username', 'Enter username');
  $form->type('password')->input('password', 'Enter Password');
  $form->required(false)->textarea('about', 'Who are you?'); // not required
  $form->end(false); // means don't show
  
  // check before show
  if($data = $form->control()){
    print_r($data);
  } else {
    echo $form->error();
  }
  
  // you can show where you want :)
  $form->show();
```

Templates
====================
If you want, you can change templates for form elements one by one.

```php
$form->template('input', function(){
  return '<div class="input-example">
    <label for="{name}">
      {form}
      <span class="title">{label}</span>
    </label>
  </div>';
});

$form->template('select', function(){
  return '<div class="select-example">
    <label for="{name}">
      {form}
      <span class="title">{label}</span>
    </label>
  </div>';
});
```

Radio/Checkbox/Select Examples
====================
```php
$form->start('GET');
$form->label('Who are you?')->textarea('about', 'Write about yourself something');
$form->type('multiple')->value([1,2])->select('hobbies', [
  ''  => '-- select a hobbie',
  '1' => 'Cycling',
  '2' => 'Diving',
  '3' => 'Driving'
]);
 $form->label('Man')->checkbox('gender', 1);
 $form->label('Woman')->checkbox('gender', 2);
 $form->submit('Submit');
 $form->end(false);
 
 echo $form->show();
 
 if ($data = $form->control()){
  print_r($data);
 } else {
  echo $form->error();
 }
```
