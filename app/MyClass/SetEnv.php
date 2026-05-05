<?php

namespace App\MyClass;

use Illuminate\Database\Eloquent\Model;

class SetEnv extends Model
{
    public static function setEmailToEnv() {
        $emailUsername = setting('email_username', '');
        $emailPassword = setting('email_password', '');
        $emailHost = setting('email_host', '');
        $emailPort = setting('email_port', '');

        self::updateEnv('MAIL_MAILER', 'smtp');
        self::updateEnv('MAIL_ENCRYPTION', 'ssl');
        self::updateEnv('MAIL_FROM_ADDRESS', $emailUsername);
        self::updateEnv('MAIL_USERNAME', $emailUsername);
        self::updateEnv('MAIL_PASSWORD', $emailPassword);
        self::updateEnv('MAIL_HOST', $emailHost);
        self::updateEnv('MAIL_PORT', $emailPort);

        // Menggunakan exec() untuk menjalankan perintah artisan
        exec('php artisan config:cache');

        return true;
    }

    public static function updateEnv($key, $value) {
        $path = base_path('.env');

        if (file_exists($path)) {
            // Membaca isi file .env
            $env = file_get_contents($path);

            // Mengganti nilai variabel
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            $env = preg_replace($pattern, $replacement, $env);

            // Menulis kembali ke file .env
            file_put_contents($path, $env);
        }
    }
}
