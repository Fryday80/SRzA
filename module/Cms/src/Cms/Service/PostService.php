<?php
namespace Cms\Service;

use Cms\Mapper\PostMapperInterface;
use Cms\Model\PostInterface;

class PostService implements PostServiceInterface
{
    /**
     * @var \Blog\Mapper\PostMapperInterface
     */
    protected $postMapper;

    /**
     * @param PostMapperInterface $postMapper
     */
    public function __construct(PostMapperInterface $postMapper)
    {
        $this->postMapper = $postMapper;
    }

    public function findAllPosts()
    {
        return $this->postMapper->findAll();
    }

    public function findPost($id)
    {
        return $this->postMapper->find($id);
    }

    public function findByUrl($url)
    {
        return $this->postMapper->findByUrl($url);
    }

    public function savePost(PostInterface $post)
    {
        return $this->postMapper->save($post);
    }

    public function deletePost(PostInterface $post)
    {
        return $this->postMapper->delete($post);
    }
}