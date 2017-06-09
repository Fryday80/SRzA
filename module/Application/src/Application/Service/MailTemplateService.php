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
    public function getByID($id)
    {
        return $this->mailTemplatesTable->getByID($id);
    }

    public function save(Array $data)
    {
        return $this->mailTemplatesTable->save($data);
    }
    public function deleteByID($id)
    {
        return $this->mailTemplatesTable->deleteByID($id);
    }

    public function isBuildIn($id)
    {
        $entry = $this->getByID($id);
        if ($entry === null) return false;
        if ($entry['build_in'] == 1) return true;
        return false;
    }
}