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
}
