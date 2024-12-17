#!/bin/bash

echo "Uninstalling dh_certificate..."
ddev drush pmu dh_certificate -y

echo "Removing field and entity configurations..."

# Field Storage configurations
ddev drush config:delete field.storage.paragraph.field_course_name
ddev drush config:delete field.storage.paragraph.field_course_number
ddev drush config:delete field.storage.paragraph.field_semester_taken
ddev drush config:delete field.storage.paragraph.field_completed
ddev drush config:delete field.storage.paragraph.field_requirement_name
ddev drush config:delete field.storage.user.field_dh_requirements

# Field Instance configurations
ddev drush config:delete field.field.paragraph.course_requirement.field_completed
ddev drush config:delete field.field.paragraph.course_requirement.field_course_name
ddev drush config:delete field.field.paragraph.course_requirement.field_course_number
ddev drush config:delete field.field.paragraph.course_requirement.field_semester_taken
ddev drush config:delete field.field.paragraph.general_requirement.field_completed
ddev drush config:delete field.field.paragraph.general_requirement.field_requirement_name
ddev drush config:delete field.field.user.user.field_dh_requirements

# Entity configurations
ddev drush config:delete paragraphs.paragraphs_type.course_requirement
ddev drush config:delete paragraphs.paragraphs_type.general_requirement

# Form and View display configurations
ddev drush config:delete core.entity_form_display.paragraph.course_requirement.default
ddev drush config:delete core.entity_form_display.paragraph.general_requirement.default
ddev drush config:delete core.entity_view_display.paragraph.course_requirement.default
ddev drush config:delete core.entity_view_display.paragraph.general_requirement.default

echo "Clearing caches..."
ddev drush cr

echo "Reinstalling dh_certificate..."
ddev drush en dh_certificate -y

echo "Checking installation..."
echo "Recent log messages:"
ddev drush watchdog:show | grep dh_certificate

echo "Field information:"
ddev drush field:info | grep dh_certificate

echo "Paragraph entities:"
ddev drush entity:query paragraph
