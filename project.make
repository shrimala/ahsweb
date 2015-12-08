api = 2
core = 8.0

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.0.1

;******************************
;    Other Modules
;******************************
;projects[panels][version]=3.0-alpha12
projects[panels][revision]=a5015828dc77720f6684a22178c9361ff0389f2e
projects[layout_plugin][revision]=a989e4dff1c17ccbca5ff9169edf3f7a7de856af
;projects[layout_plugin][version]=1.0-alpha12
;projects[page_manager][revision]=e9dcb0d2d6850b9b6e33d0c7930ced502d01e550
projects[page_manager][version]=1.0-alpha12
;projects[eck][version]=1.x-dev
projects[eck][revision]=acfdad4aa35834f473636e96328a95436c4ba46d
;projects[paragraphs][version]=1.x-dev
projects[paragraphs][revision]=740874bacb56a6a90683e7818c06ba77a638125c
;projects[token][version]=1.x-dev
projects[token][revision]=5a36ca581d4c02d0afead767b48263e29af83824
;projects[eform][version]=1.x-dev
projects[eform][revision]=cece909ce4c8ee98f97f803591c0ad43b77f3c17
;projects[inmail][version]=1.x-dev
projects[inmail][revision]=19bde15111d08cdc9b69c2474ef9e14e758891c8
;projects[mailmute][version]=1.x-dev
projects[mailmute][revision]=f340adaded7905999acd0eca6b31484b7ac5db51
;projects[media_entity][version]=1.x-dev
projects[media_entity][revision]=27c402b0f5d26066e0f10c91e77ee8b0df6dd101
;projects[advanced_help][version]=1.x-dev
projects[advanced_help][revision]=a7f30e17815a65032b83529fd21ea615022645b9
;projects[entity_reference_revisions][version]=1.0-rc1
projects[entity_reference_revisions][revision]=6a811cd957fa3cd0f22ac9a9f788fae1fd79d520
;projects[composer_manager][version]=1.0-rc1
projects[composer_manager][revision]=247d558abf22803075c9c1043ef7b7d4194b3d04
projects[flysystem][version]=1.x-dev
;projects[flysystem][revision]=aa8b84c1c1656b9a8faea8ad1b344cf078674440
;projects[flysystem_dropbox][version]=1.x-dev
projects[flysystem_dropbox][revision]=8b9b4b0b69aff47c8afd395de5226cfb4f640860
;projects[flysystem_s3][version]=1.x-dev
projects[flysystem_s3][revision]=40570814e6ceefd0a500380ec642acdd1755d578
;projects[mailsystem][version]=4.x-dev
projects[mailsystem][revision]=84ffe7f7aa13ce86c36e76a4539d2b985038ba50
;projects[views_field_view][version]=1.x-dev
projects[views_field_view][revision]=454995e659e0f874966b4d76d2a7ec41e3856066

libraries[simplenews][destination]=modules
libraries[simplenews][directory_name]=simplenews
libraries[simplenews][download][type]=git
libraries[simplenews][download][url]=https://github.com/aritnath1990/simplenews.git
libraries[simplenews][overwrite]=True


libraries[pathauto][destination]=modules
libraries[pathauto][directory_name]=pathauto
libraries[pathauto][download][type]=git
libraries[pathauto][download][url]=https://github.com/md-systems/pathauto.git
libraries[pathauto][download][revision]=4514f994f45cec60e88af9cd8a18776004fbdce8
libraries[pathauto][overwrite]=True


libraries[media_entity_embeddable_video][destination]=modules
libraries[media_entity_embeddable_video][directory_name]=Media_entity_embeddable_video
libraries[media_entity_embeddable_video][download][type]=git
libraries[media_entity_embeddable_video][download][url]=https://github.com/drupal-media/media_entity_embeddable_video.git
libraries[media_entity_embeddable_video][download][revision]=e3730bb06f356b3ae63eae538753da047f950fcf
libraries[media_entity_embeddable_video][overwrite]=True

libraries[media_entity_audio][destination]=modules
libraries[media_entity_audio][directory_name]=media_entity_audio
libraries[media_entity_audio][download][type]=git
libraries[media_entity_audio][download][url]=https://github.com/shrimala/Media_entity_audio.git
libraries[media_entity_audio][overwrite]=True

libraries[media_pinkeye][destination]=modules
libraries[media_pinkeye][directory_name]=media_pinkeye
libraries[media_pinkeye][download][type]=git
libraries[media_pinkeye][download][url]=https://github.com/shrimala/media_pinkeye.git
libraries[media_pinkeye][overwrite]=True

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

libraries[drupal-swiftmailer][destination]=modules
libraries[drupal-swiftmailer][directory_name]=swiftmailer
libraries[drupal-swiftmailer][download][type]=git
libraries[drupal-swiftmailer][download][url]=https://github.com/webflo/drupal-swiftmailer.git
libraries[drupal-swiftmailer][download][branch]=8.x-1.x
libraries[drupal-swiftmailer][overwrite]=True

libraries[media_entity_instagram][destination]=modules
libraries[media_entity_instagram][directory_name]=Media_entity_instagram
libraries[media_entity_instagram][download][type]=git
libraries[media_entity_instagram][download][url]=https://github.com/aritnath1990/media_entity_instragram.git
libraries[media_entity_instagram][overwrite]=True

libraries[media_entity_slideshow][destination]=modules
libraries[media_entity_slideshow][directory_name]=Media_entity_slideshow
libraries[media_entity_slideshow][download][type]=git
libraries[media_entity_slideshow][download][url]=https://github.com/drupal-media/media_entity_slideshow.git
libraries[media_entity_slideshow][overwrite]=True

libraries[filefield_sources][destination]=modules
libraries[filefield_sources][directory_name]=filefield_sources
libraries[filefield_sources][download][type]=git
libraries[filefield_sources][download][url]=https://github.com/shrimala/filefield_sources.git
libraries[filefield_sources][download][branch]=master
libraries[filefield_sources][overwrite]=True

libraries[filefield_sources_flysystem][destination]=modules
libraries[filefield_sources_flysystem][directory_name]=filefield_sources_flysystem
libraries[filefield_sources_flysystem][download][type]=git
libraries[filefield_sources_flysystem][download][url]=https://github.com/shrimala/filefield_sources_flysystem.git
libraries[filefield_sources_flysystem][download][branch]=master
libraries[filefield_sources_flysystem][overwrite]=True

libraries[behatyml][destination]=vendor
libraries[behatyml][directory_name]=behatfiles
libraries[behatyml][download][type]=git
libraries[behatyml][download][url]=https://github.com/aritnath1990/behat.git
libraries[behatyml][download][branch]=master
libraries[behatyml][overwrite]=True
  
