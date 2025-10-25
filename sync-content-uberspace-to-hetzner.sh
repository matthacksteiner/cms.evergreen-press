#!/bin/bash
# Sync Kirby CMS content from Uberspace to Hetzner
# This script downloads content from Uberspace to a temporary folder,
# then uploads it to the Hetzner server.

set -e  # Exit on any error

# Configuration
UBERSPACE_SSH="uberspace"  # SSH config name for Uberspace
UBERSPACE_PATH="html/cms.kaufmannklub.at/content"
HETZNER_SSH="hetzner-root"  # SSH config name for Hetzner
HETZNER_PATH="/var/www/cms.kaufmannklub/content"
TEMP_DIR="$(mktemp -d)/kirby-content-sync"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Kirby CMS Content Sync: Uberspace → Hetzner ===${NC}"
echo ""

# Create temporary directory
echo -e "${YELLOW}Creating temporary directory...${NC}"
mkdir -p "$TEMP_DIR"
echo "Temp directory: $TEMP_DIR"
echo ""

# Step 1: Download content from Uberspace
echo -e "${YELLOW}Step 1/3: Downloading content from Uberspace...${NC}"
echo "Source: $UBERSPACE_SSH:~/${UBERSPACE_PATH}/"
echo "Target: $TEMP_DIR/"
echo ""

rsync -avz --progress \
  "${UBERSPACE_SSH}:~/${UBERSPACE_PATH}/" \
  "$TEMP_DIR/"

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Download from Uberspace completed successfully${NC}"
else
  echo -e "${RED}✗ Error downloading from Uberspace${NC}"
  rm -rf "$TEMP_DIR"
  exit 1
fi
echo ""

# Step 2: Display sync summary
echo -e "${YELLOW}Step 2/3: Analyzing synced content...${NC}"
FILE_COUNT=$(find "$TEMP_DIR" -type f | wc -l | tr -d ' ')
DIR_COUNT=$(find "$TEMP_DIR" -type d | wc -l | tr -d ' ')
TOTAL_SIZE=$(du -sh "$TEMP_DIR" | cut -f1)

echo "Files: $FILE_COUNT"
echo "Directories: $DIR_COUNT"
echo "Total size: $TOTAL_SIZE"
echo ""

# Step 3: Upload to Hetzner
echo -e "${YELLOW}Step 3/3: Uploading content to Hetzner...${NC}"
echo "Source: $TEMP_DIR/"
echo "Target: $HETZNER_SSH:$HETZNER_PATH/"
echo ""

# Confirm before uploading
read -p "Continue with upload to Hetzner? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
  echo -e "${YELLOW}Upload cancelled by user${NC}"
  rm -rf "$TEMP_DIR"
  exit 0
fi

rsync -avz --progress \
  --delete \
  "$TEMP_DIR/" \
  "$HETZNER_SSH:$HETZNER_PATH/"

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Upload to Hetzner completed successfully${NC}"
else
  echo -e "${RED}✗ Error uploading to Hetzner${NC}"
  rm -rf "$TEMP_DIR"
  exit 1
fi
echo ""

# Step 4: Fix permissions on Hetzner
echo -e "${YELLOW}Fixing permissions on Hetzner...${NC}"
ssh "$HETZNER_SSH" "sudo fix-kirby-permissions cms.kaufmannklub"

if [ $? -eq 0 ]; then
  echo -e "${GREEN}✓ Permissions fixed successfully${NC}"
else
  echo -e "${YELLOW}⚠ Warning: Could not fix permissions automatically${NC}"
  echo "Run manually: ssh hetzner-root 'sudo fix-kirby-permissions cms.kaufmannklub'"
fi
echo ""

# Cleanup
echo -e "${YELLOW}Cleaning up temporary files...${NC}"
rm -rf "$TEMP_DIR"
echo -e "${GREEN}✓ Cleanup completed${NC}"
echo ""

# Summary
echo -e "${GREEN}=== Sync Complete ===${NC}"
echo "Content has been successfully synced from Uberspace to Hetzner"
echo ""
echo "Next steps:"
echo "1. Verify content in Kirby Panel: https://cms.kaufmannklub.com/panel"
echo "2. Check file permissions if needed"
echo "3. Clear Kirby cache if necessary"
echo ""

