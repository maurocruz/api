<?php
namespace Plinct\Api\Request\Configuration\Module;

class Modules
{
	/**
	 * @return array
	 */
	public function action(): array
	{
		return ModuleController::installer('Action');
	}

	/**
	 * @return array
	 */
	public function audioObject(): array
	{
		return ModuleController::installer('AudioObject',['MediaObject']);
	}

	/**
	 * @return array
	 */
	public function article(): array
	{
		return ModuleController::installer('Article',['Person','Organization']);
	}

	/**
	 * @return array
	 */
	public function book(): array
	{
		return ModuleController::installer('Book',['Person','Organization']);
	}

	/**
	 * @return array
	 */
	public function certification(): array
	{
		return ModuleController::installer('Certification', ['Organization']);
	}

	/**
	 * @return array
	 */
	public function collection(): array
	{
		return ModuleController::installer('Collection',['creativeWork']);
	}

	/**
	 * @return array
	 */
	public function contactPoint(): array
	{
		return ModuleController::installer('ContactPoint',['Thing']);
	}

	/**
	 * @return array
	 */
	public function creativeWork(): array
	{
		return ModuleController::installer('CreativeWork',['Thing']);
	}

	/**
	 * @return array
	 */
	public function event(): array
	{
		return ModuleController::installer('Event', ['Thing','Place']);
	}

	/**
	 * @return array
	 */
	public function imageObject(): array
	{
		return ModuleController::installer('ImageObject', ['MediaObject']);
	}

	/**
	 * @return array
	 */
	public function mediaObject(): array
	{
		return ModuleController::installer('MediaObject', ['CreativeWork']);
	}

	/**
	 * @return array
	 */
	public function order(): array
	{
		return ModuleController::installer('Order', ['Thing']);
	}

	/**
	 * @return array
	 */
	public function organization(): array
	{
		return ModuleController::installer('Organization', ['Thing','Place','Person','Role']);
	}

	/**
	 * @return array
	 */
	public function person(): array
	{
		return ModuleController::installer('Person', ['Thing','ImageObject','ContactPoint','Place','Organization','Role']);
	}

	/**
	 * @return array
	 */
	public function place(): array
	{
		return ModuleController::installer('Place',['Thing','ContactPoint','ImageObject']);
	}

	/**
	 * @return array
	 */
	public function product(): array
	{
		return ModuleController::installer('Product', ['Thing','ImageObject']);
	}

	/**
	 * @return array
	 */
	public function review(): array
	{
		return ModuleController::installer('Review', ['Thing']);
	}

	/**
	 * @return array
	 */
	public function service(): array
	{
		return ModuleController::installer('Service', ['Thing','ImageObject']);
	}

	/**
	 * @return array
	 */
	public function taxon(): array
	{
		return ModuleController::installer('Taxon', ['Thing','ImageObject']);
	}

	/**
	 * @return array
	 */
	public function thing(): array
	{
		return ModuleController::installer('Thing');
	}

	/**
	 * @return array
	 */
	public function videoObject(): array
	{
		return ModuleController::installer('VideoObject',['mediaObject']);
	}

	/**
	 * @return array
	 */
	public function webSite(): array
	{
		return ModuleController::installer('WebSite', ['ImageObject']);
	}
}
