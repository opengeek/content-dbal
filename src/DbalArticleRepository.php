<?php

declare(strict_types=1);

namespace Opengeek\Content\Dbal;

use Opengeek\Content\Article;
use Opengeek\Content\ArticleCollection;
use Opengeek\Content\ArticleRepositoryInterface;
use Doctrine\DBAL\Connection;

/**
 * Read-only Article repository implementation using Doctrine DBAL.
 *
 * @extends AbstractDbalRepository<Article, ArticleCollection>
 */
final readonly class DbalArticleRepository extends AbstractDbalRepository implements ArticleRepositoryInterface
{
    public function __construct(
        Connection $connection,
        DbalArticleMapper $mapper = new DbalArticleMapper()
    ) {
        parent::__construct($connection, $mapper);
    }

    public function findAll(): ArticleCollection
    {
        return parent::findAll();
    }

    public function findPublished(?\DateTimeImmutable $now = null): ArticleCollection
    {
        return parent::findPublished($now);
    }

    public function findBySlug(string $slug): Article
    {
        return parent::findBySlug($slug);
    }

    protected function createCollection(array $items): ArticleCollection
    {
        return new ArticleCollection($items);
    }

    protected function getTableName(): string
    {
        return 'articles';
    }
}
