#!/bin/bash

echo "Uninstalling dh_certificate..."
ddev drush pmu dh_certificate -y

echo "Removing field and entity configurations..."

# Field Storage configurations
ddev drush config:delete field.storage.paragraph.field_course_name -y
ddev drush config:delete field.storage.paragraph.field_course_number -y
ddev drush config:delete field.storage.paragraph.field_semester_taken -y
ddev drush config:delete field.storage.paragraph.field_completed -y
ddev drush config:delete field.storage.paragraph.field_requirement_name -y
ddev drush config:delete field.storage.user.field_dh_requirements -y

# Field Instance configurations
ddev drush config:delete field.field.paragraph.course_requirement.field_completed -y
ddev drush config:delete field.field.paragraph.course_requirement.field_course_name -y
ddev drush config:delete field.field.paragraph.course_requirement.field_course_number -y
ddev drush config:delete field.field.paragraph.course_requirement.field_semester_taken -y
ddev drush config:delete field.field.paragraph.general_requirement.field_completed -y
ddev drush config:delete field.field.paragraph.general_requirement.field_requirement_name -y
ddev drush config:delete field.field.user.user.field_dh_requirements -y

# Entity configurations
ddev drush config:delete paragraphs.paragraphs_type.course_requirement -y
ddev drush config:delete paragraphs.paragraphs_type.general_requirement -y

# Form and View display configurations
ddev drush config:delete core.entity_form_display.paragraph.course_requirement.default -y
ddev drush config:delete core.entity_form_display.paragraph.general_requirement.default -y
ddev drush config:delete core.entity_view_display.paragraph.course_requirement.default -y
ddev drush config:delete core.entity_view_display.paragraph.general_requirement.default -y

echo "Clearing caches..."
ddev drush cr

echo "Reinstalling dh_certificate..."
ddev drush en dh_certificate -y

echo "Checking installation..."
echo "Recent log messages:"
ddev drush watchdog:show --type=dh_certificate --format=table

echo "Checking field configurations..."
ddev drush config:status --format=table | grep dh_certificate

echo "Checking paragraph types..."
ddev drush config:get paragraphs.paragraphs_type.course_requirement --format=yaml
ddev drush config:get paragraphs.paragraphs_type.general_requirement --format=yaml

echo "Checking user field..."
ddev drush config:get field.field.user.user.field_dh_requirements --format=yaml

echo "Installation complete. Please check the output above for any errors."
