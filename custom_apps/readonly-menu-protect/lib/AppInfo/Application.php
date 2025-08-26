<?php
/**
 * @copyright Copyright (c) 2025
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ReadonlyMenuProtect\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\Util;

class Application extends App implements IBootstrap {

    public const APP_ID = 'readonly-menu-protect';
    public const READONLY_GROUP = 'readonly_users';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Пока не требуется
    }

    public function boot(IBootContext $context): void {
        $server = $context->getServerContainer();
        $eventDispatcher = $server->get(IEventDispatcher::class);

        $eventDispatcher->addListener(
            BeforeTemplateRenderedEvent::class,
            function(BeforeTemplateRenderedEvent $event) use ($server) {
                $userSession = $server->get(IUserSession::class);
                $user = $userSession->getUser();

                if ($user === null) {
                    return;
                }

                $groupManager = $server->get(IGroupManager::class);

                if ($groupManager->isInGroup($user->getUID(), self::READONLY_GROUP)) {
                    Util::addScript(self::APP_ID, 'readonly-menu-protect');
                    Util::addStyle(self::APP_ID, 'readonly-style');
                    Util::addBodyClass('readonly-mode');
                }
            }
        );
    }
}
