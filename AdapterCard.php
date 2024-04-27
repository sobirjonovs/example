<?php

namespace App\Adapters\Payment\Card;

use App\ActionData\Invoice\InvoicePaidActionData;
use App\Adapters\Payment\AbstractPaymentAdapter;
use App\Exceptions\PaymentFailedException;
use App\Http\Requests\Adapters\Cash\ConfirmPaymentRequest;
use App\Models\ClientContract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\InvoiceService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Http\FormRequest;
use Psr\SimpleCache\InvalidArgumentException;

class AdapterCard extends AbstractPaymentAdapter
{
    private InvoiceService $invoice_service;

    /**
     * @param InvoiceService $invoice_service
     */
    public function __construct(InvoiceService $invoice_service)
    {
        $this->invoice_service = $invoice_service;
    }

    /**
     * @return string
     */
    public static function getPaymentProvider(): string
    {
        return Transaction::CARD;
    }

    /**
     * @return FormRequest
     */
    public function validateConfirmPayment(): FormRequest
    {
        return $this->validate(ConfirmPaymentRequest::class);
    }

    /**
     * @throws GuzzleException
     * @throws PaymentFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \Throwable
     */
    public function confirm(): array
    {
        $request = $this->validateConfirmPayment();

        $order = Invoice::query()->findOrFail($request->get('order_id'));

        $invoice = $this->invoice_service->paid(InvoicePaidActionData::createFromArray([
            'type' => Payment::CARD,
            'invoice_id' => $order->id,
            'currency' => Payment::UZS,
            'amount' => $order->amount,
            'detail' => $request->get('detail')
        ]));

        /**
         * @var ClientContract $order
         */
        $order = $order->invoiceable;
        $cp = $order->contractPolicy;

        return [
            'amount' => $order->amount,
            'risks_sum' => $order->risks_sum,
            'path' => $invoice->path,
            'file' => $invoice->file,
            'series' => $cp->series,
            'number' => $cp->number,
            'success' => true,
        ];
    }
}
