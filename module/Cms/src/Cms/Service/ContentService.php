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
    public function findAllPosts()
    {
        return $this->contentTable->findAll();
    }

    public function findPost($id)
    {
        return $this->contentTable->findById($id);
    }

    public function findByUrl($url)
    {
        return $this->contentTable->findByUrl($url);
    }

    public function savePost(Content $post)
    {
        return $this->contentTable->save($post);
    }

    public function deletePost(Content $post)
    {
        return $this->contentTable->deleteContent($post);
    }
}