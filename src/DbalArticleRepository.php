<?php

declare(strict_types=1);

namespace Opengeek\Content\Dbal;

use Doctrine\DBAL\Connection;
use Opengeek\Content\Exception\ContentNotFoundException;
use Opengeek\Content\Article;
use Opengeek\Content\ArticleCollection;
use Opengeek\Content\ArticleRepositoryInterface;

/**
 * Read-only Article repository implementation using Doctrine DBAL.
 */
final readonly class DbalArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private Connection $connection,
        private DbalArticleMapper $mapper = new DbalArticleMapper()
    ) {
    }

    public function findAll(): ArticleCollection
    {
        $qb = $this->connection->createQueryBuilder();
        $rows = $qb->select('*')
            ->from('articles')
            ->orderBy('publish_date', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return new ArticleCollection(array_map(
            fn(array $row) => $this->mapper->mapToDto($row),
            $rows
        ));
    }

    public function findPublished(?\DateTimeImmutable $now = null): ArticleCollection
    {
        $now ??= new \DateTimeImmutable();
        $nowStr = $now->format('Y-m-d H:i:s');

        $qb = $this->connection->createQueryBuilder();
        $rows = $qb->select('*')
            ->from('articles')
            ->where('publish_date <= :now')
            ->setParameter('now', $nowStr)
            ->orderBy('publish_date', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        return new ArticleCollection(array_map(
            fn(array $row) => $this->mapper->mapToDto($row),
            $rows
        ));
    }

    public function findBySlug(string $slug): Article
    {
        $qb = $this->connection->createQueryBuilder();
        $row = $qb->select('*')
            ->from('articles')
            ->where('slug = :slug')
            ->setParameter('slug', $slug)
            ->executeQuery()
            ->fetchAssociative();

        if ($row === false) {
            throw ContentNotFoundException::forSlug($slug);
        }

        return $this->mapper->mapToDto($row);
    }
}
