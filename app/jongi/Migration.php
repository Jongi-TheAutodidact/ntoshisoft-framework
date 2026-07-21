<?php

/**
 * Migration class
 */



defined('CPATH') or exit('Access Denied!');


class Migration
{
    use Database;

    protected $columns      = [];
    protected $keys         = [];
    protected $primaryKeys  = [];
    protected $uniqueKeys   = [];
    protected $data         = [];
    protected $foreignKeys  = [];

    protected function addForeignKey($column, $foreignTable, $foreignColumn, $onDelete = 'RESTRICT', $onUpdate = 'RESTRICT')
    {
        $this->foreignKeys[] = [
            'column' => $column,
            'foreign_table' => $foreignTable,
            'foreign_column' => $foreignColumn,
            'on_delete' => $onDelete,
            'on_update' => $onUpdate
        ];
    }

    protected function createTable($table)
    {
        if (!empty($this->columns)) {

            $query = "CREATE TABLE IF NOT EXISTS $table (";

            foreach ($this->columns as $column) {
                $query .= $column . ",";
            }

            foreach ($this->primaryKeys as $key) {
                $query .= "PRIMARY KEY (" . implode(', ', $key) . "),";
            }

            foreach ($this->uniqueKeys as $key) {
                $query .= "UNIQUE KEY (" . $key . "),";
            }

            foreach ($this->keys as $key) {
                $query .= "KEY (" . $key . "),";
            }

            foreach ($this->foreignKeys as $fk) {
                $query .= "CONSTRAINT fk_{$table}_{$fk['column']} ";
                $query .= "FOREIGN KEY ({$fk['column']}) ";
                $query .= "REFERENCES {$fk['foreign_table']}({$fk['foreign_column']}) ";
                $query .= "ON DELETE {$fk['on_delete']} ";
                $query .= "ON UPDATE {$fk['on_update']},";
            }

            $query = trim($query, ",");
            $query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $this->query($query);

            $this->columns      = [];
            $this->keys         = [];
            $this->primaryKeys  = [];
            $this->uniqueKeys   = [];
            $this->foreignKeys  = []; 

            echo "\n\r Success :) - Table '$table' affected! \n\r";
        } else {

            echo "\n\r Table '$table' could not be created! \n\r";
        }
    }

    protected function addColumn($text)
    {
        $this->columns[] = $text;
    }
    protected function addPrimaryKey($key)
    {
        if (is_array($key)) {
            $this->primaryKeys[] = $key;
        } else {
            $this->primaryKeys[] = [$key];
        }
    }
    protected function addUniqueKey($key)
    {
        $this->uniqueKeys[] = $key;
    }
    protected function addKey($key)
    {
        $this->keys[] = $key;
    }
    protected function addData($key, $value)
    {
        $this->data[$key] = $value;
    }
    protected function insertData($table)
    {
        if (!empty($this->data)) {

            $keys = array_keys($this->data);
            $query = "insert into $table (" . implode(",", $keys) . ") values (:" . implode(",:", $keys) . ")";
            $this->query($query, $this->data);

            $this->data   = [];

            echo "\n\r Data inserted successfully into table: $table \n\r";
        } else {
            echo "\n\r Data could not be inserted into table: $table \n\r";
        }
    }

    protected function dropTable($tablename)
    {
        $this->query('drop table ' . $tablename);
        echo "\n\r Table '$tablename' deleted successfully. \n\r";
    }
}
