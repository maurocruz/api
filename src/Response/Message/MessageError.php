<?php
namespace Plinct\Api\Response\Message;

class MessageError extends MessageAbstract
{
	public function __construct()
	{
		parent::setStatus('error');
	}

	/** GENERIC */
	public function generic(array $data = null, string $message = 'An error occurred!'): array {
		$this->setMessage($message);
		$this->setData($data);
		return $this->returns;
	}

	public function anErrorHasOcurred($data): array
	{
		return parent::getReturns('E0001','an error has occurred', $data);
	}
}
