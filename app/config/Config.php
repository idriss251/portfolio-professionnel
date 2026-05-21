<?php
/**
 * Configuration - Gère les variables d'environnement
 */

class Config
{
    private static $config = [];
    private static $loaded = false;

    public static function load()
    {
        if (self::$loaded) return;

        $envFile = dirname(__FILE__) . '/.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos($line, '#') === 0) continue;
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes
                if (in_array($value[0] ?? null, ['"', "'"])) {
                    $value = substr($value, 1, -1);
                }
                
                self::$config[$key] = $value;
            }
        }
        
        self::$loaded = true;
    }

    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$config[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
}

Config::load();
