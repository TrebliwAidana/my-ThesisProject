<?php

$directory = 'resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

$replacements = [
    // Button background: from primary-600 to gold-500
    '/\b(bg)-primary-600\b/' => 'bg-gold-500',
    // Button hover: from primary-700 to gold-400 (lighter)
    '/\b(hover:bg)-primary-700\b/' => 'hover:bg-gold-400',
    // Focus ring: from primary-500 to gold-500
    '/\b(focus:ring)-primary-500\b/' => 'focus:ring-gold-500',
    // Any other primary background used in buttons (e.g., primary-500, primary-400)
    '/\b(bg)-primary-500\b/' => 'bg-gold-500',
    '/\b(bg)-primary-400\b/' => 'bg-gold-400',
    // Gradient backgrounds for headers (e.g., from-primary-600 to-primary-700)
    '/\b(from)-primary-600\b/' => 'from-gold-500',
    '/\b(to)-primary-700\b/' => 'to-gold-600',
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