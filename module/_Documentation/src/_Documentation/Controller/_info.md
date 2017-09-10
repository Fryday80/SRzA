#Controller
- ## Application Module
    - ##### [SystemController](#SystemController)
- ## Auth Module
    - ##### [AuthController](#AuthController)
    - ##### [PermissionController](#PermissionController)
    - ##### [ProfileController](#ProfileController)
    - ##### [ResourceController](#ResourceController)
    - ##### [RoleController](#RoleController)
    - ##### [UserController](#UserController)
- ## Calendar Module
- ## Cast Module
- ## Cms Module
- ## Equipment Module
- ## Gallery Module
- ## Media Module
- ## Nav Module

<a name="SystemController"></a>
---
- ## SystemController
- module: Application
- routes:
```
test    => /test
phpinfo => /php
system  => /system
message => /message
```

- action: `test` url `/test` 
    - test area
- action: `formtest` url `/system/formtest`
    - style test for all Form Elements
- action: `dashboard` url `/system/Dashboard` or `/system`
    - dashboard for Admins
- action: `mailTemplatesIndex` url `/system/mailTemplates`
    - lists all mail templates
- action: `mailTemplate` url `/system/mailTemplates/:templateName`
    - unused ? 
- action: `json` url `/system/json`
    - performs json requests
- action: `maintenance` url `none`
    - this is default when site is in maintenance mode
- action: `message` url `/message`
- action: `settings` url `none` 
    - unused
- action: `php` url `/php`
    - shows php info
    
<a name="AuthController"></a>
---
- ## AuthController
- module: Auth
- routes:
```
test    => /test
phpinfo => /php
system  => /system
message => /message
```