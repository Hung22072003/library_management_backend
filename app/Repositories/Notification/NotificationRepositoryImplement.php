<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class NotificationRepositoryImplement implements NotificationRepositoryInterface
{
    public function getAll($size = 6, $q = '') {
        return Notification::where('user_id', 'like', '%' . $q . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($size);
    }
    public function getById($id)
    {
        return Notification::find($id);
    }
    public function create(array $data)
    {
        return Notification::create($data);
    }
    public function update($id, array $data) {}
    public function delete($id) {}

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->update([Notification::IS_READ => true]);
            return true;
        }
        return false;
    }

    public function getNotificationsOfUser($user_id, $size = 6)
    {
        return Notification::where('user_id', '=', $user_id)
            ->orderBy('created_at', 'desc')
            ->paginate($size);
    }
}
