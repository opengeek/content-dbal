<?php

declare(strict_types=1);

namespace Opengeek\Content\Dbal;

use Doctrine\DBAL\Connection;
use Opengeek\Content\Contracts\ContentMapperInterface;
use Opengeek\Content\Contracts\ContentRepositoryInterface;
use Opengeek\Content\Exception\ContentNotFoundException;

/**
 * Base implementation for database-backed content loading using Doctrine DBAL.
 *
 * @template TDto
 * @template TCollection
 * @implements ContentRepositoryInterface<TDto, TCollection>
 */
abstract readonly class AbstractDbalRepository implements ContentRepositoryInterface
{
    /**
     * @param ContentMapperInterface<array<string, mixed>, TDto> $mapper
     */
    public function __construct(
        protected Connection $connection,
        protected ContentMapperInterface $mapper,
    ) {
    }

    public function findAll(): mixed
    {
        $qb = $this->connection->createQueryBuilder();
        $rows = $qb->select('*')
            ->from($this->getTableName())
            ->orderBy($this->getPublishDateColumn(), 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->createCollection(array_map(
            fn(array $row) => $this->mapper->map($row),
            $rows
        ));
    }

    public function findPublished(?\DateTimeImmutable $now = null): mixed
    {
        $now ??= new \DateTimeImmutable();
        $nowStr = $now->format('Y-m-d H:i:s');

        $qb = $this->connection->createQueryBuilder();
        $rows = $qb->select('*')
            ->from($this->getTableName())
            ->where(sprintf('%s <= :now', $this->getPublishDateColumn()))
            ->setParameter('now', $nowStr)
            ->orderBy($this->getPublishDateColumn(), 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return $this->createCollection(array_map(
            fn(array $row) => $this->mapper->map($row),
            $rows
        ));
    }

    public function findBySlug(string $slug): mixed
    {
        $qb = $this->connection->createQueryBuilder();
        $row = $qb->select('*')
            ->from($this->getTableName())
            ->where('slug = :slug')
            ->setParameter('slug', $slug)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw ContentNotFoundException::forSlug($slug);
        }

        return $this->mapper->map($row);
    }

    /**
     * @param array<int, TDto> $items
     * @return TCollection
     */
    abstract protected function createCollection(array $items): mixed;

    /**
     * @return string The table name for the content
     */
    abstract protected function getTableName(): string;

    /**
     * @return string The column name for the publish date
     */
    protected function getPublishDateColumn(): string
    {
        return 'publish_date';
    }
}
