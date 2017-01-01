<?php

//require __DIR__.'/api/router.class.php';

require __DIR__.'/application/application.class.php';

require __DIR__.'/core/database/dbobject.aclass.php';
require __DIR__.'/core/database/database.sclass.php';


require __DIR__.'/exceptions/application.exception.class.php';
require __DIR__.'/exceptions/api.exception.class.php';

require __DIR__.'/exceptions/generic.exception.class.php';

require __DIR__.'/vault/vault.sclass.php';
require __DIR__.'/vault/vaultdb.sclass.php';
require __DIR__.'/vault/vaultfs.sclass.php';

require __DIR__.'/business/document.class.php';
require __DIR__.'/business/categorie.class.php';
require __DIR__.'/business/metadoc.class.php';
require __DIR__.'/business/metatypedoc.class.php';
require __DIR__.'/business/tier.class.php';
require __DIR__.'/business/typedoc.class.php';

require __DIR__.'/core/filesystem/filesystem.sclass.php';
require __DIR__.'/core/filesystem/pdfhandler.class.php';

require __DIR__.'/core/tasks/task.class.php';

require __DIR__.'/process/engines/import-files.class.php';
