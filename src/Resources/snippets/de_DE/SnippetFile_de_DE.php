<?php

declare(strict_types=1);

namespace Omikron\FactFinder\Shopware6\Resources\snippets\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'factfinder.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/factfinder.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
    }

    public function getAuthor(): string
    {
        return 'Omikron Data Quality GmbH';
    }

    public function isBase(): bool
    {
        return false;
    }
}
