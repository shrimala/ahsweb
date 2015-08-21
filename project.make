api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.0.0-beta14
;project[drupal][patch][url]=/sites/default/files/civipatch1.patch
;projects[drupal][patch][]= http://ssdkolkata.net/baisakhi/autoload_real.php.patch
;projects[drupal][patch][]= http://ssdkolkata.net/baisakhi/autoload_flysystem_psr4.php.patch

;******************************
;    civicrm-drupal
;******************************


libraries[civicrmdrupal][destination] = modules
libraries[civicrmdrupal][directory_name] = civicrm
libraries[civicrmdrupal][download][type] = get
libraries[civicrmdrupal][download][url] = http://ssdkolkata.net/baisakhi/civicrm-drupal-2.tar.gz
libraries[civicrmdrupal][overwrite] = TRUE


;****************************
;     civicrm-core
;****************************


libraries[civicrm][destination] = libraries
libraries[civicrm][directory_name] = civicrm
libraries[civicrm][download][type] = get
libraries[civicrm][download][url] = http://ssdkolkata.net/baisakhi/civicrm-core-3.tar.gz
libraries[civicrm][overwrite] = TRUE
;libraries[civicrm][patch][] = http://ssdkolkata.net/baisakhi/civi-core-requirements2.patch
libraries[civicrm][patch][] = http://ssdkolkata.net/baisakhi/civi-Install-Requirements-patch.patch
;libraries[civicrm][patch][] = http://ssdkolkata.net/baisakhi/ClassLoader-patch.patch

;******************************
;    Other Modules
;******************************

projects[panels][version]=3.0-alpha12
projects[layout_plugin][version]=1.0-alpha12
projects[page_manager][version]=1.0-alpha12
projects[eck][version]=1.x-dev
projects[paragraphs][version]=1.x-dev
projects[token][version]=1.x-dev
;projects[pathauto][version]=1.x
projects[eform][version]=1.x-dev
projects[simplenews][version]=1.x-dev
projects[inmail][version]=1.x-dev
projects[mailmute][version]=1.x-dev
projects[media_pinkeye][version]=1.x-dev
projects[media_entity][version]=1.x-dev
projects[advanced_help][version]=1.x-dev
projects[entity_reference_revisions][version]=1.x-dev
projects[composer_manager][version]=1.x-dev
projects[flysystem][version]=1.x-dev
projects[flysystem_dropbox][version]=1.x-dev
projects[flysystem_s3][version]=1.x-dev
;projects[jplayer][version]=2.x-dev

libraries[pathauto][destination]=modules
libraries[pathauto][directory_name]=pathauto
libraries[pathauto][download][type]=git
libraries[pathauto][download][url]=https://github.com/md-systems/pathauto.git
libraries[pathauto][download][branch]=8.x-1.x
libraries[pathauto][overwrite]=True

;libraries[flysystem][destination]=core/vendor/league
;libraries[flysystem][directory_name]=flysystem
;libraries[flysystem][download][type]=git
;libraries[flysystem][download][url]=https://github.com/thephpleague/flysystem.git
;libraries[flysystem][download][branch]=master
;libraries[flysystem][overwrite]=True

;libraries[flysystem-cached-adapter][destination]=core/vendor/league
;libraries[flysystem-cached-adapter][directory_name]=flysystem-cached-adapter                  
;libraries[flysystem-cached-adapter][download][type]=git
;libraries[flysystem-cached-adapter][download][url]=https://github.com/thephpleague/flysystem-cached-adapter.git
;libraries[flysystem-cached-adapter][download][branch]=master
;libraries[flysystem-cached-adapter][overwrite]=True


;libraries[flysystem-replicate-adapter][destination]=core/vendor/league
;libraries[flysystem-replicate-adapter][directory_name]=flysystem-replicate-adapter
;libraries[flysystem-replicate-adapter][download][type]=git
;libraries[flysystem-replicate-adapter][download][url]=https://github.com/thephpleague/flysystem-replicate-adapter.git
;libraries[flysystem-replicate-adapter][download][branch]=master
;libraries[flysystem-replicate-adapter][overwrite]=True

;libraries[flysystem-stream-wrapper][destination]=core/vendor/league
;libraries[flysystem-stream-wrapper][directory_name]=flysystem-stream-wrapper
;libraries[flysystem-stream-wrapper][download][type]=git
;libraries[flysystem-stream-wrapper][download][url]=https://github.com/twistor/flysystem-stream-wrapper.git
;libraries[flysystem-stream-wrapper][download][branch]=master
;libraries[flysystem-stream-wrapper][overwrite]=True


libraries[media_entity_embeddable_video][destination]=modules
libraries[media_entity_embeddable_video][directory_name]=Media_entity_embeddable_video
libraries[media_entity_embeddable_video][download][type]=git
libraries[media_entity_embeddable_video][download][url]=https://github.com/drupal-media/media_entity_embeddable_video.git
libraries[media_entity_embeddable_video][overwrite]=True


libraries[media_entity_image][destination]=modules
libraries[media_entity_image][directory_name]=Media_entity_image
libraries[media_entity_image][download][type]=git
libraries[media_entity_image][download][url]=https://github.com/drupal-media/media_entity_image.git
libraries[media_entity_image][overwrite]=True

libraries[media_entity_twitter][destination]=modules
libraries[media_entity_twitter][directory_name]=Media_entity_twitter           
libraries[media_entity_twitter][download][type]=git
libraries[media_entity_twitter][download][url]=https://github.com/drupal-media/media_entity_twitter.git
libraries[media_entity_twitter][overwrite]=True

libraries[drupal_swiftmailer][destination]=modules
libraries[drupal_swiftmailer][directory_name]=drupal_swiftmailer
libraries[drupal_swiftmailer][download][type]=git
libraries[drupal_swiftmailer][dowload][url]=https://github.com/webflo/drupal-swiftmailer.git
libraries[drupal_swiftmailer][download][branch]=8.x-1.x
libraries[drupal_swiftmailer][overwrite]=True

libraries[media_entity_instagram][destination]=modules
libraries[media_entity_instagram][directory_name]=Media_entity_instagram           
libraries[media_entity_instagram][download][type]=git
libraries[media_entity_instagram][download][url]=https://github.com/drupal-media/media_entity_instagram.git
libraries[media_entity_instagram][overwrite]=True


libraries[media_entity_slideshow][destination]=modules
libraries[media_entity_slideshow][directory_name]=Media_entity_slideshow       
libraries[media_entity_slideshow][download][type]=git
libraries[media_entity_slideshow][download][url]=https://github.com/drupal-media/media_entity_slideshow.git
libraries[media_entity_slideshow][overwrite]=True

libraries[image_title_caption][destination]=modules/custom
libraries[image_title_caption][directory_name]=image_title_caption
libraries[image_title_caption][download][type]=git
libraries[image_title_caption][download][url]=https://github.com/aritnath1990/image_title_caption.git
libraries[image_title_caption][overwrite]=True

libraries[media_player_module][destination]=modules/custom
libraries[media_player_module][directory_name]=media_player_module
libraries[media_player_module][download][type]=git
libraries[media_player_module][download][url]=https://github.com/aritnath1990/media_player_module.git
libraries[media_player_module][download][revision]=e303deaaac38a7a9203ba4b1cdea0d1c2321f3c7
libraries[media_player_module][overwrite]=True

