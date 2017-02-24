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
