<?php

namespace Weblinks\DAO;

use Weblinks\Domain\Link;

class LinkDAO extends DAO 
{
    /**
     * @var \Weblinks\DAO\UserDAO
     */
    protected $userDAO;

    public function setUserDAO($userDAO) {
        $this->userDAO = $userDAO;
    }

    public function find($id) {
        $sql = "select * from t_link where lin_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No url matching id " . $id);
    }
    /**
     * Returns a list of all links, sorted by id.
     *
     * @return array A list of all links.
     */
    public function findAll() {
        $sql = "select * from t_link order by lin_id desc";
        $result = $this->getDb()->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['lin_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }
    /**
     * Saves an url into the database.
     *
     * @param \Weblinks\Domain\Url $url The url to save
     */
    public function save(Link $url) {
        $urlData = array(
            'lin_title' => $url->getTitle(),
            'lin_url' => $url->getUrl(),
            'usr_id' => $url->getUser()->getId(),
        );
        if ($url->getId()) {
            // The url has already been saved : update it
            $this->getDb()->update('t_link', $urlData, array('lin_id' => $url->getId()));
        } else {
            // The url has never been saved : insert it
            $this->getDb()->insert('t_link', $urlData);
            // Get the id of the newly created url and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $url->setId($id);
        }
    }
    /**
     * Removes an url from the database.
     *
     * @param \Weblinks\Domain\Url $url The url to remove
     */
    public function delete($id) {
        // Delete the url
        $this->getDb()->delete('t_link', array('lin_id' => $id));
    }
    public function deleteAllByUser($userId) {
        $this->getDb()->delete('t_link', array('usr_id' => $userId));
    }
    /**
     * Creates an Link object based on a DB row.
     *
     * @param array $row The DB row containing Link data.
     * @return \Weblinks\Domain\Link
     */
    protected function buildDomainObject($row) {
        $link = new Link();
        $link->setId($row['lin_id']);
        $link->setUrl($row['lin_url']);
        $link->setTitle($row['lin_title']);

        if (array_key_exists('usr_id', $row)) {
            // Find and set the associated author
            $userId = $row['usr_id'];
            $user = $this->userDAO->find($userId);
            $link->setUser($user);
        }
        
        return $link;
    }
}
