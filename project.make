api = 2
core = 8.x

; Drupal core.
projects[drupal][type] = core
projects[drupal][version] = 8.1.0

;Drupal core patches recommended for Platform.sh at install time
;projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-redirect_to_install-728702-85.patch
projects[drupal][patch][] = https://www.drupal.org/files/issues/drupal-2607352-6.patch
