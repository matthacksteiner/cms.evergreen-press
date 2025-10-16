# Baukasten CMS Template System

This repository serves as a template for Kirby CMS projects using the Baukasten framework. It includes automated workflows to maintain and update child repositories that were created from this template.

## Template Features

### Automated Child Repository Updates

This template includes a GitHub Actions workflow that can automatically update child repositories with the latest changes from this template repository.

**Workflow: `.github/workflows/update-child-repos.yml`**

- **Trigger**: Manual dispatch (workflow_dispatch)
- **Purpose**: Update child CMS repositories with template changes
- **Features**:
  - Merge template changes into child repositories
  - Handle merge conflicts gracefully
  - Create pull requests for review
  - Remove template-specific files automatically
  - Support for dry runs

### Template File Management

**File: `.templateignore`**

Defines which files should be excluded when updating child repositories:

- Template maintenance workflows
- Deployment configurations (as child repos may have different hosting)
- Template-specific documentation
- Environment files

**Script: `init-project.sh`**

Initialization script for new projects created from this template:

- Removes template maintenance files
- Creates a basic `.env` file for the child repository
- Cleans up template-specific configurations

## Using This Template

### Creating a New Project

1. **Create a new repository** from this template
2. **Clone the new repository** to your local machine
3. **Run the initialization script**:
   ```bash
   bash init-project.sh
   ```
4. **Configure your environment**:
   - Update the `.env` file with your hosting details
   - Set up deployment workflows if needed
   - Configure Kirby CMS settings

### Maintaining the Template

#### Adding Child Repositories

Edit `.github/child-repositories.json` to include repositories that should receive template updates:

```json
{
	"repositories": [
		{
			"url": "https://github.com/your-org/your-cms-project.git",
			"description": "Your CMS project description"
		}
	]
}
```

#### Updating Child Repositories

1. **Navigate to Actions** in your GitHub repository
2. **Select "Update Child CMS Repositories"** workflow
3. **Click "Run workflow"** and configure options:
   - **Commit message**: Description of the update
   - **Branch name**: Branch to create in child repositories
   - **Create PR**: Whether to create pull requests
   - **Conflict strategy**: How to handle merge conflicts
   - **Dry run**: Test without making changes

#### Configuration Options

- **abort-on-conflicts**: Stop on merge conflicts
- **create-conflict-pr**: Create PRs even with conflicts for manual resolution
- **Dry run**: Preview changes without applying them
- **Delete temp branch**: Clean up temporary branches after successful merges

### Required Secrets

Configure these secrets in your repository settings:

- **PAT_TOKEN**: Personal Access Token with repository permissions

## Template Structure

### Kirby CMS Specific Files

The template includes:

- **Site configuration** in `site/config/`
- **Blueprints** for content structure in `site/blueprints/`
- **Templates** for rendering in `site/templates/`
- **Plugins** for extended functionality in `site/plugins/`
- **Content** examples in `content/`

### Deployment

The template includes a deployment workflow (`.github/workflows/deploy.yml`) configured for Uberspace hosting. Child repositories may need different deployment configurations.

### Best Practices

1. **Test template changes** in a development environment first
2. **Use dry runs** to preview updates before applying them
3. **Review pull requests** carefully when conflicts occur
4. **Document changes** in commit messages for clarity
5. **Keep child repositories** updated regularly to minimize conflicts

## Troubleshooting

### Common Issues

1. **Merge conflicts**: Use the conflict resolution workflow to create PRs for manual review
2. **Authentication errors**: Ensure PAT_TOKEN is configured with proper permissions
3. **Missing files**: Check that `.templateignore` includes all necessary exclusions

### Getting Help

- Check the [Kirby CMS documentation](https://getkirby.com/docs)
- Review the [Baukasten documentation](../docs/)
- Open an issue in this repository for template-specific questions

---

**Note**: This file is automatically removed when initializing a new project from this template.

# Baukasten CMS Template Tool Guide

This document explains how to maintain and update the Baukasten CMS template.

## Template Structure

The template includes several maintenance files:

- `setup-baukasten-project.sh` - Sets up new child repositories
- `init-project.sh` - Initializes child projects after template use
- `update-template-version.sh` - Updates template versions
- `.templateignore` - Files to exclude from child repositories
  **Project Level (child repositories)**:

- Languages are excluded from deployment (`deploy.yml` excludes `languages`)
- Each project maintains its own language configuration
- Projects can customize languages without affecting template

**Deployment Level**:

- GitHub Actions excludes `languages` folder during deployment
- Allows different staging/production language setups
- Prevents accidental overwriting of project-specific languages

## Child Repository Management
