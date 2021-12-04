<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector;
use Rector\Core\Configuration\Option;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfReturnToEarlyReturnRector;
use Rector\Restoration\Rector\ClassLike\UpdateFileNameByClassNameFileSystemRector;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Rector\MethodCall\ContainerGetToConstructorInjectionRector;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $services = $containerConfigurator->services();

    $parameters->set(
        Option::PATHS,
        [
            __DIR__ . '/src',
            __DIR__ . '/migrations',
            __DIR__ . '/tests',
        ]
    );

    // Sets
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::CODING_STYLE_ADVANCED);
    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::EARLY_RETURN);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
    $containerConfigurator->import(SymfonySetList::SYMFONY_52);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CODE_QUALITY);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION);

    // Rules
    $services->set(UpdateFileNameByClassNameFileSystemRector::class);

    // Parameters

    $parameters->set(
        Option::PHPSTAN_FOR_RECTOR_PATH,
        __DIR__ . '/phpstan.neon'
    );

    $parameters->set(
        Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER,
        __DIR__ . '/var/cache/dev/srcApp_KernelDevDebugContainer.xml'
    );

    $parameters->set(
        Option::SKIP,
        [
            SimplifyUselessVariableRector::class,
            ContainerGetToConstructorInjectionRector::class,
            ChangeOrIfReturnToEarlyReturnRector::class,
        ],
    );
};
