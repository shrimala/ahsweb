api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.0.x

;******************************
;    Other Modules
;******************************
projects[panels][version]=3.0-alpha12
projects[layout_plugin][version]=1.0-alpha12
projects[page_manager][version]=1.0-alpha12
projects[eck][version]=1.x-dev
projects[paragraphs][version]=1.x-dev
projects[token][version]=1.x-dev
projects[eform][version]=1.x-dev
projects[inmail][version]=1.x-dev
projects[mailmute][version]=1.x-dev
projects[media_entity][version]=1.x-dev
projects[advanced_help][version]=1.x-dev
projects[entity_reference_revisions][version]=1.0-rc1
projects[composer_manager][version]=1.0-rc1
projects[flysystem][version]=1.x-dev
projects[flysystem_dropbox][version]=1.x-dev
projects[flysystem_s3][version]=1.x-dev
projects[mailsystem][version]=4.x-dev
projects[views_field_view][version]=1.x-dev


libraries[simplenews][destination]=modules
libraries[simplenews][directory_name]=simplenews
libraries[simplenews][download][type]=git
libraries[simplenews][download][url]=https://github.com/aritnath1990/simplenews.git
libraries[simplenews][overwrite]=True


libraries[pathauto][destination]=modules
libraries[pathauto][directory_name]=pathauto
libraries[pathauto][download][type]=git
libraries[pathauto][download][url]=https://github.com/md-systems/pathauto.git
libraries[pathauto][download][branch]=8.x-1.x
libraries[pathauto][overwrite]=True


libraries[media_entity_embeddable_video][destination]=modules
libraries[media_entity_embeddable_video][directory_name]=Media_entity_embeddable_video
libraries[media_entity_embeddable_video][download][type]=git
libraries[media_entity_embeddable_video][download][url]=https://github.com/drupal-media/media_entity_embeddable_video.git
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

