;<?php exit("Access Denied."); ?>
; All values in this file may be overwritten by site configuration files

; ---------------------------------------------------------------------------- ;
; Modules to load at startup (order is important)
; Variable Format: ClassName = "Alias" (blank = ClassName as Alias)
; ---------------------------------------------------------------------------- ;

[startup]
url             = "url"
request         = "req"
template        = "tpl"
database        = "db"

; ---------------------------------------------------------------------------- ;
; Add module-specific settings below
; Section Format: [ClassName]
; Variable Format: variable = "value"
; ---------------------------------------------------------------------------- ;

[framework]
environment     = "development"
salt            = "saltiness"

[database]
server          = "server"
username        = "username"
password        = "password"
database        = "database"
