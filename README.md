# opengeek/content-dbal

![PHP ^8.3](https://img.shields.io/badge/PHP-%5E8.3-blue)

SQL persistence for `opengeek/content` using Doctrine DBAL.

## Installation

```bash
composer require opengeek/content-dbal
```

## Features
- **Generic Architecture**: `AbstractDbalRepository` for consistent SQL-backed repositories.
- **Mappers**: Decoupled `DbalArticleMapper` for simple DTO-to-row mapping.
- **Read/Write Support**: Extensible repository and persister patterns.
- **Relational Storage**: Built on Doctrine DBAL for broad database compatibility.
- **SQLite Support**: Preconfigured SQLite schema management for testing and small apps.

## Usage

```php
use Doctrine\DBAL\DriverManager;
use Opengeek\Content\Dbal\DbalArticleRepository;
use Opengeek\Content\Dbal\DbalArticlePersister;

$connection = DriverManager::getConnection($params);

$repository = new DbalArticleRepository($connection);
$persister = new DbalArticlePersister($connection);

$article = $repository->findBySlug('my-article');
$persister->save($updatedArticle);
```
