<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepositoryInterface;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function markAsRead($id);
    public function getNotificationsOfUser($user_id, $size = 6);
}
