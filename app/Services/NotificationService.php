<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Illuminate\Support\Str;

class NotificationService
{
    private $notificationRepository;
    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function createNotification($data)
    {
        switch ($data['type']) {
            case 'overdue_loan':
                $data['message'] = 'You have a loan batch coming due soon. Please return the book or extend the loan period.';
                break;
            case 'cancel_loan':
                $data['message'] = 'Your batch loan has been canceled.';
                break;
            case 'borrow_loan':
                $data['message'] = 'A user has just signed up to borrow a book.';
                $data['user_id'] = '1';
                break;
            case 'overdue_payment':
                $data['message'] = 'You have overdue payments.';
                break;
            case 'success_payment':
                $data['message'] = 'A user has just payment successfully.';
                $data['user_id'] = '1';
                break;
        }

        $data['id'] = Str::uuid();
        $notification = $this->notificationRepository->create($data);

        broadcast(new NotificationEvent($notification, $notification->user_id))->toOthers();
        return $notification;
    }

    public function getAllNotifications($size = 6, $q = '')
    {
        return $this->notificationRepository->getAll($size, $q);
    }
    public function getById($id)
    {
        return $this->notificationRepository->getById($id);
    }
    public function markAsRead($id)
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function getNotificationsOfUser($user_id, $size)
    {
        return $this->notificationRepository->getNotificationsOfUser($user_id, $size);
    }
}
