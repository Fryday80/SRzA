#Controller Plugins
- ## Application Module
    - ##### [MessageRedirect](###MessageRedirect)
    
- ## Media Module
    - ##### [ImagePlugin](###ImagePlugin)

#
###MessageRedirect
- module: Application
- regName: ??  @todo ist nicht registriert

setzt eine Flash Massage und redirect zu message page
```
    public function __invoke($title, $msg);
```

###ImagePlugin
- module: ImagePlugin
- regName: image

>DE: Plugin um die Bilder über den Media Service hoch zu laden und zu löschen
>EN: Plugin to manage Image upload and delete via MediaService
```
public function upload($data, $dataTargetPaths, $uploadFileNames);
```
- $data:            single | array of ... form upload arrays
- $dataTargetPaths: single | array of ... target path strings
- $uploadFileNames: single | array of ... target filename strings
- @**return**: $mediaItems: array of [MediaItems]
