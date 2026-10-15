<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Invoice;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Invoice extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Order'];

	protected int $idinvoice;
	public mixed $customer;
	public mixed $referencesOrder;
	public mixed $provider;
	public float $totalPaymentDue;
	public string $scheduledPaymentDate;
	public int $numberOfPaymentDue;
	public int $numberOfInvoices;
	public ?string $paymentDueDate = null;
	public ?string $paymentStatus = null;

	public function __construct(array $value = ['@type' => 'Invoice'])
	{
		parent::__construct(['@type' => 'Invoice']);

		$this->customer = $value['customer'] ?? null;
		$this->referencesOrder = $value['referencesOrder'] ?? null;
		$this->provider = $value['provider'] ?? null;
		$this->totalPaymentDue = $value['totalPaymentDue'] ?? null;
		$this->scheduledPaymentDate = $value['scheduledPaymentDate'] ?? null;
		$this->numberOfPaymentDue = $value['numberOfPaymentDue'] ?? null;
		$this->numberOfInvoices = $value['numberOfInvoices'] ?? null;
		$this->paymentDueDate = $value['paymentDueDate'] ?? null;
		$this->paymentStatus = $value['paymentStatus'] ?? null;

	}

	public function getId(): int
	{
		return $this->idinvoice;
	}

}
