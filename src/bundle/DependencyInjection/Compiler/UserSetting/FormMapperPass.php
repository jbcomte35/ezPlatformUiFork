<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUiBundle\DependencyInjection\Compiler\UserSetting;

use EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException;
use EzSystems\EzPlatformAdminUi\UserSetting\FormMapperRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormMapperPass implements CompilerPassInterface
{
    public const TAG_NAME = 'ezplatform.admin_ui.user_setting.form_mapper';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \EzSystems\EzPlatformAdminUi\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(FormMapperRegistry::class)) {
            return;
        }

        $registryDefinition = $container->getDefinition(FormMapperRegistry::class);
        $taggedServiceIds = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['identifier'])) {
                    throw new InvalidArgumentException(
                        $taggedServiceId,
                        sprintf("Tag '%s' must contain 'identifier' argument.", self::TAG_NAME)
                    );
                }

                $registryDefinition->addMethodCall(
                    'addFormMapper',
                    [$tag['identifier'], new Reference($taggedServiceId)]
                );
            }
        }
    }
}
