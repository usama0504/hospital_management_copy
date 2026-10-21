<?php

namespace App\Services\Payments;

use App\Models\Payment;

/**
 * Test-mode gateway: portal ke andar ek nakli checkout page jahan "Pay" ya
 * "Fail" dabate hain. Sirf development/testing ke liye.
 */
class SandboxGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'sandbox';
    }

    public function checkoutUrl(Payment $payment): string
    {
        return route('portal.payments.sandbox', $payment);
    }
}
