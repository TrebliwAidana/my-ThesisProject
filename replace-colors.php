<?php

$directory = 'resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

$replacements = [
    // Replace indigo Tailwind classes with primary (emerald)
    '/\b(bg|text|border|ring|focus:ring|hover:bg|hover:border|from|to|divide)-indigo-(\d+)\b/' => '$1-primary-$2',
    // Replace purple Tailwind classes with gold
    '/\b(bg|text|border|from|to)-purple-(\d+)\b/' => '$1-gold-$2',
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $original = $content;
        foreach ($replacements as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        if ($content !== $original) {
            file_put_contents($file->getRealPath(), $content);
            echo "Updated: " . $file->getRealPath() . "\n";
        }
    }
}

echo "Done. Run 'npm run build' and 'php artisan view:clear'.\n";