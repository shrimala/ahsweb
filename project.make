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
  
