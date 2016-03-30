<?php
namespace Weleoka\Users;

/**
 * Model for Users.
 *
 */
class UsersdbModel implements \Anax\DI\IInjectionAware
{

    use \Anax\DI\TInjectable;


    /**
     * Save current object/row.
     *
     * @param array $values key/values to save or empty to use object properties.
     *
     * @return boolean true or false if saving went okey.
     */
    public function save($values = [])
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        if (isset($values['id'])) {
            return $this->update($values);

        } else {

            return $this->create($values);
        }
    }


    /**
     * Delete row.
     *
     * @param integer $id to delete.
     *
     * @return boolean true or false if deleting went okey.
     */
    public function delete($id)
    {
        $this->db->delete(
            $this->getSource(),
            'id = ?'
        );

        return $this->db->execute([$id]);
    }


    /**
     * Find and return specific user by ID.
     *
     * @return this
     */
    public function find($id)
    {

        if (isset($id)) {
            $this->db->select()
                 ->from($this->getSource())
                 ->where("id = ?");
            $this->db->execute([$id]);

            return $this->db->fetchInto($this);

        } else {
            echo "No user found, sorry";
    //      $id = $this->db->lastInsertId();
    //      $this->db->select()
    //          ->from($this->getSource())
    //          ->where("id = ?");
    //      echo $id;
    //      $this->db->execute([$id]);
    //      return $this->db->fetchInto($this);
        }
    }


    /**
     * Find and return all.
     *
     * @return array
     */
    public function findAll()
    {
        $this->db->select()
             ->from($this->getSource());

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
   }


    /**
     * Build a select-query.
     *
     * @param string $columns which columns to select.
     *
     * @return $this
     */
    public function query($columns = '*')
    {
        $this->db->select($columns)
                 ->from($this->getSource());

        return $this;
    }


    /**
     * Build the where part.
     *
     * @param string $condition for building the where part of the query.
     *
     * @return $this
     */
    public function where($condition)
    {
        $this->db->where($condition);

        return $this;
    }


    /**
     * Build the and where part.
     *
     * @param string $condition for building the where part of the query.
     *
     * @return $this
     */
    public function andWhere($condition)
    {
        $this->db->andWhere($condition);

        return $this;
    }


    /**
     * Execute the query built.
     *
     * @param string $query custom query.
     *
     * @return $this
     */
    public function execute($params = [])
    {
        $this->db->execute($this->db->getSQL(), $params);
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }


    /**
     * Create new row.
     *
     * @param array $values key/values to save.
     *
     * @return boolean true or false if saving went okey.
     */
    public function create($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);

        $this->db->insert(
            $this->getSource(),
            $keys
        );

        $res = $this->db->execute($values);
        $this->id = $this->db->lastInsertId();

        return $res;
    }


    /**
     * Update row.
     *
     * @param array $values key/values to save.
     *
     * @return boolean true or false if saving went okey.
     */
    public function update($values)
    {
        $keys   = array_keys($values);
        $values = array_values($values);

        // Its update, remove id and use as where-clause
        unset($keys['id']);
        $values[] = $this->id;

        $this->db->update(
            $this->getSource(),
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }


    /**
     * Get the table name.
     *
     * @return string with the table name.
     */
    public function getSource()
    {

        return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
    }


    /**
     * Get object properties.
     *
     * @return array with object properties.
     */
    public function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['di']);
        unset($properties['db']);

        return $properties;
    }


    /**
     * Set object properties.
     *
     * @param array $properties with properties to set.
     *
     * @return void
     */
    public function setProperties($properties)
    {
        // Update object with incoming values, if any
        if (!empty($properties)) {

            foreach ($properties as $key => $val) {
                $this->$key = $val;
            }
        }
    }
}