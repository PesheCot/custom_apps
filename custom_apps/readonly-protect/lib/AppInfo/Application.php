<?php
/**
 * @copyright Copyright (c) 2024 Admin
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ReadonlyProtect\AppInfo;

use OCA\ReadonlyProtect\Hooks\FileOperationsHook;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUserSession;
use OCP\Util;

class Application extends App implements IBootstrap {
    
    public const APP_ID = 'readonly-protect';
    public const READONLY_GROUP = 'readonly_users';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerService(FileOperationsHook::class, function($c) {
            return new FileOperationsHook(
                $c->get(IUserSession::class),
                $c->get(\OCP\IGroupManager::class)
            );
        });
    }

    public function boot(IBootContext $context): void {
        $server = $context->getServerContainer();
        $userSession = $server->get(IUserSession::class);
        $eventDispatcher = $server->get(IEventDispatcher::class);

        $eventDispatcher->addListener(
            BeforeTemplateRenderedEvent::class,
            function(BeforeTemplateRenderedEvent $event) use ($userSession, $server) {
                $user = $userSession->getUser();
                if ($user !== null) {
                    $groupManager = $server->get(\OCP\IGroupManager::class);
                    
                    if ($groupManager->isInGroup($user->getUID(), self::READONLY_GROUP)) {
                        Util::addScript(self::APP_ID, 'readonly-protect');
                        Util::addStyle(self::APP_ID, 'readonly-style');
                    }
                }
            }
        );
    }

    public static function isReadonlyUser(string $userId, \OCP\IGroupManager $groupManager): bool {
        return $groupManager->isInGroup($userId, self::READONLY_GROUP);
    }
}
