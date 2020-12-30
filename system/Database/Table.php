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

/**
 * Représention d'une table da la base de donnée
 *
 * Cette interface fournis des méthodes simplifiées pour manipuler
 * la table en éffectuant des requêtes sql
 */
class Table
{

    /**
     * @var Database
     */
    private $database = null;

    /**
     * Constructor
     *
     * @param string $table
     * @param array $config Configuration pour la connexion à la base de donnée
     */
    function __construct(string $table, array $config = [])
    {
        if ($config) {
            $manager = ConnectionManager::getInstance();

            if (isset($config['data'])) {
                $manager->addConfiguration($config['name'], $config['data']);
            }

            $manager->setCurrentConfigName($config['name']);
        }

        $this->database = new Database($table);
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
        return $this->database->query($sql, $bind);
    }

    /**
     * Compte le nombre d'enregistrement.
     *
     * @param string $field
     * @param string $alias
     * @return Database
     */
    public function count(string $field = '*', string $alias = 'nb')
    {
        return $this->database->countQuery($field, $alias);
    }

    /**
     * Récupère des enregistrements dans la base de donnée
     *
     * @param string $param
     * @return Database
     */
    public function select($param = '*')
    {
        return $this->database->selectQuery($param);
    }

    /**
     * Crée un nouvel enregistrement dans la base de donnée
     *
     * @param array $param
     * @return Database
     */
    public function insert(array $param)
    {
        return $this->database->insertQuery($param);
    }

    /**
     * Met à jour un enregistrement dans la base de donnée
     *
     * @param array $param
     * @return Database
     */
    public function update(array $param)
    {
        return $this->database->updateQuery($param);
    }

    /**
     * Supprime un enregistrement dans la base de donnée
     *
     * @return Database
     */
    public function delete()
    {
        return $this->database->deleteQuery();
    }
}
