# Models
- ## Application Module
    - ##### Abstract Models
        - ##### [AbstractModel](#AbstractModel)
        - ##### [DatabaseTable](#DatabaseTable)
    - ##### Enums
    - ##### Models / DataModels
    - ##### Interfaces
    - ##### Tables
- ## Auth Module
    - ##### Abstract Models
    - ##### Enums
    - ##### Models / DataModels
    - ##### Interfaces
    - ##### Tables
- ## Calendar Module
- ## Cast Module
- ## Cms Module
- ## Equipment Module
- ## Gallery Module
- ## Media Module
    - ##### Abstract Models
    - ##### Enums
    - ##### Models / DataModels
        - ##### [MediaItem](#MediaItem)
    - ##### Interfaces
    - ##### Tables
- ## Nav Module

<a name="AbstractModel"></a>
---
###AbstractModel
`class AbstractModel implements ArrayAccess, IObjectToArray, IHydratorModelAccess` 
>Basic Model for Data Models
- module: Application
- type: Basic Model


- allows array access to object
- allows hydrator access to object
- contains method ->toArray() to get (public) values as array

<a name="DatabaseTable"></a>
---
###DatabaseTable
`class DatabaseTable extends AbstractTableGateway`  
>Basic Model for Tables
- module: Application
- type: Basic Model

```php
    public function __construct(Adapter $adapter, $objectPrototype, Hydrator $hydrator = null )
    
    public function getAll()
    public function getById($id)
    public function getNextId()
	
    public function add($data)
    
    public function save($data)
    
    public function remove($id)
    
    public function hydrate(Array $data)
    
    public function select($where = null)
    
    public function update($set, $where = null, array $joins = null)
    
    public function insert($set)
    
    protected function entityToArray($entity)
    
    protected function prepareSelect($select)
    
    protected function prepareDataForSave($data)
    
    protected function getByKey($key, $value, $asArray = false)
```
- standard db access

<a name="MediaItem"></a>
---
###MediaItem
` class MediaItem `
>Model for MediaService  


- module: Application
- type: Explicit Model / Standalone

```
    class MediaItem
```
return type from [MediaService](../Service/_info.md#MediaService) requests  
[goto Services](../Service/_info.md)