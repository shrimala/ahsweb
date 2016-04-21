api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.1.0

;Drupal core patches recommended for Platform.sh at install time
;projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-redirect_to_install-728702-85.patch
projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-2607352-6.patch


;******************************
;    Other Modules
;******************************

projects[layout_plugin][version]=1.0-alpha22
projects[ds][version]=2.3
projects[entity_reference_revisions][version]=1.0-rc6
projects[paragraphs][revision]=	990082ae9d2a83f3b80f241f903a41438d648e97

projects[inmail][revision]=	e2a8975de4bb3a9348866e87d8395540d6296eb2
projects[mailmute][revision]=afd07079589bb5c24970b0657ce02be4bd86f53e
projects[mailsystem][version]=4.0-beta1
projects[swiftmailer][revision]=39e456317cccefaeac8e09a7340ae0a2430e5dda	

projects[media_entity][revision]=0e8c01ccdb5652a2912618013c0222ce409f033f
projects[video_embed_field][version]=1.0-rc7
;projects[media_entity_image][revision]=717acca8a2cc7ba6ab76f8a30bc7aa247f542255	

projects[composer_manager][revision]=247d558abf22803075c9c1043ef7b7d4194b3d04
projects[flysystem][version]=1.0-alpha2
projects[flysystem_dropbox][revision]=8b9b4b0b69aff47c8afd395de5226cfb4f640860
projects[filefield_paths][revision]=410c6b998e20e0e245f3b64ce758049cc722636e
	
libraries[simplenews][destination]=modules
libraries[simplenews][directory_name]=simplenews
libraries[simplenews][download][type]=git
libraries[simplenews][download][url]=https://github.com/aritnath1990/simplenews.git
libraries[simplenews][overwrite]=True

libraries[media_entity_audio][destination]=modules
libraries[media_entity_audio][directory_name]=media_entity_audio
libraries[media_entity_audio][download][type]=git
libraries[media_entity_audio][download][url]=https://github.com/shrimala/Media_entity_audio.git
libraries[media_entity_audio][overwrite]=True

libraries[filefield_sources][destination]=modules
libraries[filefield_sources][directory_name]=filefield_sources
libraries[filefield_sources][download][type]=git
libraries[filefield_sources][download][url]=https://github.com/tienvx/filefield_sources.git
libraries[filefield_sources][download][branch]=8.x-1.x
libraries[filefield_sources][overwrite]=True

libraries[filefield_sources_flysystem][destination]=modules
libraries[filefield_sources_flysystem][directory_name]=filefield_sources_flysystem
libraries[filefield_sources_flysystem][download][type]=git
libraries[filefield_sources_flysystem][download][url]=https://github.com/shrimala/filefield_sources_flysystem.git
libraries[filefield_sources_flysystem][download][branch]=master
libraries[filefield_sources_flysystem][overwrite]=True


libraries[media_entity_embeddable_video][destination]=modules
libraries[media_entity_embeddable_video][directory_name]=Media_entity_embeddable_video
libraries[media_entity_embeddable_video][download][type]=git
libraries[media_entity_embeddable_video][download][url]=https://github.com/drupal-media/media_entity_embeddable_video.git
libraries[media_entity_embeddable_video][download][revision]=e3730bb06f356b3ae63eae538753da047f950fcf
libraries[media_entity_embeddable_video][overwrite]=True
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
libraries[behatyml][destination]=vendor
libraries[behatyml][directory_name]=behatfiles
libraries[behatyml][download][type]=git
libraries[behatyml][download][url]=https://github.com/aritnath1990/behat.git
libraries[behatyml][download][branch]=master
libraries[behatyml][overwrite]=True
projects[flysystem_s3][revision]=40570814e6ceefd0a500380ec642acdd1755d578
