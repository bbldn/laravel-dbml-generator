<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\Schema;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\TableRender\Render as TableRender;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\RelationshipsRender\Render as RelationshipsRender;

final readonly class Render
{
    private TableRender $tableRender;

    private RelationshipsRender $relationshipsRender;

    public function __construct()
    {
        $this->tableRender = new TableRender();
        $this->relationshipsRender = new RelationshipsRender();
    }

    public function render(Schema $schema): string
    {
        return $this->tableRender->render($schema) . PHP_EOL . PHP_EOL . $this->relationshipsRender->render($schema);
    }
}
