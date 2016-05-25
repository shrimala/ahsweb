api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.0.5
projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-redirect_to_install-728702-85.patch
projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-2607352-6.patch
; Drush make allows a default sub directory for all contributed projects.
defaults[projects][subdir] = contrib

; Platform indicator module.
projects[platform][version] = 1.3
