#Controller Plugins
- in Application
    - ##### [MessageRedirect](###MessageRedirect) 
    - ##### [DataTableHelper](###DataTableHelper)
    - ##### [MyUrl](###MyUrl)
    - ##### [MyViewHelper](###MyViewHelper)
- in Auth
- in Media
    - ##### [ImagePlugin](###ImagePlugin)






###MessageRedirect
- module: Application
- regName: ??  @todo ist nicht registriert

setzt eine Flash Massage und redirect zu message page
```
    public function __invoke($title, $msg);
```


###DataTableHelper
- module: Application
- regName: ??
```
```


###MyUrl
- module: Application
- regName: ??
```
```
###MyViewHelper
- module: Application
- regName: ??
```
```

###ImagePlugin
- module: ImagePlugin
- regName: image

>DE: Plugin um die Bilder über den Media Service hoch zu laden und zu löschen
>EN: Plugin to manage Image upload and delete via MediaService
```
in Controller xy:
$mediaItems = $imageUpload->upload($data, $dataTargetPaths, $uploadFileNames);
```
- $data:            array of form upload arrays
- $dataTargetPaths: single target path string | array of target path strings
- $uploadFileNames: single target file name | array of target filename strings
- @return: $mediaItems: array of [MediaItems]
