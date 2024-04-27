<?php

namespace App\Adapters\Payment;

use App\Contracts\Payment\AdapterPaymentContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

abstract class AbstractPaymentAdapter implements AdapterPaymentContract
{
    private array $data = [];

    /**
     * @param array $data
     * @return AbstractPaymentAdapter
     */
    public function setData(array $data = []): AbstractPaymentAdapter
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getData(): Collection
    {
        return collect($this->data);
    }

    /**
     * @param string|null $request
     * @return FormRequest
     */
    final protected function validate(string $request = null): FormRequest
    {
        /**
         * @var FormRequest $request
         */
        $request ??= FormRequest::class;

        $request = $request::createFrom(request()->merge($this->getData()->toArray()))
            ->setContainer(app())
            ->setRedirector(app(Redirector::class));

        $request->validateResolved();

        return $request;
    }

    /**
     * @return FormRequest
     */
    public function validatePayment(): FormRequest
    {
        return $this->validate();
    }

    /**
     * @return FormRequest
     */
    public function validateConfirmPayment(): FormRequest
    {
        return $this->validate();
    }

    /**
     * @return array
     */
    public function payment(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function confirm(): array
    {
        return [];
    }
}
