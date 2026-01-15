<?php

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Generate a temporary password
     * 
     * @param int $length
     * @return string
     */
    public static function generateTemporaryPassword($length = 8)
    {
        // Generate a random password with letters and numbers
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
}

