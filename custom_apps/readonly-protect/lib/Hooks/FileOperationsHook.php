<?php
/**
 * @copyright Copyright (c) 2024 Admin
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ReadonlyProtect\Hooks;

use OCP\IUserSession;
use OCP\IGroupManager;
use OCP\Files\ForbiddenException;

class FileOperationsHook {
    
    private $userSession;
    private $groupManager;

    public function __construct(IUserSession $userSession, IGroupManager $groupManager) {
        $this->userSession = $userSession;
        $this->groupManager = $groupManager;
    }

    public function checkPermissions(): void {
        $user = $this->userSession->getUser();
        if ($user !== null) {
            $userId = $user->getUID();
            
            if ($this->groupManager->isInGroup($userId, 'readonly_users')) {
                throw new ForbiddenException(
                    'Readonly users are not allowed to modify files',
                    true
                );
            }
        }
    }
}
