<?php

// This is what is used to define your application environment. It makes it easier to 
// have conditional code based on environment
$config['app']['env'] = 'development';

// This is the key that appears in your url: index.php?uri= in this case
$config['route']['path'] = 'uri';

// Accepted Http Verbs. Add each one to teh array, but make sure that they are 
// capitalized
$config['http']['verbs'] = array('GET','POST','PUT','DELETE','HEAD');