#!/bin/bash

# Sync documentation to GitHub Wiki
# Usage: ./tools/sync-wiki.sh

set -e

echo "🔄 Syncing documentation to GitHub Wiki..."

# Generate fresh documentation
echo "📖 Generating documentation..."
composer docs:api

# Prepare wiki content
echo "🔧 Preparing wiki content..."
rm -rf wiki-content
mkdir -p wiki-content
cp -r docs/* wiki-content/

cd wiki-content

# Rename README.md to Home.md (Wiki homepage)
if [ -f "README.md" ]; then
    mv README.md Home.md
    echo "✅ Renamed README.md to Home.md"
fi

# Create custom sidebar
echo "📋 Creating custom sidebar..."
cat > "_Sidebar.md" << 'EOF'
## Getting Started
- **[Introduction](Introduction)**
- **[Installation](Introduction#install-the-sdk)**
- **[Authentication](Authentication)**

## Essentials
- **[Stores](Stores)**
- **[Authorization Models](Models)**
- **[Relationship Tuples](Tuples)**
- **[Permissions Queries](Queries)**
- **[Assertions](Assertions)**

## Features
- **[Helper Functions](Helpers)**
- **[Concurrency](Concurrency)**
- **[Exceptions](Exceptions)**
- **[Integration](Integration)**
- **[Observability](Observability)**
- **[Results](Results)**

## API Reference

### Core
- **[Client](API-Client)**
- **[Client Interface](API-ClientInterface)**

### Authentication
- **[Access Token](API-Authentication-AccessToken)**
- **[Client Credentials](API-Authentication-ClientCredentialAuthentication)**
- **[Authentication Interface](API-Authentication-AuthenticationInterface)**
- **[Access Token Interface](API-Authentication-AccessTokenInterface)**

### Models
- **[Authorization Model](API-Models-AuthorizationModel)**
- **[Store](API-Models-Store)**
- **[Tuple](API-Models-Tuple)**
- **[Tuple Key](API-Models-TupleKey)**
- **[User](API-Models-User)**
- **[Assertion](API-Models-Assertion)**
- **[Condition](API-Models-Condition)**
- **[Browse Collections →](API-Models)**

### Requests & Responses
- **[Requests →](API-Requests)**
- **[Responses →](API-Responses)**

### Schema & Validation
- **[Schema](API-Schema-Schema)**
- **[Schema Validator](API-Schema-SchemaValidator)**
- **[Browse Schema →](API-Schema)**

### Language Support
- **[DSL Transformer](API-Language-DslTransformer)**

### Results & Exceptions
- **[Results →](API-Results)**
- **[Exceptions →](API-Exceptions)**

---

*[View on GitHub Pages](https://evansims.github.io/openfga-php/) • [Source Code](https://github.com/evansims/openfga-php)*
EOF

# Create custom footer
echo "📋 Creating custom footer..."
cat > "_Footer.md" << 'EOF'
---

## Quick Navigation

**Essential Guides:** [Introduction](Introduction) • [Installation](Introduction#install-the-sdk) • [Authentication](Authentication) • [Stores](Stores) • [Models](Models) • [Tuples](Tuples) • [Queries](Queries)

**Advanced Features:** [Helper Functions](Helpers) • [Concurrency](Concurrency) • [Results](Results) • [Exceptions](Exceptions) • [Observability](Observability) • [Integration](Integration)

## Developer Resources

- **[📖 Complete API Reference](API-Client)** - Full class and method documentation
- **[🚀 Quick Start Examples](Introduction#quick-start)** - Get up and running in minutes
- **[🔧 Helper Functions](Introduction#helper-functions)** - Convenient shortcuts for common operations
- **[🧪 Testing Guide](Integration#testing)** - Unit testing with the SDK
- **[📊 Performance Guide](Concurrency)** - Optimize for high-scale applications

## Support & Community

- **[🐛 Report Issues](https://github.com/evansims/openfga-php/issues)** - Bug reports and feature requests
- **[💬 Discussions](https://github.com/evansims/openfga-php/discussions)** - Community support and questions
- **[📝 Contributing](https://github.com/evansims/openfga-php/blob/main/CONTRIBUTING.md)** - Help improve the SDK
- **[🔄 Changelog](https://github.com/evansims/openfga-php/blob/main/CHANGELOG.md)** - Latest updates and releases

## OpenFGA Ecosystem

- **[🌐 OpenFGA Documentation](https://openfga.dev/docs)** - Official OpenFGA documentation
- **[🎮 OpenFGA Playground](https://play.fga.dev)** - Interactive modeling environment
- **[📚 Authorization Concepts](https://openfga.dev/docs/concepts)** - Learn relationship-based access control
- **[🔗 Other SDKs](https://openfga.dev/docs/getting-started/setup-sdk)** - JavaScript, Go, Python, .NET, and more

---

*OpenFGA PHP SDK • [Apache 2.0 License](https://github.com/evansims/openfga-php/blob/main/LICENSE) • Version: `composer show evansims/openfga-php | grep 'versions'`*
EOF

# Convert directory structure to wiki-friendly format
echo "🔗 Converting content for Wiki format..."
find . -name "*.md" -type f -not -name "_Sidebar.md" -not -name "_Footer.md" | while read file; do
    # Skip the sidebar and footer files
    if [[ "$file" == "./_Sidebar.md" ]] || [[ "$file" == "./_Footer.md" ]]; then
        continue
    fi

    # Remove YAML front matter (everything between --- lines at the start)
    sed -i.bak '/^---$/,/^---$/d' "$file"

    # Remove the first H1 title (# Title) since Wiki generates its own title
    sed -i.bak '/^# /d' "$file"

    # Convert internal markdown links to wiki format
    # Handle links with anchors: file.md#anchor -> file#anchor
    sed -i.bak 's/\.md#/#/g' "$file"

    # Handle simple links: file.md -> file
    sed -i.bak 's/\.md)/)/g' "$file"

    # Convert relative paths to wiki links - flatten API paths
    sed -i.bak 's|\[\([^]]*\)\](API/\([^)]*\))|\[\1\](API-\2)|g' "$file"
    sed -i.bak 's|\[\([^]]*\)\](\([^)]*\)/\([^)]*\))|\[\1\](\2-\3)|g' "$file"

    # Remove backup files
    rm -f "$file.bak"
done

# Rename files to flatten API structure for Wiki
echo "📁 Flattening API directory structure..."
find API -name "*.md" -type f | while read file; do
    # Convert API/Path/File.md to API-Path-File.md
    newname=$(echo "$file" | sed 's|API/||' | sed 's|/|-|g')
    mv "$file" "API-$newname"
done

# Remove empty API directories
find API -type d -empty -delete 2>/dev/null || true
rmdir API 2>/dev/null || true

cd ..

# Check if wiki repository exists
WIKI_URL="https://github.com/evansims/openfga-php.wiki.git"
if [ -d "wiki-repo" ]; then
    echo "📁 Using existing wiki repository..."
    cd wiki-repo
    # Reset to clean state and fetch latest (but don't merge to avoid conflicts)
    git reset --hard HEAD 2>/dev/null || true
    git fetch origin 2>/dev/null || true
else
    echo "📥 Cloning wiki repository..."
    if git clone "$WIKI_URL" wiki-repo; then
        echo "✅ Wiki repository cloned successfully"
    else
        echo "❌ Failed to clone wiki repository."
        echo "This might mean:"
        echo "1. The wiki doesn't exist yet - create at least one page first:"
        echo "   https://github.com/evansims/openfga-php/wiki"
        echo "2. You need to authenticate with GitHub (run 'gh auth login')"
        echo "3. You don't have access to this repository"
        exit 1
    fi
    cd wiki-repo
fi

# Configure git
git config user.name "$(git config --global user.name)"
git config user.email "$(git config --global user.email)"

# Clear existing wiki content (except .git)
find . -type f -not -path "./.git/*" -delete 2>/dev/null || true

# Copy new content
cp -r ../wiki-content/* .

# Add and commit changes
git add .

if git diff --staged --quiet; then
    echo "ℹ️  No changes to Wiki"
else
    echo "💾 Committing changes..."
    git commit -m "Sync documentation from main branch

📖 Updated documentation
🤖 Generated automatically"

    # Push changes (force push to overwrite any upstream conflicts)
    # The generated documentation is the authoritative source, so we force push
    # to ensure our generated content takes precedence over any manual wiki edits
    echo "🚀 Pushing to Wiki..."
    git push --force origin master 2>/dev/null || git push --force origin main 2>/dev/null || git push --force -u origin master
    echo "✅ Wiki updated successfully!"
    echo "🌐 View at: https://github.com/evansims/openfga-php/wiki"
fi

# Cleanup
cd ..
rm -rf wiki-content

echo "✨ Wiki sync complete!"
