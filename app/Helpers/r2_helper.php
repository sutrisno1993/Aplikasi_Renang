<?php

use App\Libraries\R2Client;

if (!function_exists('r2_url')) {
    /**
     * Get the Cloudflare R2 URL for a given key
     * 
     * @param string $key
     * @param string $defaultFolder Folder default jika key tidak memiliki prefix folder
     * @return string
     */
    function r2_url($key, $defaultFolder = '')
    {
        if (empty($key)) return '';

        // Jika ini adalah URL lengkap, langsung kembalikan
        if (filter_var($key, FILTER_VALIDATE_URL)) {
            return $key;
        }

        // Jika key tidak mengandung slash (/) dan ada default folder, tambahkan folder
        if (strpos($key, '/') === false && !empty($defaultFolder)) {
            $key = rtrim($defaultFolder, '/') . '/' . $key;
        }

        $r2 = new R2Client();
        return $r2->getUrl($key);
    }
}

if (!function_exists('get_initials')) {
    /**
     * Get initials from a name (first letter of first and second word)
     * 
     * @param string $name
     * @return string
     */
    function get_initials($name)
    {
        if (empty($name)) return '?';
        
        $words = explode(' ', trim($name));
        $initials = strtoupper(substr($words[0], 0, 1));
        
        if (count($words) > 1) {
            $initials .= strtoupper(substr($words[1], 0, 1));
        }
        
        return $initials;
    }
}
