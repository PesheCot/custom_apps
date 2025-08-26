<?php
/**
 * @copyright Copyright (c) 2025
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

namespace OCA\ReadonlyMenuProtect\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IL10N;

class Admin implements ISettings {

    private $l10n;

    public function __construct(IL10N $l10n) {
        $this->l10n = $l10n;
    }

    public function getForm(): TemplateResponse {
        return new TemplateResponse('readonly-menu-protect', 'settings');
    }

    public function getSection(): string {
        return 'additional';
    }

    public function getPriority(): int {
        return 90;
    }
}
