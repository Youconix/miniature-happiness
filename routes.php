<?php
use youconix\core\Routes;

// Add here your routes
Routes::get('/profile/{id}','User','view')->where('id','[0-9]+');
?>