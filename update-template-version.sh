#!/bin/sh

# Function to get the latest release tag from the template repository
get_template_release() {
    git ls-remote --tags template | grep -v '{}' | cut -d'/' -f3 | sort -V | tail -n1
}

# Check if template remote exists
if ! git remote | grep -q "^template$"; then
    echo "Adding template remote..."
    git remote add template https://github.com/matthacksteiner/cms.baukasten
fi

# Fetch all remotes including tags
echo "Fetching updates from template..."
git fetch --all --tags

# Check if we're in the middle of a merge
if [ -f .git/MERGE_HEAD ]; then
    echo "Detected an ongoing merge. Please complete the merge first:"
    echo "1. Resolve any conflicts"
    echo "2. git add . "
    echo "3. git commit"
    exit 1
fi

# Function to remove template-ignored files
remove_template_files() {
    echo "Removing template-specific files..."

    # Read template ignore list from .templateignore
    if [ -f ".templateignore" ]; then
        echo "Reading template files to remove from .templateignore..."

        while IFS= read -r line; do
            # Skip comments and empty lines
            if echo "$line" | grep -q '^[[:space:]]*#' || [ -z "$line" ]; then
                continue
            fi

            # Remove leading/trailing whitespace
            file=$(echo "$line" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')

            # Only remove files that exist and were added by the template merge
            if [ -f "$file" ] || [ -d "$file" ]; then
                # Check if this file exists in the template but not in our previous commit
                if git show template/main:"$file" >/dev/null 2>&1; then
                    if ! git show HEAD~1:"$file" >/dev/null 2>&1; then
                        if [ -f "$file" ]; then
                            rm -f "$file"
                            echo "  ✓ Removed template file: $file"
                        elif [ -d "$file" ]; then
                            rm -rf "$file"
                            echo "  ✓ Removed template directory: $file"
                        fi
                    else
                        echo "  ↳ Preserved existing file: $file"
                    fi
                fi
            fi
        done < .templateignore
    else
        echo "Warning: .templateignore not found, using default exclusions"
        # Fallback for backwards compatibility
        for file in "init-project.sh" "setup-baukasten-project.sh" "update-template-version.sh" \
                   ".github/workflows/update-cms-child-repos.yml" ".github/child-repositories.json" \
                   "README.template-tool.md" ".env" ".env.example" \
                   "herd.yml" ".templateignore"; do
            if [ -f "$file" ] || [ -d "$file" ]; then
                if git show template/main:"$file" >/dev/null 2>&1; then
                    if ! git show HEAD~1:"$file" >/dev/null 2>&1; then
                        rm -f "$file" 2>/dev/null || rm -rf "$file" 2>/dev/null || true
                        echo "  ✓ Removed template file: $file"
                    fi
                fi
            fi
        done
    fi
}

# Merge template changes
echo "Merging template changes..."
if ! git merge template/main --allow-unrelated-histories; then
    echo "Error: Merge failed. Please resolve conflicts manually."
    echo "After resolving conflicts:"
    echo "1. git add ."
    echo "2. git commit"
    echo "3. Run this script again"
    exit 1
fi

# Remove template-specific files that shouldn't be in child repositories
remove_template_files

# Stage the file removals
git add .

# Get the latest release version
TEMPLATE_VERSION=$(get_template_release)

if [ -z "$TEMPLATE_VERSION" ]; then
    echo "Warning: Could not determine template version"
    exit 0
fi

# Update README.md with the new version
echo "Updating README.md with template version ${TEMPLATE_VERSION}..."

# Create a temporary file
tmp_file=$(mktemp)

# Use awk with a more specific pattern matching
awk -v ver="$TEMPLATE_VERSION" '
    {
        # Only replace the first occurrence of **Template Release:** that is at the start of a line
        if ($0 ~ /^[[:space:]]*\*\*Template Release:\*\*/) {
            print "**Template Release:** " ver
        } else {
            print $0
        }
    }' README.md > "$tmp_file"

# Copy the temp file back to README.md
cp "$tmp_file" README.md
rm "$tmp_file"

# Add the README update to git
git add README.md

# Check if there are any changes to commit
if ! git diff --staged --quiet; then
    echo "Committing template update..."
    git commit -m "Update from template repository

- Merged latest changes from template
- Updated template version to ${TEMPLATE_VERSION}
- Removed template-specific files"
else
    echo "No changes to commit - template is already up to date"
fi

echo "Done! Template has been updated to version ${TEMPLATE_VERSION}"