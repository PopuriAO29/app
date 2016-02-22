<?php

namespace Wikia\Service\User\Permissions;

use Wikia\DependencyInjection\Injector;

trait PermissionsAccessor {

	/**
	 * @var PermissionsService
	 */
	private $permissionsService;

	/**
	 * @return PermissionsService
	 */
	private function permissionsService() {
		if ( is_null( $this->permissionsService ) ) {
			$this->permissionsService = Injector::getInjector()->get( PermissionsService::class );
		}

		return $this->permissionsService;
	}
} 