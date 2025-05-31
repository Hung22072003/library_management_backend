<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class VnPayController extends Controller
{
    private $transactionService;
    private $notificationService;
    public function __construct(TransactionService $transactionService, NotificationService $notificationService)
    {
        $this->transactionService = $transactionService;
        $this->notificationService = $notificationService;
    }
    public function createPayment(Request $request)
    {
        $vnp_TmnCode = Config::get('services.vnpay.tmn_code');
        $vnp_HashSecret = Config::get('services.vnpay.hash_secret');
        $vnp_Url = Config::get('services.vnpay.url');
        $vnp_Returnurl = Config::get('services.vnpay.return_url');

        $vnp_TxnRef = $request->id; // Mã đơn hàng
        $vnp_OrderInfo = 'Thanh toán hóa đơn';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->amount * 100; // Số tiền thanh toán (nhân 100 để chuyển sang đơn vị VND)
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->bank_code ?? '';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        // echo $vnp_Url;
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = Config::get('services.vnpay.hash_secret');

        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        $id_transaction = $inputData['vnp_TxnRef'];
        echo $id_transaction;
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash && $request->vnp_ResponseCode == '00') {
            $this->transactionService->updateTransaction(
                $id_transaction,
                [
                    'payment_status' => 'success',
                    'payment_method' => 'vnpay'
                ]
            );

            $this->notificationService->createNotification([
                'type' => 'success_payment',
                'transaction_id' => $id_transaction,
            ]);
            return redirect(Config::get('services.vnpay.return_url_client') . '?status=success&id=' . $id_transaction);
        }
        $this->transactionService->updateTransaction(
            $id_transaction,
            [
                'payment_status' => 'failed',
                'payment_method' => 'vnpay'
            ]
        );
        return redirect(Config::get('services.vnpay.return_url_client') . '?status=fail' . '&id=' . $id_transaction);
    }
}
