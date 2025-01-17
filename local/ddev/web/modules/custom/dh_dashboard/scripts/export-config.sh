#!/bin/bash

# Export full config
drush config-export -y

# Create config/install directory if it doesn't exist
mkdir -p ../config/install

# Copy relevant config files
cd ../config/sync
for f in dh_dashboard.*.yml block.block.dh_dashboard_*.yml; do
  if [ -f "$f" ]; then
    # Remove UUID and _core from the files
    sed '/^uuid:/d; /_core:/,+1d' "$f" > "../config/install/$f"
    echo "Exported $f"
  fi
done

# Update info.yml with exported configs
echo "config:" > ../dh_dashboard.info.yml.tmp
echo "  install:" >> ../dh_dashboard.info.yml.tmp
for f in ../config/install/*.yml; do
    echo "    - $(basename "$f" .yml)" >> ../dh_dashboard.info.yml.tmp
done

# Merge with existing info.yml
sed '/^config:/,/^[a-z]/{ /^[a-z]/!d; }' ../dh_dashboard.info.yml > ../dh_dashboard.info.yml.new
cat ../dh_dashboard.info.yml.tmp >> ../dh_dashboard.info.yml.new
mv ../dh_dashboard.info.yml.new ../dh_dashboard.info.yml
rm ../dh_dashboard.info.yml.tmp

echo "Configuration export complete"
