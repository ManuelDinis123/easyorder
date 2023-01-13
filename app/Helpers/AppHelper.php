<?php

namespace App\Helpers;

class AppHelper
{

    /**
     *  Checks if the user is logged in
     * 
     * @return Boolean
     */
    public static function hasLogin()
    {
        return session()->get('authenticated');
    }

    /**
     *  Checks if the user is logged in an is a professional account
     * 
     * @return Boolean
     */
    public static function checkAuth()
    {
        return session()->get('authenticated') && session()->get('user')['isProfessional'];
    }

    /**
     * Checks if values in a given array are empty or not
     * 
     * @return Boolean
     */
    public static function hasEmpty($values)
    {
        foreach ($values as $val) {
            if (!$val) {
                return true;
            }
        }
        return false;
    }
}
