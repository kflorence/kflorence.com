;<?php if (!BASE_PATH) exit("Access denied."); ?>

; ---------------------------------------------------------------------------- ;
; Add module-specific settings below
; Section Format: [ClassName]
; Variable Format: variable = "value"
; ---------------------------------------------------------------------------- ;


[Flickr]
api_key = "flickr_api_key"
secret = "flickr_api_secret"
cache_dir = "./sites/kflorence.com/cache/flickr/"
cache_expire = 43200

; ---------------------------------------------------------------------------- ;
; Load page-specific modules below
; Section Format: [Page:pagename]
; Variable Format: ClassName = "Alias" (blank = (lowercase) ClassName as Alias)
; ---------------------------------------------------------------------------- ;

[Page:work]
Pager =         ""

[Page:play]
Pager =         ""
