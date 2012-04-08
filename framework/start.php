<?php

// Required includes
require_once(HELPERS . 'magic.php');
require_once(HELPERS . 'utility.php');
require_once(HELPERS . 'settings.php');

// Make sure we have a valid site
if (is_dir(DOC_ROOT . BASE_DIR . SITE))
{
    // Load the framework
    $f = singleton('framework');

    // Start the framework
    $f->run();
}

else Error::fatalError('Site "' . SITE . '" does not exist.');