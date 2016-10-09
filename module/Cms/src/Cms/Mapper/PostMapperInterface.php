<?php
 namespace Cms\Mapper;

 use Cms\Model\PostInterface;

 interface PostMapperInterface
 {
     /**
      * @param int|string $id
      * @return PostInterface
      * @throws \InvalidArgumentException
      */
     public function find($id);

     /**
      * @param string $url
      * @return PostInterface
      * @throws \InvalidArgumentException
      */
     public function findByUrl($url);
     /**
      * @return array|PostInterface[]
      */
     public function findAll();
     /**
      * @param PostInterface $postObject
      *
      * @param PostInterface $postObject
      * @return PostInterface
      * @throws \Exception
      */
     public function save(PostInterface $postObject);
     /**
      * @param PostInterface $postObject
      *
      * @return bool
      * @throws \Exception
      */
     public function delete(PostInterface $postObject);
 }