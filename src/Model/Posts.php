<?php

namespace App\Model;

use \BabiPHP\Database\Table;

class Posts
{
    private $table = null;

    public function __construct()
	{
        $this->table = new Table('posts');
	}

    function getById($id, $fields = '*')
    {
        $bind[':id'] = $id;
        return $this->table->select($fields)->where('id = :id')->bind($bind)->findOne();
    }

    function getAll()
    {
        return $this->table->select()->find();
    }
}