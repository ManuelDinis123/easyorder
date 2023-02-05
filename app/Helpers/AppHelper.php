<?php

namespace App\Helpers;

use App\Models\Types;
use Illuminate\Support\Facades\Log;

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
        return session()->get('authenticated') && (session()->get('user')['isProfessional']&&session()->get('user')['active']);
    }

    /**
     * Gets a id of the type and checks if user has certain permissions
     * set all to true if function should return false if any of the permissions fail
     * set all to false if only need one to return true
     * 
     * @param id integer
     * @param permissions Array
     * @param all Boolean
     * @return Boolean
     */
    public static function checkUserType($id, $permissions, $all = true)
    {
        // Get the type from the id
        $type = Types::whereId($id)->get()->first();

        $hasPerm = 0;

        // If the permissions var isn't an array an only one permission the loop isn't necessary so only do the if
        if (is_array($permissions)) {
            foreach ($permissions as $perm) {
                if ($type[$perm] == 0) {
                    if ($all) return 0;
                    $hasPerm = 0;
                } else if (!$all) {
                    $hasPerm = 1;
                }
            }
        } else {
            if ($type[$permissions] == 0) {
                return 0;
            } else {
                return 1;
            }
        }

        return $hasPerm;
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
