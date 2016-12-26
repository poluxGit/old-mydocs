<?php

/**
 * Add File into Vault Form
 *
 * @uses API
 *
 * @author polux <polux@poluxfr.org>
 */
require __DIR__.'/../vendor/autoload.php';

use MyGED\Vault as Vault;
use MyGED\Application\Application;
use MyGED\Business\Document;

// Application init!
Application::initApplication();

echo <<<END

  <form action="../api/v1/file/" method="POST" enctype="multipart/form-data">
    <input type="file" name='file'/>
    <input type="submit" value='Upload'/>
  </form>
END;
exit;
