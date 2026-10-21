<?php

namespace App\Services\Payments;

use App\Models\Payment;

/**
 * Har payment gateway (sandbox, JazzCash, Easypaisa, ...) ek chhota adapter hai.
 *
 * checkout() batata hai ke patient ko payment karne ke liye kahan bheja jaye.
 * Asli gateway ka adapter yahan sirf URL banata hai; paisa mila ya nahi, ye
 * gateway ke signed callback se verify hona chahiye (signature check ke baghair
 * kabhi PaymentService::complete() na bulayen).
 */
interface PaymentGateway
{
    public function name(): string;

    /**
     * Payment page ka URL jahan patient ko bhejna hai.
     */
    public function checkoutUrl(Payment $payment): string;
}
