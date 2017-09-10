# Services
- in Application
    - ##### [CacheService](#CacheService)
    - ##### [DataService](#DataService)
    - ##### [MessageService](#MessageService)
    - ##### [StatisticService](#StatisticService)
    - ##### [SystemService](#SystemService)
- in Auth
    - ##### [AccessService](#AccessService);
    - ##### [AclService](#AclService);
    - ##### [UserService](#UserService);
- in Calendar
    - ##### [CalendarService](#CalendarService)
- in Cast
    - ##### [BlazonService](#BlazonService)
    - ##### [CastService](#CastService)
- in Cms
    - ##### [ContentService](#ContentService)
- in Equipment
    - ##### [EquipmentService](#EquipmentService)
    - ##### [LostAndFoundService](#LostAndFoundService)
- in Gallery
    - ##### [GalleryService](#GalleryService)
- in Media
    - ##### [MediaService](#MediaService)



<a id="MediaService"></a>
---
### MediaService
>File System Management  
>Restricted to `/Data` folder
- module: Media
- regName: MediaService

Verwaltet das filesystem. Der mediaService ist beschränkt auf den "Data" ordner


###### single file upload example
```
/** @var MediaService $mediaService */
$mediaService = $this->systemService->serviceManager->get('MediaService');

$fileData = $this->getRequest()->getFiles()->toArray()['File'];
try {
    $uploadHandler = $mediaService->uploadHandlerFactory($fileData, '/gallery/pups', true);
    $uploadHandler->autoRename = true;
    $uploadHandler->upload();
} catch (Exception $e) {
    bdump($e->getMessage());
}

```

###### multi file upload example
```
/** @var MediaService $mediaService */
$mediaService = $this->systemService->serviceManager->get('MediaService');

$filesData = $this->getRequest()->getFiles()->toArray();
try {
    $mediaService->multiUpload($filesData, '/gallery/pups', "neuerName");
    // or 
    $mediaService->multiUpload($filesData, ['/gallery/pups'], ['neuerName']);
    // wenn arrays benutz werden müssen die die selbe länge haben!
} catch (Exception $e) {
    bdump($e->getMessage());
}
```

<a id="DataService"></a>
---
### DataService
Todo