api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.1.2

;******************************
;    Other Modules
;******************************

projects[migrate_tools][version]=2.x-dev
projects[migrate_plus][version]=2.x-dev
projects[video_embed_field][version]=1.x-dev
projects[config_inspector][version]=1.x-dev
projects[devel][version]=1.x-dev
projects[entity_reference_revisions][version]=1.x-dev
projects[filefield_paths][version]=1.x-dev
projects[flysystem][version]=1.x-dev
projects[flysystem_dropbox][version]=1.x-dev
projects[media_entity_audio][version]=1.x-dev
projects[paragraphs][version]=1.x-dev
projects[filefield_sources][version]=1.x-dev
projects[drupal][patch][]=https://www.drupal.org/files/issues/empty-2717483-12.patch
projects[drupal][patch][]=https://www.drupal.org/files/issues/filefield_paths-ignore_base_fields-2662420-5.patch
projects[drupal][patch][]=https://www.drupal.org/files/issues/2760983.patch
  


libraries[migrate_nd][destination]=modules
libraries[migrate_nd][directory_name]=migrate_nd
libraries[migrate_nd][download][type]=git
libraries[migrate_nd][download][url]=https://github.com/aritnath1990/media_migration.git
libraries[migrate_nd][overwrite]=True


