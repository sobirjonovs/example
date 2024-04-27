<?php

namespace App\Adapters\Payment;

use App\Contracts\Payment\AdapterFabricPaymentContract;
use App\Contracts\Payment\AdapterPaymentContract;
use App\Exceptions\PaymentFailedException;
use App\Models\DictionaryItem;

class AdapterFabricPayment implements AdapterFabricPaymentContract
{
    private array $adapters = [];

    /**
     * @param array $services
     * @return $this
     */
    public function setAdapters(array $services): AdapterFabricPayment
    {
        $this->adapters = $services;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * @param int $provider
     * @return AdapterPaymentContract
     * @throws PaymentFailedException
     */
    public function create(int $provider): AdapterPaymentContract
    {
        foreach ($this->getAdapters() as $adapter) {
            /**
             * @var string $provider
             */
            $payment_provider = DictionaryItem::query()->findOrFail($provider)->name;

            /**
             * @var AdapterPaymentContract $adapter
             */
            if ($payment_provider === $adapter::getPaymentProvider()) {
                /**
                 * @var string $adapter
                 */
                return resolve($adapter)->setData(request()->toArray());
            }
        }

        throw new PaymentFailedException;
    }
}
