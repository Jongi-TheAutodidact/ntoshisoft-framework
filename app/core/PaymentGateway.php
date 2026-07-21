<?php

declare(strict_types=1);

class PaymentGateway {
    public function processPayfast(string $transaction_id, string|int|float $amount): never {
        $payfastParams = [
            'merchant_id' => PF_MERCHANT_ID,
            'merchant_key' => PF_MERCHANT_KEY,
            'amount' => $amount,
            'return_url' => ROOT.'/admin/pos/success/'.$transaction_id,
            'cancel_url' => ROOT.'/admin/pos/cancel/'.$transaction_id,
            'notify_url' => ROOT.'/api/payfast/notify'
        ];
        
        // Generate signature and redirect
        $signature = md5(http_build_query($payfastParams));
        $payfastParams['signature'] = $signature;
        
        redirect('https://www.payfast.co.za/eng/process?' . http_build_query($payfastParams));
    }
}