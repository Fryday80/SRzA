<?php
namespace Application\Service;

use Application\Model\MailTemplatesTable;

class MailTemplateService
{
    /** @var MailTemplatesTable  */
    private $mailTemplatesTable;

    function __construct(MailTemplatesTable $mailTemplatesTable)
    {
        $this->mailTemplatesTable = $mailTemplatesTable;
    }

    public function getAllTemplates()
    {
        return $this->mailTemplatesTable->getAllTemplates();
    }
    public function getBy(Array $select){
        return $this->mailTemplatesTable->getBy($select);
    }
    public function getByID($id)
    {
        return $this->getBy(array('id' => $id));
    }
    public function getByName($name)
    {
        return $this->getBy(array( 'name' => $name));
    }

    public function save(Array $data)
    {
        return $this->mailTemplatesTable->save($data);
    }
    
    public function deleteBy(Array $select)
    {
        return $this->mailTemplatesTable->deleteBy($select);
    }
    public function deleteByID($id)
    {
        return $this->deleteBy(array('id' => $id));
    }
    public function deleteByName($name)
    {
        return $this->deleteBy(array('name' => $name));
    }

    public function isBuildIn($key, $column = null) {
        if ($column === null) {
            $select = (is_array($key))                     ? $key                  : null;
            $select = ($entry === null || is_string($key)) ? array('name' => $key) : null;
            $select = ($entry === null || is_int($key))    ? array('id' => $key)   : null;
        }
        return $this->mailTemplatesTable->isBuildIn($select);
    }
}