#!/bin/bash

ddev drush cr

# Remove Layout Builder from content types
ddev drush sql-query "UPDATE config SET data = REPLACE(data, 's:14:\"layout_builder\";', '') WHERE name LIKE 'core.entity_view_display%'" || true

# Remove Layout Builder section field from nodes
ddev drush sql-query "DELETE FROM node__layout_builder__layout" || true
ddev drush sql-query "DELETE FROM node_revision__layout_builder__layout" || true

# Clear entity and field caches
ddev drush ev '\Drupal::entityTypeManager()->clearCachedDefinitions();'
ddev drush ev '\Drupal::service("entity_field.manager")->clearCachedFieldDefinitions();'
ddev drush cr

# Uninstall module first to handle dependencies
ddev drush pmu dh_dashboard layout_builder layout_discovery -y || true

# Clean up core Layout Builder configurations
ddev drush config:delete core.entity_form_display.node.page.default || true
ddev drush config:delete layout_builder.override.node.page.default || true
ddev drush sql-query "DELETE FROM key_value WHERE collection='entity.storage_schema.sql' AND name LIKE '%layout_builder%'" || true

# Delete specific configurations
ddev drush config:delete node.type.dashboard || true
ddev drush config:delete node.type.program || true
ddev drush config:delete block_content.type.dh_dashboard_news_feed || true
ddev drush config:delete block_content.type.dh_certificate_progress || true
ddev drush config:delete rules.component.certificate_progress_rule || true
ddev drush config:delete layout_builder.layout.dashboard || true
ddev drush config:delete layout_builder.layout.dashboard_threecol || true

# Delete all block configurations
ddev drush config:delete block.block.dh_news_feed || true
ddev drush config:delete block.block.dh_program_info || true
ddev drush config:delete block.block.certificate_progress || true
ddev drush config:delete block.block.dh_certificate_progress_compact || true
ddev drush config:delete block.block.dh_dashboard_certificate_progress || true
ddev drush config:delete block.block.dh_dashboard_news_feed || true

ddev drush cr

# Install prerequisites 
ddev drush en typed_data rules hal layout_discovery layout_builder serialization -y

# Install module
ddev drush en dh_dashboard -y

# Final cache rebuild
ddev drush cr
