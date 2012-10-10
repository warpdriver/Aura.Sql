<?php
namespace Aura\Sql;

use Aura\Sql\Query\Select;
use Aura\Sql\Query\Insert;
use Aura\Sql\Query\Update;
use Aura\Sql\Query\Delete;

// table data gateway
class Gateway
{
    protected $connections;
    
    protected $mapper;
    
    public function __construct(
        ConnectionLocator $connections,
        AbstractMapper $mapper
    ) {
        $this->connections = $connections;
        $this->mapper   = $mapper;
    }
    
    public function getConnections()
    {
        return $this->connections;
    }
    
    public function getMapper()
    {
        return $this->mapper;
    }
    
    public function insert($object)
    {
        $connection = $this->connections->getWrite();
        $insert = $connection->newInsert();
        $this->mapper->modifyInsert($insert, $object);
        $connection->query($insert, $insert->getBind());
        return $connection->lastInsertId();
    }
    
    public function update($object, $initial_data = null)
    {
        $connection = $this->connections->getWrite();
        $update = $connection->newUpdate();
        $this->mapper->modifyUpdate($update, $object, $initial_data);
        return $connection->query($update, $update->getBind());
    }
    
    public function delete($object)
    {
        $connection = $this->connections->getWrite();
        $delete = $connection->newDelete();
        $this->mapper->modifyDelete($delete, $object);
        return $connection->query($delete, $delete->getBind());
    }

    public function newSelect(array $cols = [])
    {
        $connection = $this->connections->getRead();
        $select = $connection->newSelect();
        $this->mapper->modifySelect($select, $cols);
        return $select;
    }
    
    public function fetchOneBy($col, $val)
    {
        $select = $this->newSelectBy($col, $val);
        return $this->fetchOne($select);
    }
    
    public function fetchAllBy($col, $val)
    {
        $select = $this->newSelectBy($col, $val);
        return $this->fetchAll($select);
    }
    
    protected function newSelectBy($col, $val)
    {
        $select = $this->newSelect();
        $where = $this->getMapper()->getTableCol($col);
        if (is_array($val)) {
            $where .= ' IN (?)';
        } else {
            $where .= ' = ?';
        }
        $select->where($where, $val);
        return $select;
    }
    
    public function fetchAll(Select $select, array $bind = [])
    {
        $connection = $select->getConnection();
        return $connection->fetchAll($select, $bind);
    }
    
    public function fetchCol(Select $select, array $bind = [])
    {
        $connection = $select->getConnection();
        return $connection->fetchCol($select, $bind);
    }
    
    public function fetchOne(Select $select, array $bind = [])
    {
        $connection = $select->getConnection();
        return $connection->fetchOne($select, $bind);
    }
    
    public function fetchPairs(Select $select, array $bind = [])
    {
        $connection = $select->getConnection();
        return $connection->fetchPairs($select, $bind);
    }
    
    public function fetchValue(Select $select, array $bind = [])
    {
        $connection = $select->getConnection();
        return $connection->fetchValue($select, $bind);
    }
}