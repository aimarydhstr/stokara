<?php
require dirname(__DIR__).'/vendor/autoload.php';
use Symfony\Component\Process\Process;
$root = dirname(__DIR__);
$count = 0;
$errors = [];
foreach (['app', 'routes', 'database', 'scripts', 'tests', 'lang', 'storage/framework/views'] as $directory) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->getExtension() !== 'php' || str_ends_with($file->getFilename(), '.blade.php')) continue;
        $process = new Process([PHP_BINARY, '-l', $file->getPathname()]);
        $process->run();
        $count++;
        if (! $process->isSuccessful()) $errors[] = $process->getOutput().$process->getErrorOutput();
    }
}
echo json_encode(['files_checked' => $count, 'errors' => $errors], JSON_PRETTY_PRINT).PHP_EOL;
exit($errors ? 1 : 0);