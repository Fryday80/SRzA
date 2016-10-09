<?php
namespace Cms\Service;

use Cms\Model\PostInterface;

interface PostServiceInterface
{
    public function findAllPosts();

    public function findPost($id);

    public function findByUrl($url);

    public function savePost(PostInterface $blog);

    public function deletePost(PostInterface $blog);
}