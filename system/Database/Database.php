<?php

/**
 * BabiPHP : The flexible PHP Framework
 *
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP.
 * @link          https://github.com/lambirou/babiphp BabiPHP Project
 * @license       MIT
 *
 * Not edit this file
 */

namespace BabiPHP\Database;

use \PDO;
use \PDOException;

class Database
{
    /**
     * @var \BabiPHP\Database\ConnectionManager
     */
    private $manager;

    /**
     * @var string
     */
    private $returnType = 'object';

    /**
     * @var string
     */
    private $returnStatu = false;

    /**
     * @var mixed
     */
    private $error = null;

    /**
     * @var mixed
     */
    private $results = null;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $table_prefix = '';

    protected $_fields;

    protected $_where;

    protected $_order;

    protected $_group;

    protected $_limit;

    protected $_sqlType;

    protected $_sql;

    protected $_bind;


    protected $_fetch = true;

    private static $_instance;

    /**
     * Contructeur de la classe
     *
     * @param string $table
     */
    function __construct(string $table)
    {
        $this->manager = ConnectionManager::getInstance();
        $this->table_prefix = $this->manager->getTablePrefix();
        $this->table = $this->table_prefix . $table;

        self::$_instance = $this;
    }

    /**
     * Return the current instance of this class
     *
     * @return Database
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * Permet d'exécuter une requête
     *
     * @param string $sql
     * @param array $bind
     * @return mixed
     */
    public function query(string $sql, array $bind)
    {
        $this->_sql = $sql;
        $this->_bind = $this->cleanBind($bind);
        return $this->getResult();
    }

    /**
     * Compte le nombre d'enregistrement.
     *
     * @param string $fields
     * @return Database
     */
    public function countQuery($field = '*', string $alias = 'nb')
    {
        $this->_fields = 'count(' . $field . ') as ' . $alias;
        $this->_sqlType = 'count';
        $this->_fetch = false;

        return $this;
    }

