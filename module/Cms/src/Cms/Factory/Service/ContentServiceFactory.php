<?php
 namespace Cms\Factory\Service;

 use Cms\Model\Tables\ContentTable;
 use Cms\Service\ContentService;
 use Zend\ServiceManager\FactoryInterface;
 use Zend\ServiceManager\ServiceLocatorInterface;

 class ContentServiceFactory implements FactoryInterface
 {
     /**
      * Create service
      *s
      * @param ServiceLocatorInterface $serviceLocator
      * @return mixed
      */
     public function createService(ServiceLocatorInterface $serviceLocator)
     {
     	/** @var ContentTable $contentTable */
     	$contentTable = $serviceLocator->get('Cms\Model\ContentTable');
         return new ContentService( $contentTable );
     }
 }