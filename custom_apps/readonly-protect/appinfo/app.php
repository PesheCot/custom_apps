<?php
/**
 * @copyright Copyright (c) 2024 Admin
 * @license GNU AGPL version 3 or any later version
 */

declare(strict_types=1);

use OCA\ReadonlyProtect\AppInfo\Application;

require_once __DIR__ . '/../../autoload.php';

$app = new Application();
$app->register();
