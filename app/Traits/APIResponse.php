<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HTTPStatus;

trait APIResponse
{
    /**
     * @param mixed $messages
     */
    public function responseSuccess($message = 'Success !', int $status = HTTPStatus::HTTP_OK): JsonResponse
    {
        return Response::json([
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * @param mixed $messages
     * @param mixed $data
     */
    public function responseSuccessWithData($data, bool $isCreate = false,  $message = 'Success !', int $status = HTTPStatus::HTTP_OK): JsonResponse
    {
        if ($isCreate) {
            $status = HTTPStatus::HTTP_CREATED;
        }

        return Response::json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * @param mixed $messages
     */
    public function responseError($message = 'Failed !', int $status = HTTPStatus::HTTP_BAD_REQUEST): JsonResponse
    {

        return Response::json([
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * @param string $messages
     * @param mixed $errors
     */
    public function responseErrorWithData($errors, $message = 'Failed !', int $status = HTTPStatus::HTTP_BAD_REQUEST): JsonResponse
    {
        return Response::json([
            'errors' => $errors,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    public function responseErrorValidate($errors, $validator, int $status = HTTPStatus::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'messages' => $errors,
            'status' => $status,
        ], 422));
    }
}
