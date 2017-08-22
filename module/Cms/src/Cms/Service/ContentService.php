<?php
namespace Cms\Service;

use Cms\Model\DataModels\Content;
use Cms\Model\ContentTable;

class ContentService
{
    /**
     * @var ContentTable
     */
    protected $contentTable;

	/**
	 * @param ContentTable $contentTable
	 */
    public function __construct(ContentTable $contentTable)
    {
        $this->contentTable = $contentTable;
    }

	/**
	 * @return Content[]|null
	 */
    public function getAll()
    {
        return $this->contentTable->getAll();
    }

    public function getById($id)
    {
        return $this->contentTable->getById($id);
    }

    public function getByUrl($url)
    {
        return $this->contentTable->getByUrl($url);
    }

    public function save($post)
    {
		if (! ($post instanceof Content || is_array($post))) return null;
        return $this->contentTable->save($post);
    }

    public function delete($post)
    {
        return $this->contentTable->deleteContent($post);
    }
}