<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ManagementHelper
{
    
    // public static function jsonResponse($data = null, $token = null, $state = 'error', $status_code = 500, $message = '')
    // {
    //     return response()->json([
    //         'data' => $data,
    //         'token' => $token,
    //         'state' => $state,
    //         'status_code' => $status_code,
    //         'message' => $message,
    //     ], $status_code);

    // }


    /**
     * This function is used to print and debug data
     */
    public static function pre($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }

    /**
     * This function is used to generate the hashed password
     * @param {string} $plainPassword : This is plain text password
     */
    public static function getHashedPassword($plainPassword)
    {
        return Hash::make($plainPassword);
    }

    /**
     * This function is used to verify a hashed password
     * @param {string} $plainPassword : This is plain text password
     * @param {string} $hashedPassword : This is hashed password
     */
    public static function verifyHashedPassword($plainPassword, $hashedPassword)
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

    /**
     * This function is used to get the current browser agent
     */
    public static function getBrowserAgent()
    {
        $agent = request()->header('User-Agent');

        if (strpos($agent, 'MSIE') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($agent, 'Trident') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($agent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($agent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($agent, 'Safari') !== false) {
            return 'Apple Safari';
        } elseif (strpos($agent, 'Opera') !== false) {
            return 'Opera';
        } else {
            return 'Unidentified User Agent';
        }
    }

    /**
     * This function is used to configure email settings
     */
    public static function setProtocol()
    {
        return [
            'driver' => env('MAIL_DRIVER', 'smtp'),
            'host' => env('MAIL_HOST', 'smtp.example.com'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'example@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ];
    }

    /**
     * This function is used to send reset password emails
     * @param {array} $detail : Details for the email
     */
    public static function resetPasswordEmail($detail)
    {
        $emailConfig = setProtocol();

        Mail::send('email.resetPassword', ['data' => $detail], function ($message) use ($detail, $emailConfig) {
            $message->from($emailConfig['from']['address'], $emailConfig['from']['name'])
                    ->to($detail['email'])
                    ->subject('Reset Password');
        });

        return count(Mail::failures()) === 0;
    }

    /**
     * This function is used to set flash data in session
     */
    public static function setFlashData($status, $flashMsg)
    {
        Session::flash($status, $flashMsg);
    }

    /**
     * This function is used to check access permissions
     */
    public static function doesHaveAccessTo($modules, $option)
    {
        $accessInfo = Session::get('accessInfo', []);
        $isAdmin = Session::get('isAdmin', 0);

        // if ($isAdmin || (array_key_exists($modules, $accessInfo) 
        //     && ($accessInfo[$modules][$option] == 1 || $accessInfo[$modules]['total_access'] == 1))) {
        //     return true;
        // }

        // return false;

         // Check if module and option exist in accessInfo
         if (isset($accessInfo[$modules])) {
            if (!empty($accessInfo[$modules][$option]) && $accessInfo[$modules][$option] == 1) {
                return true;
            }
            if (!empty($accessInfo[$modules]['total_access']) && $accessInfo[$modules]['total_access'] == 1) {
                return true;
            }
        }

        return false;
        
    }

    /**
     * This function is used to get the active package ID
     */
    public static function activePackageId()
    {
        $package = DB::table('packages')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->first();

        return $package ? $package->type : 0;
    }

}
