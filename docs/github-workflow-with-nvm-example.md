# GitHub Workflow Example with NVM

This example shows how to use NVM in your GitHub Actions workflow for building frontend assets on
the Hetzner server. This is use for example with karin-gmeiner.at.

## Complete Workflow Example

```yaml
name: Deploy to Hetzner VPS

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Create target directory on server
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.HETZNER_HOST }}
          username: ${{ secrets.HETZNER_USER }}
          key: ${{ secrets.HETZNER_KEY }}
          script: |
            bash -c 'if [ ! -d "/var/www/${{ secrets.HETZNER_PATH }}" ]; then sudo /usr/local/bin/add-site ${{ secrets.HETZNER_PATH }} ${{ secrets.HETZNER_USER }}; fi'

      - name: Deploy files via rsync
        uses: Burnett01/rsync-deployments@6.0.0
        with:
          switches:
            -avzr --delete --exclude="content" --exclude="site/languages" --exclude="site/accounts"
            --exclude=".vscode" --exclude=".git" --exclude=".env" --exclude="node_modules"
            --exclude="public/media" --exclude="vendor" --exclude="kirby" --exclude="storage"
            --exclude=".DS_Store" --exclude="server-setup" --exclude="docs"
          remote_path: /var/www/${{ secrets.HETZNER_PATH }}/
          remote_host: ${{ secrets.HETZNER_HOST }}
          remote_user: ${{ secrets.HETZNER_USER }}
          remote_key: ${{ secrets.HETZNER_KEY }}

      - name: Create .env file
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.HETZNER_HOST }}
          username: ${{ secrets.HETZNER_USER }}
          key: ${{ secrets.HETZNER_KEY }}
          script: |
            cd /var/www/${{ secrets.HETZNER_PATH }}/
            bash -c 'cat > .env << EOF
            # Kirby CMS Environment Variables
            DEPLOY_URL=${{ secrets.DEPLOY_URL }}
            KIRBY_DEBUG=false
            KIRBY_CACHE=true
            EOF'

      - name: Install Composer dependencies
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.HETZNER_HOST }}
          username: ${{ secrets.HETZNER_USER }}
          key: ${{ secrets.HETZNER_KEY }}
          script: |
            cd /var/www/${{ secrets.HETZNER_PATH }}/
            composer install --no-interaction --prefer-dist --optimize-autoloader

      - name: Build frontend assets
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.HETZNER_HOST }}
          username: ${{ secrets.HETZNER_USER }}
          key: ${{ secrets.HETZNER_KEY }}
          script: |
            cd /var/www/${{ secrets.HETZNER_PATH }}/

            # Load NVM in non-interactive shell
            export NVM_DIR="$HOME/.nvm"
            if [ -s "$NVM_DIR/nvm.sh" ]; then
              \. "$NVM_DIR/nvm.sh"
            else
              echo "❌ NVM not found!"
              exit 1
            fi

            # Check for .nvmrc and use specified version
            if [ -f .nvmrc ]; then
              echo "📦 Found .nvmrc, installing/using specified Node version..."
              nvm install $(cat .nvmrc)
              nvm use $(cat .nvmrc)
            else
              echo "📦 No .nvmrc found, using default Node version..."
              nvm use default
            fi

            # Verify Node and npm are available
            echo "✅ Node version: $(node --version)"
            echo "✅ NPM version: $(npm --version)"

            # Install dependencies and build
            echo "📥 Installing npm dependencies..."
            npm ci

            echo "🔨 Building frontend assets..."
            npm run build

            echo "✅ Frontend build complete!"
```

## Key Changes from Original Workflow

### Original "Build frontend assets" step:

```yaml
- name: Build frontend assets
  uses: appleboy/ssh-action@v1.0.3
  with:
    host: ${{ secrets.HETZNER_HOST }}
    username: ${{ secrets.HETZNER_USER }}
    key: ${{ secrets.HETZNER_KEY }}
    script: |
      cd /var/www/${{ secrets.HETZNER_PATH }}/
      npm ci
      npm run build
```

### New "Build frontend assets" step with NVM:

```yaml
- name: Build frontend assets
  uses: appleboy/ssh-action@v1.0.3
  with:
    host: ${{ secrets.HETZNER_HOST }}
    username: ${{ secrets.HETZNER_USER }}
    key: ${{ secrets.HETZNER_KEY }}
    script: |
      cd /var/www/${{ secrets.HETZNER_PATH }}/

      # Load NVM
      export NVM_DIR="$HOME/.nvm"
      [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

      # Use Node version from .nvmrc if it exists
      if [ -f .nvmrc ]; then
        nvm install
        nvm use
      fi

      npm ci
      npm run build
```

## What Was Added

1. **NVM Loading**: Exports `NVM_DIR` and sources the NVM script
2. **.nvmrc Support**: Automatically installs and uses the Node version specified in `.nvmrc`
3. **Error Handling**: Checks if NVM exists before proceeding
4. **Version Logging**: Shows which Node/NPM versions are being used (helpful for debugging)

## Testing the Workflow

After updating your workflow file, push to the `main` branch and monitor the GitHub Actions logs.
You should see:

```
✅ Node version: v24.11.0
✅ NPM version: 11.6.1
📥 Installing npm dependencies...
🔨 Building frontend assets...
✅ Frontend build complete!
```

## Troubleshooting

### Error: "protocol version mismatch -- is your shell clean?"

**Problem**: rsync fails with protocol incompatibility error during deployment

**Cause**: The kirbyuser's fish shell config is outputting text during non-interactive SSH
connections (like rsync uses)

**Solution**: Ensure all output commands in `~/.config/fish/config.fish` are wrapped in interactive
checks:

```fish
if status is-interactive
    # Commands to run in interactive sessions can go here

    # Load NVM only in interactive sessions
    nvm use lts
end

# Composer bin path (this is OK - doesn't output)
set -gx PATH ~/.config/composer/vendor/bin $PATH
```

**Verify the fix**:

```bash
# This should output ONLY "test" with no other messages
ssh hetzner-kirby 'echo "test"'
```

**Commands that cause issues if not wrapped**:

- ❌ `nvm use lts` - outputs "Now using Node v24.11.0..."
- ❌ `echo "Welcome!"` - any echo statements
- ❌ Custom prompts or greeting messages
- ✅ `set -gx PATH` - environment variables are OK (no output)

### Error: "NVM not found!"

- Verify NVM is installed: `ssh hetzner-kirby 'bash -c "[ -d ~/.nvm ] && echo OK || echo MISSING"'`
- Reinstall NVM if needed (see server documentation)

### Error: "Node version not found"

- The Node version in `.nvmrc` doesn't exist
- NVM will automatically download it on first use
- Check your `.nvmrc` file format (should be just the version number, e.g., `20.10.0` or `24.11.0`)

### Builds are slow

- First deployment after NVM installation will download Node.js
- Subsequent deployments reuse the cached version
- Consider adding a step to cache Node.js versions if builds are frequent
