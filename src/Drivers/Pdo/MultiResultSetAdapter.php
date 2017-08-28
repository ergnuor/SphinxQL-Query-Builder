<?php

namespace Foolz\SphinxQL\Drivers\Pdo;

class MultiResultSetAdapter implements \Foolz\SphinxQL\Drivers\MultiResultSetAdapterInterface
{
    /**
     * @var bool
     */
    protected $valid = true;

    /**
     * @var \PDOStatement
     */
    protected $statement = null;

    protected $cursor = 0;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    public function getNext()
    {
        if(version_compare(PHP_VERSION, '5.4.0', '>=')) {
            if (
                !$this->valid() ||
                !$this->statement->nextRowset()
            ) {
                $this->valid = false;
            }
        } else {
            $this->cursor++;
            if (
                !$this->valid() ||
                !$this->statement[$this->cursor]
            ) {
                $this->valid = false;
            }
        }
    }

    /**
     * @return ResultSet
     */
    public function current()
    {
        if(version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return ResultSet::make($this->statement);
        } else {
            return ResultSet::make($this->statement[$this->cursor]);
        }
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->statement && $this->valid;
    }
}
