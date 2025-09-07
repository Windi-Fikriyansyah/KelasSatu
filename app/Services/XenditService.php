<?php
// app/Services/XenditService.php
namespace App\Services;

use Xendit\Xendit;

class XenditService
{
    public function __construct()
    {
        Xendit::setApiKey(env('XENDIT_API_KEY'));
    }

    public function createInvoice($externalId, $amount, $payerEmail, $description = '')
    {
        $params = [
            'external_id' => $externalId,
            'amount' => $amount,
            'payer_email' => $payerEmail,
            'description' => $description,
        ];

        return \Xendit\Invoice::create($params);
    }

    public function createVirtualAccount($externalId, $bankCode, $name)
    {
        $params = [
            'external_id' => $externalId,
            'bank_code' => $bankCode,
            'name' => $name,
        ];

        return \Xendit\VirtualAccounts::create($params);
    }
}
