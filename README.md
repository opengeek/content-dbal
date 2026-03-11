# opengeek/content-dbal

![PHP ^8.3](https://img.shields.io/badge/PHP-%5E8.3-blue)

SQL persistence for `opengeek/content` using Doctrine DBAL.

## Installation

```bash
composer require opengeek/content-dbal
```

## Features

- **Read/Write**: Full repository and persister support.
- **Relational Storage**: Backed by Doctrine DBAL for broad database support.
- **SQLite Support**: Includes an optional SQLite-specific schema manager.

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
