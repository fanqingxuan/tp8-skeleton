<?php

/*
 * This file is part of the League\Fractal package.
 *
 * (c) Phil Sturgeon <me@philsturgeon.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace extend;

use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

class ArraySerializer extends SerializerAbstract
{
    /**
     * {@inheritDoc}
     */
    public function collection(?string $resourceKey, array $data): array
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function item(?string $resourceKey, array $data): array
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function null(): ?array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function includedData(ResourceInterface $resource, array $data): array
    {
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function meta(array $meta): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function paginator(PaginatorInterface $paginator): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function cursor(CursorInterface $cursor): array
    {
        return [];
    }
}
