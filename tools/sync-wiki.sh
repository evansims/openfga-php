#!/bin/bash

# Sync documentation to GitHub Wiki
# Usage: ./tools/sync-wiki.sh

set -e

echo "🔄 Syncing documentation to GitHub Wiki..."

# Generate fresh documentation
echo "📖 Generating documentation..."
composer docs

# Prepare wiki content
echo "🔧 Preparing wiki content..."
rm -rf wiki-content
mkdir -p wiki-content
cp -r docs/* wiki-content/

cd wiki-content

# Rename index.md to Home.md (Wiki homepage)
if [ -f "index.md" ]; then
    mv index.md Home.md
    echo "✅ Renamed index.md to Home.md"
fi

# Create custom sidebar
echo "📋 Creating custom sidebar..."
cat > "_Sidebar.md" << 'EOF'
## 📚 Guides

- **[Home](Home)**
- **[Getting Started](GettingStarted)**
- **[Authentication](Authentication)**
- **[Stores](Stores)**
- **[Authorization Models](AuthorizationModels)**
- **[Queries](Queries)**
- **[Relationship Tuples](RelationshipTuples)**
- **[Assertions](Assertions)**
- **[Results](Results)**

---

## 🔧 API Reference

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

# Convert directory structure to wiki-friendly format
echo "🔗 Converting content for Wiki format..."
find . -name "*.md" -type f -not -name "_Sidebar.md" | while read file; do
    # Skip the sidebar file
    if [[ "$file" == "./_Sidebar.md" ]]; then
        continue
    fi
    
    # Remove YAML front matter (everything between --- lines at the start)
    sed -i.bak '/^---$/,/^---$/d' "$file"
    
    # Remove the first H1 title (# Title) since Wiki generates its own title
    sed -i.bak '/^# /d' "$file"
    
    # Remove .md extension from internal links
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
    git pull origin master 2>/dev/null || git pull origin main 2>/dev/null || true
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
    
    # Push changes
    echo "🚀 Pushing to Wiki..."
    git push origin master 2>/dev/null || git push origin main 2>/dev/null || git push -u origin master
    echo "✅ Wiki updated successfully!"
    echo "🌐 View at: https://github.com/evansims/openfga-php/wiki"
fi

# Cleanup
cd ..
rm -rf wiki-content

echo "✨ Wiki sync complete!"