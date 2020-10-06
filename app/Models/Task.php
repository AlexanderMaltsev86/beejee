<?php


namespace App\Models;

use App\Framework\Model;

/**
 * Model for 'tasks' table
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $text
 * @property bool $status
 * @property string $updated_by
 */
class Task extends Model {
    protected static function getTableName() {
        return "tasks";
    }

    protected static function getFillableColumns() {
        return [
            'id',
            'name',
            'email',
            'text',
            'completed',
            'updated_by'
        ];
    }
}