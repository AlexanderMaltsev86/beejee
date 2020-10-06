<?php


namespace App\Models;


use App\Framework\Model;

/**
 * Model for 'users' table
 * @package App\Models
 * @property int $id
 * @property string $login
 * @property string $password_hash
 */
class User extends Model {
    protected static function getTableName() {
        return "users";
    }

    protected static function getFillableColumns() {
        return [
            'id',
            'login',
            'password_hash'
        ];
    }
}