<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    // Replace base_url('logo.png?v=' . time())
    $content = preg_replace("/base_url\('logo\.png\?v=' \. time\(\)\)/", "app_logo('sportcenter_logo', 'logo.png')", $content);
    
    // Replace base_url('logo.png')
    $content = preg_replace("/base_url\('logo\.png'\)/", "app_logo('sportcenter_logo', 'logo.png')", $content);

    // Replace base_url("logo.png")
    $content = preg_replace('/base_url\("logo\.png"\)/', "app_logo('sportcenter_logo', 'logo.png')", $content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
    }
}
echo "Done.\n";
