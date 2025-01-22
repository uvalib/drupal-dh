#!/bin/bash -x

echo "Starting DH Dashboard reinstallation process..."
./scripts/reinstall-dashboard.sh

echo "Starting DH Certificate reinstallation process..."
./web/modules/custom/dh_certificate/scripts/reinstall-certificate.sh