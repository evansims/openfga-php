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

# Convert directory structure to wiki-friendly format
echo "🔗 Converting links for Wiki format..."
find . -name "*.md" -type f | while read file; do
    # Remove .md extension from internal links
    sed -i.bak 's/\.md)/)/g' "$file"
    
    # Convert relative paths to wiki links
    sed -i.bak 's|\[\([^]]*\)\](\([^)]*\)/\([^)]*\))|\[\1\](\3)|g' "$file"
    
    # Remove backup files
    rm -f "$file.bak"
done

cd ..

# Check if wiki repository exists
WIKI_URL="git@github.com:evansims/openfga-php.wiki.git"
if [ -d "wiki-repo" ]; then
    echo "📁 Using existing wiki repository..."
    cd wiki-repo
    git pull origin master 2>/dev/null || git pull origin main 2>/dev/null || true
else
    echo "📥 Cloning wiki repository..."
    if git clone "$WIKI_URL" wiki-repo 2>/dev/null; then
        echo "✅ Wiki repository cloned successfully"
    else
        echo "❌ Wiki repository doesn't exist yet."
        echo "Please create at least one page in your GitHub Wiki first:"
        echo "https://github.com/evansims/openfga-php/wiki"
        echo "Then run this script again."
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