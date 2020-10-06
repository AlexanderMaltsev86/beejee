<?php
namespace App\Framework;

use App\Exceptions\DatabaseException;
use Medoo\Medoo;
use PDOException;

/**
 * Base class for all models
 * @package App\Framework
 */
abstract class Model {
    /**
     * @var Medoo
     */
    private static $database;

    protected abstract static function getTableName();
    protected abstract static function getFillableColumns();

    protected static function getPrimaryKey() {
        return 'id';
    }
    /**
     * @return Medoo
     * @throws DatabaseException
     */
    private static function getDatabase() {
        if (!self::$database) {
            try {
                $di = DI::getInstance();
                self::$database = new Medoo($di->get('dbConfig'));
            } catch (PDOException $exception) {
                throw new DatabaseException($exception);
            }
        }
        return self::$database;
    }

    private function getValuesMap() {
        $valuesMap = [];
        foreach (static::getFillableColumns() as $columnName) {
            if ($columnName !== static::getPrimaryKey() || $this->{$columnName} !== null) { //Skip empty primary key value
                $valuesMap[$columnName] = $this->{$columnName};
            }
        }
        return $valuesMap;
    }

    /**
     * Create a new row in the table
     * @throws DatabaseException
     */
    public function create() {
        $result = self::getDatabase()->insert(static::getTableName(), $this->getValuesMap());
        if (!$result) {
            throw new DatabaseException("Could not create row in database: " . self::getDatabase()->error()[2]);
        }
        if ($result->errorInfo() && !empty($result->errorInfo()[2])) {
            throw new DatabaseException("Could not create row in database: " . $result->errorInfo()[2]);
        }
    }

    /**
     * Update a row in the table
     * @throws DatabaseException
     */
    public function update() {
        $result = self::getDatabase()->update(static::getTableName(), $this->getValuesMap(), [
            static::getPrimaryKey() => $this->{static::getPrimaryKey()}
        ]);
        if (!$result) {
            throw new DatabaseException("Could not update row in database: " . self::getDatabase()->error()[2]);
        }
        if ($result->errorInfo() && !empty($result->errorInfo()[2])) {
            throw new DatabaseException("Could not update row in database: " . $result->errorInfo()[2]);
        }
    }

    public function delete() {
        $result = self::getDatabase()->delete(static::getTableName(), $this->getValuesMap());
        if (!$result) {
            throw new DatabaseException("Could not delete row in database: " . self::getDatabase()->error()[2]);
        }
        if (!$result->errorInfo()) {
            throw new DatabaseException("Could not delete row in database: " . $result->errorInfo());
        }
    }

    private static function getItemFromRow($row) {
        $item = new static();
        foreach ($row as $column => $value) {
            $item->{$column} = $value;
        }
        return $item;
    }

    /**
     * Get elements from table filtered by params
     * @param array|null $params
     * @return array
     * @throws DatabaseException
     */
    public static function find(?array $params = null) {
        $result = self::getDatabase()->select(static::getTableName(), static::getFillableColumns(), $params);
        if (!$result && self::getDatabase()->error()[2]) {
            throw new DatabaseException("Could not select rows from database: " . self::getDatabase()->error()[2]);
        }
        $items = [];
        foreach ($result as $row) {
            $items[] = self::getItemFromRow($row);
        }
        return $items;
    }

    /**
     * Get first element from table filtered by params
     * @param array|null $params
     * @return static|null
     * @throws DatabaseException
     */
    public static function findFirst(?array $params = null) {
        $params['LIMIT'] = 1;
        $result = self::getDatabase()->select(static::getTableName(), static::getFillableColumns(), $params);
        if (!$result && self::getDatabase()->error()[2]) {
            throw new DatabaseException("Could not select a row from database: " . self::getDatabase()->error()[2]);
        }
        if (count($result) > 0) {
            return self::getItemFromRow($result[0]);
        } else {
            return null;
        }
    }

    /**
     * Get count of elements filtered by params
     * @param array|null $params
     * @return static|null
     * @throws DatabaseException
     */
    public static function count(?array $params = []) {
        $result = self::getDatabase()->count(static::getTableName(), '*', $params);
        if (!$result && self::getDatabase()->error()[2]) {
            throw new DatabaseException("Could not count rows in database: " . self::getDatabase()->error()[2]);
        }
        return $result;
    }
}