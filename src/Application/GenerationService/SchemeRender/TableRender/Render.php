<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\TableRender;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\Schema;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\DbmlTypeEnum;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\DbmlParameterEnum;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\SupportedTypeEnum;

final readonly class Render
{
    public function render(Schema $schema): string
    {
        $schemaTextList = [];
        foreach ($schema->classItemMap as $classItem) {
            $lineList = [];
            foreach ($classItem->itemMap as $propertyName => $propertyItem) {
                $propertyList = [
                    "\"$propertyName\"",
                ];

                $type = $propertyItem->type;
                switch ($type) {
                    case SupportedTypeEnum::INT:
                    case SupportedTypeEnum::INTEGER:
                        $propertyList[] = DbmlTypeEnum::INT;
                        break;
                    case SupportedTypeEnum::BOOL:
                    case SupportedTypeEnum::BOOLEAN:
                        $propertyList[] = DbmlTypeEnum::BOOL;
                        break;
                    case SupportedTypeEnum::STRING:
                    case SupportedTypeEnum::HASHED:
                        $propertyList[] = DbmlTypeEnum::TEXT;
                        break;
                    case SupportedTypeEnum::DATE:
                    case SupportedTypeEnum::IMMUTABLE_DATE:
                        $propertyList[] = DbmlTypeEnum::DATE;
                        break;
                    case SupportedTypeEnum::FLOAT:
                    case SupportedTypeEnum::DOUBLE:
                        $propertyList[] = DbmlTypeEnum::DECIMAL;
                        break;
                    case SupportedTypeEnum::DATETIME:
                    case SupportedTypeEnum::TIMESTAMP:
                    case SupportedTypeEnum::IMMUTABLE_DATETIME:
                        $propertyList[] = DbmlTypeEnum::DATETIME;
                        break;
                    default:
                        $enumItemMap = $schema->enumItemMap;
                        if (true === key_exists($type, $enumItemMap)) {
                            $propertyList[] = DbmlTypeEnum::ENUM;
                        }
                        break;
                }

                $propertyAttributeTextList = [];
                if (true === in_array($propertyName, $classItem->primaryKey->properties)) {
                    $propertyAttributeTextList[] = DbmlParameterEnum::PRIMARY_KEY;
                    $propertyAttributeTextList[] = DbmlParameterEnum::NOT_NULL;

                    if (true === $classItem->primaryKey->autoincrement) {
                        $propertyAttributeTextList[] = DbmlParameterEnum::INCREMENT;
                    }
                }

                if (count($propertyAttributeTextList) > 0) {
                    $propertyList[] = sprintf('[%s]', implode(', ', $propertyAttributeTextList));
                }

                if (count($propertyList) > 1) {
                    $lineList[] = implode(' ', $propertyList);
                }
            }

            $tableName = $classItem->tableName;
            if (mb_strlen($tableName) > 0) {
                $body = "\t" . implode(PHP_EOL . "\t", $lineList);
                $schemaTextList[] = <<<TEXT
Table "$tableName" {
$body
}
TEXT;
            }
        }

        return implode(PHP_EOL . PHP_EOL, $schemaTextList);
    }
}
