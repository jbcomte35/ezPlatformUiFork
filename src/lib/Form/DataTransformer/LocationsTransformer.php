<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Form\DataTransformer;

use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;

/**
 * Transforms between a Location's ID and a domain specific Location object.
 */
class LocationsTransformer implements DataTransformerInterface
{
    /** @var LocationService */
    protected $locationService;

    /**
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Transforms a domain specific Location objects into a Location's ID comma separated string.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    public function transform($value): ?string
    {
        /** TODO add sanity check is array of Location object? */
        if (!is_array($value) || empty($value)) {
            return null;
        }

        return implode(',', array_column($value, 'id'));
    }

    /**
     * Transforms a Location's ID string into a domain specific Location objects.
     *
     * @param mixed $value
     *
     * @return Location[]
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value): ?array
    {
        if (empty($value)) {
            return [];
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $value = explode(',', $value);

        try {
            return array_map([$this->locationService, 'loadLocation'], $value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
