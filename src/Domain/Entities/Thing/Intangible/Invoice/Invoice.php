<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Invoice;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class Invoice implements EntityInterface
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

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return 'Invoice';
	}

	public function getId(): int
	{
		return $this->idinvoice;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}
}
