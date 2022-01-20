<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $data = Auth::user()->notifications()->simplePaginate();
        $data->transform(function ($item) {
            return $this->localize($item);
        });
        return $data;
    }

    public function unread()
    {
        $data = Auth::user()->unreadNotifications()->simplePaginate();
        $data->transform(function ($item) {
            return $this->localize($item);
        });
        return $data;
    }

    public function read($id)
    {
        return Auth::user()->unreadNotifications()->findOrFail($id)->markAsRead();
    }

    public function readAll()
    {
        return Auth::user()->unreadNotifications->markAsRead();
    }

    private function localize($item)
    {
        $message = $item->data;
        $localizedMessage = [];
        $localizedMessage['id'] = $item->id;
        $localizedMessage['order_id'] = @$message['order_id'];
        $localizedMessage['order_number'] = @$message['order_number'];
        $localizedMessage['title'] = $message['data']['title_' . app()->getLocale()];
        $localizedMessage['body'] = $message['data']['body_' . app()->getLocale()];
        $localizedMessage['alert_type'] = $message['data']['alert_type'];
        $localizedMessage['read_at'] = $item->read_at;
        return $localizedMessage;
    }
}
