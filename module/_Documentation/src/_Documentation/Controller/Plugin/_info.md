#Controller Plugins
- ## Application Module
    - ##### [MessageRedirect](###MessageRedirect)
    - ##### [DefaultView](###DefaultView)
    
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

###DefaultView
>Provides access to default view models
- module: Application
- regName: defaultView
```
    public $vars;

    public function setVars($vars);
    public function addVars($vars);
    public function setVar;
    // get default view for:
    public function delete($vars = null);
    public function edit($vars = null);
```
` ->vars ` provides std array of vars  
` ->addVars($vars) ` uses ` $vars + $this->vars ` to avoid multiple entries per key

###ImagePlugin
>Plugin to manage Image upload and delete via MediaService   
- module: Media
- regName: image
```
public function upload($data, $dataTargetPaths, $uploadFileNames);
```
- `$data:` &ensp; &ensp; &ensp; &ensp; &ensp; &ensp;&ensp; single | array of ... form upload arrays
- `$dataTargetPaths:` single | array of ... target path strings
- `$uploadFileNames:` single | array of ... target filename strings
- ` @return: ` &ensp; &ensp; &ensp; &ensp; &ensp;&ensp;  array of [MediaItem](../../Model/_info.md)s
