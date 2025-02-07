<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MessageService
{
    public function showMessage($msg)
    {
        $message = $this->getMessage($msg);

        // Flash the message to the session for view rendering
        Session::flash('message', $message);

        // You can also return it if you want to use it elsewhere
        // return view('component.notification',$message); 
    }

    private function getMessage($msg)
    {
        $messages = [
            1 => ['type' => 'success', 'msg' => 'موفقانه ثبت گردید'],
            2 => ['type' => 'warning', 'msg' => 'ثبت نگردید'],
            3 => ['type' => 'warning', 'msg' => 'موفقانه حذف گردید'],
            4 => ['type' => 'danger', 'msg' => 'حذف نگردید'],
            5 => ['type' => 'success', 'msg' => 'موفقانه ویرایش گردید'],
            6 => ['type' => 'danger', 'msg' => 'ویرایش نگردید'],
            7 => ['type' => 'warning', 'msg' => 'دیتا تکراری میباشد'],
            8 => ['type' => 'warning', 'msg' => 'فعلا صلاحیت حذف را ندارید'],
            9 => ['type' => 'warning', 'msg' => 'این کاربر کدام رول ندارد ویا غیر فعال میباشد'],
            10 => ['type' => 'success', 'msg' => 'دیتابیس موفقانه ریستور گردید'],
            11 => ['type' => 'success', 'msg' => 'موفقانه نسخه پشتبان ایجاد گردید'],
            12 => ['type' => 'warning', 'msg' => 'نسخه پشتبان ایجاد نگردید'],
            13 => ['type' => 'warning', 'msg' => 'دیتابیس ریستور نگردید']
        ];

        return $messages[$msg] ?? ['type' => 'info', 'msg' => 'پیام نامشخص'];
        
    }
}
