<?php

namespace LukeZbihlyj\SilexORM\Query;

use Spot\Mapper;
use Spot\Query;
use Spot\Query\Resolver as BaseResolver;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Spot\Relation\BelongsTo;

/**
 * @package LukeZbihlyj\SilexORM\Query\Resolver
 */
class Resolver extends BaseResolver
{
    /**
     * @return bool
     * @throws SchemaException
     * @throws Exception
     */
    public function migrate()
    {
        $entity = $this->mapper->entity();
        $table = $entity::table();
        $fields = $this->mapper->entityManager()->fields();
        $fieldIndexes = $this->mapper->entityManager()->fieldKeys();
        $connection = $this->mapper->connection();

        $schemaManager = $this->mapper->connection()->getSchemaManager();
        $tableObject = $schemaManager->listTableDetails($table);
        $tableObjects[] = $tableObject;
        $schema = new Schema($tableObjects);

        $tableColumns = $tableObject->getColumns();
        $tableExists = !empty($tableColumns);

        if ($tableExists) {
            $existingTable = $schema->getTable($table);
            $newSchema = $this->migrateCreateSchema();
            $queries = $schema->getMigrateToSql($newSchema, $connection->getDatabasePlatform());
        } else {
            $newSchema = $this->migrateCreateSchema();
            $queries = $newSchema->toSql($connection->getDatabasePlatform());
        }

        $lastResult = false;
        foreach ($queries as $sql) {
            $lastResult = $connection->exec($sql);
        }

        return $lastResult;
    }

    /**
     * @return Schema
     */
    public function migrateCreateSchema()
    {
        $entityName = $this->mapper->entity();
        $table = $entityName::table();
        $indexes = [];
        $fields = $this->mapper->entityManager()->fields();
        $fieldIndexes = $this->mapper->entityManager()->fieldKeys();

        $schema = new Schema();
        $table = $schema->createTable($this->escapeIdentifier($table));

        if (method_exists($entityName, 'indexes')) {
            $indexes = $entityName::indexes();
        }

        foreach ($fields as $field) {
            $fieldType = $field['type'];
            unset($field['type']);
            $table->addColumn($this->escapeIdentifier($field['column']), $fieldType, $field);
        }

        if ($fieldIndexes['primary']) {
            $resolver = $this;
            $primaryKeys = array_map(function($value) use($resolver) { return $resolver->escapeIdentifier($value); }, $fieldIndexes['primary']);
            $table->setPrimaryKey($primaryKeys);
        }

        foreach ($fieldIndexes['unique'] as $keyName => $keyFields) {
            $table->addUniqueIndex($keyFields, $this->escapeIdentifier($this->trimSchemaName($keyName)));
        }

        foreach ($fieldIndexes['index'] as $keyName => $keyFields) {
            $table->addIndex($keyFields, $this->escapeIdentifier($this->trimSchemaName($keyName)));
        }

        foreach ($indexes as $keyName => $keyFields) {
            $table->addIndex($keyFields, $this->escapeIdentifier($this->trimSchemaName($this->mapper->table() . '_' . $keyName)));
        }

        $this->addForeignKeys($table);

        return $schema;
    }
}
