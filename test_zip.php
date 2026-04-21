<?php phpinfo();

echo 'Zip extension loaded: ' . (extension_loaded('zip') ? 'YES' : 'NO') . '<br>';
echo 'ZipArchive class exists: ' . (class_exists('ZipArchive') ? 'YES' : 'NO');
phpinfo(INFO_MODULES);