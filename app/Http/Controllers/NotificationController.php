<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationStoreRequest;
use App\Services\NotificationService;
use App\Traits\APIResponse;
use Illuminate\Http\Request;

class NotificationController extends ControllerWithGuard
{
    private $notificationService;
    use APIResponse;
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $size = request()->query('size', 6);
        // $user = auth()->user();
        // if($user->role === 'admin') {
        //     $notifications = $this->notificationService->getAllNotifications($size, $q);
        //     return $this->responseSuccessWithData($notifications);
        // }
        $notifications = $this->notificationService->getNotificationsOfUser(auth()->id(), $size);
        return $this->responseSuccessWithData($notifications);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationStoreRequest $request)
    {
        $data = $request->only([
            'type',
            'batch_id',
            'transaction_id',
        ]);
        $notfication = $this->notificationService->createNotification($data);
        if(!$notfication) {
            return $this->responseError('Notification not created', 500);
        }
        return $this->responseSuccessWithData($notfication, true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $result = $this->notificationService->markAsRead($id);
        if(!$result) {
            return $this->responseError('Update notification unsuccessfully', 404);
        }
        return $this->responseSuccess('Update notification successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getNotificationsOfUser()
    {
        $size = request()->query('size', 6);
        $notifications = $this->notificationService->getNotificationsOfUser(auth()->id, $size);
        return $this->responseSuccessWithData($notifications);
    }
}
