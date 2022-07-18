<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Aws Ses Bundle.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use SerendipityHQ\Integration\Rector\SerendipityHQ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);
    $parameters->set(Option::BOOTSTRAP_FILES, [__DIR__ . '/vendor-bin/phpunit/vendor/autoload.php']);

    $containerConfigurator->import(SerendipityHQ::SHQ_SYMFONY_BUNDLE);

    $toSkip = SerendipityHQ::buildToSkip(SerendipityHQ::SHQ_SYMFONY_BUNDLE_SKIP);
    $parameters->set(Option::SKIP, $toSkip);
};
