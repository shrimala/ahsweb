api = 2
core = 8.0

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.0.1

;******************************
;    Other Modules
;******************************

projects[ds][revision]=c813fbd2075588965fb5b6eb944b0bf3edb2641e
projects[layout_plugin][revision]=993c829961b5b304a0094d7cd33f014edccf859e
;projects[paragraphs][version]=1.x-dev
projects[paragraphs][revision]=740874bacb56a6a90683e7818c06ba77a638125c
;projects[inmail][version]=1.x-dev
projects[inmail][revision]=19bde15111d08cdc9b69c2474ef9e14e758891c8
;projects[mailmute][version]=1.x-dev
projects[mailmute][revision]=f340adaded7905999acd0eca6b31484b7ac5db51
;projects[media_entity][version]=1.x-dev
;projects[media_entity][revision]=27c402b0f5d26066e0f10c91e77ee8b0df6dd101
projects[media_entity][revision]=522ba3184464c10a7e7ab077d86a789737af34e6
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

libraries[simplenews][destination]=modules
libraries[simplenews][directory_name]=simplenews
libraries[simplenews][download][type]=git
libraries[simplenews][download][url]=https://github.com/aritnath1990/simplenews.git
libraries[simplenews][overwrite]=True

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
;libraries[media_entity_image][download][revision]=0b06c2f6c62e6518d39fd71798f82f4ccd3b52d8
libraries[media_entity_image][download][revision]=d632fc8388109411bc54897830d74de32128d68b
libraries[media_entity_image][overwrite]=True

libraries[media_entity_twitter][destination]=modules
libraries[media_entity_twitter][directory_name]=Media_entity_twitter
libraries[media_entity_twitter][download][type]=git
libraries[media_entity_twitter][download][url]=https://github.com/drupal-media/media_entity_twitter.git
;libraries[media_entity_twitter][download][revision]=fe694730dd25065138865ca6320c5748313886fe
libraries[media_entity_twitter][download][revision]=2bba2e60e4fdbaa023a0c7b92aeb8f70c76e8468
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
libraries[media_entity_slideshow][download][revision]=bc00e14df038137b7b63f5bb79a46b8183ec3b39
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
  
