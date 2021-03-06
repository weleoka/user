<?php

namespace Weleoka\Users;


/**
 * Model for redis database of user.
 *
 */
class UsersdbModelRedis {



    /**
     * Get keys from db.
     *
     * @param array $values keys to look for. * for all.
     *
     * @return array query result
     */
    public function keys($values = [])
    {

        return $this->redis->keys($values);
    }


    /**
     * Hash get all key/value pairs.
     *
     * @param string $key The key/name of the hash.
     *
     * @return array query result.
     */
    public function hgetall($key)
    {

        return $this->redis->hgetall($key);
    }


    /**
     * Hash get one entry with key/value pairs.
     *
     * @param string $key The key/name of the hash.
     *
     * @return array query result.
     */
    public function hget($hash, $key)
    {

        return $this->redis->hget($hash, $key);
    }


    /**
     * Save current object/row.
     *
     * @param array $values key/values to save or empty to use object properties.
     *
     * @return boolean true or false if saving went okey.
     */
    public function save($values = [])
    {

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

    }


    /**
     * Find and return specific user by ID.
     *
     * @return this
     */
    public function find($id)
    {

    }


    /**
     * Find and return all.
     *
     * @return array
     */
    public function findAll()
    {

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

    }


    /**
     * Get the class name.
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


    /**
     * Get a timestamp
     *
     * @return string timestamp.
     */
    public function getTime()
    {

        return date('Y-m-d H:i:s');
    }
}