    /**
     * Récupère des enregistrements
     *
     * @param string $fields
     * @return Database
     */
    public function selectQuery($fields = '*')
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }

        $this->_fields = $fields;
        $this->_sqlType = 'select';

        return $this;
    }

    /**
     * Crée un nouvel enregistrement.
     *
     * @param array $fields
     * @return Database
     */
    public function insertQuery(array $fields)
    {
        $this->_sqlData = $fields;
        $this->_sqlType = 'insert';
        return $this;
    }

    /**
     * Met à jour un enregistrement
     *
     * @param array $fields
     * @return Database
     */
    public function updateQuery(array $fields)
    {
        foreach ($fields as $column => $value) {
            if ($value === null) {
                $value = "NULL";
            } else {
                $value = '"' . $value . '"';
            }

            $field_array[] = '`' . $column . '` = ' . $value;
        }

        $this->_sqlData = implode(',', $field_array);
        $this->_sqlType = 'update';
        return $this;
    }

    /**
     * Supprime un enregistrement.
     *
     * @return Database
     */
    public function deleteQuery()
    {
        $this->_sqlType = 'delete';
        return $this;
    }

    /**
     * Permet de définir des conditions pour éffectuer des
     * requêtes sur la table
     *
     * @param array $where
     * @return Database
     */
    public function where($where = null)
    {
        if (!is_null($where)) {
            if (is_array($where)) {
                $where = implode(" AND ", $where);
            }

            $this->_where = $where;
        }

        return $this;
    }

    /**
     * Permet de définir l'ordre de récupération des informations.
     *
     * @param string $order
     * @return Database
     */
    public function orderBy($order = null)
    {
        if (!is_null($order)) {
            $this->_order = $order;
        }

        return $this;
    }

    /**
     * Permet de limiter les informations récupérées.
     *
     * @param string $order
     * @return Database
     */
    public function groupBy($group = null)
    {
        if (!is_null($group)) {
            $this->_group = $group;
        }

        return $this;
    }

    /**
     * Permet de limiter les informations récupérées.
     *
     * @param string $limit
     * @return Database
     */
    public function limit($limit = null)
    {
        if (!is_null($limit)) {
            $this->_limit = $limit;
        }

        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête au format JSON
     *
     * @return string
     */
    public function toJson()
    {
        $this->returnType = 'json';
        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête en tableau PHP
     *
     * @return array
     */
    public function toArray()
    {
        $this->returnType = 'array';
        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête au format BOTH
     *
     * @return void
     */
    public function toBoth()
    {
        $this->returnType = 'both';
        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête au format yml
     *
     * @return string
     */
    public function toYaml()
    {
        $this->returnType = 'yaml';
        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête au format XML
     *
     * @return string
     */
    public function toXml()
    {
        $this->returnType = 'xml';
        return $this;
    }

    /**
     * Permet de retourner le résultat de la requête au format CSV
     *
     * @return void
     */
    public function toCsv()
    {
        $this->returnType = 'csv';
        return $this;
    }

    /**
     * Permet de retourner un objet GraphResponse
     *
     * @return GraphResponse
     */
    public function getGraph()
    {
        $this->returnStatu = true;
        return $this;
    }

    /**
     * Permet de binder les paramètres de la requête
     *
     * @param array $bind
     * @return void
     */
    public function bind(array $bind = array())
    {
        $this->_bind = $this->cleanBind($bind);
        return ($this->_sqlType == 'delete' || $this->_sqlType == 'count') ? $this->save() : $this;
    }

    /**
     * Permet de retourner tous les résultats trouvés
     *
     * @return mixed
     */
    public function find()
    {
        return $this->getResult(true);
    }

    /**
     * Permet de retourner un seul résultat
     *
     * @return mixed
     */
    public function findOne()
    {
        return $this->getResult(false);
    }

    /**
     * Permet de traiter la requête
     *
     * @return mixed
     */
    public function save()
    {
        return $this->getResult();
    }

    /**
     * Permet de traiter la requête
     *
     * @return mixed
     */
    public function done()
    {
        return $this->getResult(false);
    }

    /**
     * Permet de lancer la requête
     *
     * @param  string $sql
     * @return mixed  The result or error
     */
    private function run($sql = null)
    {
        $db = $this->manager->getConnection();
        $this->error = '';

        if (!$this->_sql) {
            $this->_sql = (is_null($sql)) ? $this->buildQuery() : $sql;
        }

        try {
            $pdostmt = $db->prepare($this->_sql);
            $request = ($this->_bind) ? $pdostmt->execute($this->_bind) : $pdostmt->execute();

            if ($request) {
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->_sql)) {
                    $pdostmt->setFetchMode(PDO::FETCH_OBJ);
                    $this->results = ($this->_fetch) ? $pdostmt->fetchAll() : $pdostmt->fetch();
                } elseif (preg_match("/^(" . implode("|", array("insert")) . ") /i", $this->_sql)) {
                    $this->results = $db->lastInsertId();
                } elseif (preg_match("/^(" . implode("|", array("delete", "update")) . ") /i", $this->_sql)) {
                    $this->results = true;
                }
            }
        } catch (PDOException $e) {
            $this->error = $e;
        }
    }

    /**
     * Permet de filtrer les données de la table courante
     *
     * @param  string $table
     * @param  array $info
     * @return array
     */
    private function filter($table, $info)
    {
        $driver = $this->manager->getDriver();

        if ($driver == 'sqlite') {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        } elseif ($driver == 'mysql') {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        } else {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }

        $this->run($sql);

        if ($this->results !== false) {
            $fields = array();

            foreach ($list as $record) {
                $fields[] = $record->$key;
            }

            return array_values(array_intersect($fields, array_keys($info)));
        }

        return array();
    }

    /**
     * Permet de construire la requête selon le type
     *
     * @return string
     */
    private function buildQuery()
    {
        if ($this->_sqlType == 'count') {
            $sql = "SELECT " . $this->_fields . " FROM " . $this->table;

            if ($this->_where) {
                $sql .= " WHERE " . $this->_where;
            }
            if ($this->_order) {
                $sql .= " ORDER BY " . $this->_order;
            }
            if ($this->_limit) {
                $sql .= " LIMIT " . $this->_limit;
            }
        } elseif ($this->_sqlType == 'select') {
            $sql = "SELECT " . $this->_fields;
            $sql .= " FROM " . $this->table;

            if ($this->_where) {
                $sql .= " WHERE " . $this->_where;
            }
            if ($this->_group) {
                $sql .= " GROUP BY " . $this->_group;
            }
            if ($this->_order) {
                $sql .= " ORDER BY " . $this->_order;
            }
            if ($this->_limit) {
                $sql .= " LIMIT " . $this->_limit;
            }
        } elseif ($this->_sqlType == 'insert') {
            $columns = array();
            $values = array();

            foreach ($this->_sqlData as $column => $value) {
                $columns[] = '`' . $column . '`';

                if ($value === null) {
                    $values[] = "NULL";
                } else {
                    $values[] = '"' . $value . '"';
                }
            }

            $sql = "INSERT INTO `" . $this->table . "` (" . implode(',', $columns) . ")";
            $sql .= " VALUES (" . implode(',', $values) . ")";
        } elseif ($this->_sqlType == 'update') {
            $sql = "UPDATE " . $this->table . " SET ";
            $sql .= $this->_sqlData;

            if ($this->_where) {
                $sql .= " WHERE " . $this->_where;
            }
        } elseif ($this->_sqlType == 'delete') {
            $sql = "DELETE FROM " . $this->table;

            if ($this->_where) {
                $sql .= " WHERE " . $this->_where;
            }
        }

        return (isset($sql)) ? $sql . ';' : null;
    }

    /**
     * Permet de réinitialiser la classe
     *
     * @return void
     */
    private function reset()
    {
        $this->error = null;
        $this->results = null;
        $this->returnType = 'object';
        $this->returnStatu = false;
        $this->_fields = '';
        $this->_where = '';
        $this->_order = '';
        $this->_limit = null;
        $this->_sqlType = null;
        $this->_sql = null;
        $this->_bind = null;
        $this->_fetch = true;
    }

    /**
     * Retourne le résultat de la requête exécutée
     *
     * @param mixed $fetch
     * @return mixed
     */
    private function getResult($fetch = null)
    {
        if (is_bool($fetch)) {
            $this->_fetch = $fetch;
        }

        $this->run();

        if ($this->returnStatu) {
            $data = $this->voidClass();
            $data->error = $this->voidClass(['message' => null, 'line' => null, 'file' => null]);
            $data->request = $this->voidClass(['sql' => $this->_sql, 'bind' => $this->_bind, 'type' => $this->_sqlType, 'fields' => $this->_fields]);

            if (!is_null($this->results)) {
                $data->success = true;
                $data->response = $this->fetchMode($this->results);
            } elseif (!is_null($this->error)) {
                $data->success = false;
                $data->response = null;

                $data->error->message = $this->error->getMessage();
                $data->error->line = $this->error->getLine();
                $data->error->file = basename($this->error->getFile());
            }

            $data = new GraphResponse($data);
        } else {
            if (!is_null($this->results)) {
                $data = $this->fetchMode($this->results);
            } elseif (!is_null($this->error)) {
                $data = $this->error;
            }
        }

        $this->reset();

        return $data;
    }

    /**
     * Permet de convertir le type de retour du résultat de la reque
     *
     * @param mixed $results
     * @return mixed
     */
    private function fetchMode($results)
    {
        if (!is_bool($results) && !is_numeric($results)) {
            switch ($this->returnType) {
                case 'array':
                    $results = $this->objectToArray($results);
                    break;
                case 'both':
                    $results = $this->objectToBoth($results);
                    break;
                case 'json':
                    $results = json_encode($results);
                    break;
                case 'yaml':
                    $results = yaml_emit($results);
                    break;
                case 'csv':
                    $results = $this->arrayToCsv($this->objectToArray($results), 'sql_result');
                    break;
                default:
                    $results = $results;
                    break;
            }
        }

        return $results;
    }

    /**
     * cleanBind
     *
     * @param mixed $bind
     */
    private function cleanBind($bind)
    {
        if (!is_array($bind)) {
            $bind = (!empty($bind)) ? [$bind] : [];
        }

        return $bind;
    }

    /**
     * arrayToObject
     *
     * @param array $array
     * @return Object
     */
    private function arrayToObject(array $array)
    {
        if (is_array($array) && !empty($array)) {
            $d = new \stdClass();

            foreach ($array as $k => $v) {
                if (!empty($v) && is_array($v)) {
                    $v = $this->arrayToObject($v);
                }

                $d->$k = $v;
            }

            return $d;
        }
    }

    /**
     * objectToArray
     *
     * @param $object
     * @return Array
     */
    private function objectToArray($object)
    {
        if (is_object($object)) {
            return get_object_vars($object);
        } elseif (is_array($object)) {
            $data = array();

            foreach ($object as $key => $value) {
                $data[] = get_object_vars($value);
            }

            return $data;
        }
    }

    /**
     * objectToBoth
     *
     * @param  object $data
     * @return array
     */
    private function objectToBoth($object)
    {
        if (count($object) > 1) {
            $new_array = array();

            foreach ($object as $key => $value) {
                $value = $this->objectToArray($value);
                $num = array_values($value);
                $new_array[] = array_merge($value, $num);
            }

            $data = $new_array;
        } else {
            $data = $this->objectToArray($object);
            $num = array_values($data);
            $data = array_merge($data, $num);
        }

        return $data;
    }

    private function arrayToCsv($input_array, $output_file_name, $delimiter = ',')
    {
        ob_start();

        $f = fopen('php://memory', 'w');

        foreach ($input_array as $line) {
            fputcsv($f, $line, $delimiter);
        }

        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        fpassthru($f);

        return ob_get_clean();
    }

    /**
     * create one void class
     *
     * @param array
     * @return object
     */
    private function voidClass($array = [])
    {
        $class = new \stdClass;

        foreach ($array as $key => $value) {
            $class->$key = $value;
        }

        return $class;
    }

    /**
     * Close connection
     */
    public function __destruct()
    {
        $this->manager = null;
        $this->table = '';
        $this->table_prefix = '';
        self::$_instance = null;
    }
}
