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

# Copy main README.md
cp docs/README.md wiki-content/

# Copy API documentation (preserving structure)
cp -r docs/API wiki-content/

# Flatten written guides into wiki-content root
find docs -name "*.md" -not -path "docs/README.md" -not -path "docs/API/*" | while read file; do
    filename=$(basename "$file")
    cp "$file" "wiki-content/$filename"
done

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
- **[Installation](Installation)**
- **[Authentication](Authentication)**

## Essentials
- **[Authorization Stores](Stores)**
- **[Authorization Models](Models)**
- **[Relationship Tuples](Tuples)**
- **[Permission Queries](Queries)**
- **[Testing with Assertions](Assertions)**

## Features
- **[Helper Functions](Helpers)**
- **[Concurrency](Concurrency)**
- **[Error Handling](Exceptions)**
- **[Framework Integration](Integration)**
- **[OpenTelemetry Observability](Observability)**
- **[Result Pattern](Results)**

## API Reference

### Core

- **[SDK Client](API-Client)**
- **[Helper Functions](API-Helpers)**
- **[Results Pattern](API-Results-README)**
- **[DSL Transformer](API-Language-DslTransformer)**

### i18n
- **[Languages](API-Language)**
- **[Messages](API-Messages)**

### Authentication
- **[Access Token](API-Authentication-AccessToken)**
- **[Client Credentials](API-Authentication-ClientCredentialAuthentication)**
- **[More Authentication …](API-Authentication-README)**

### Events
- **[Event Dispatcher](API-Events-EventDispatcher)**
- **[HTTP Request Sent Event](API-Events-HttpRequestSentEvent)**
- **[HTTP Response Received Event](API-Events-HttpResponseReceivedEvent)**
- **[Operation Started Event](API-Events-OperationStartedEvent)**
- **[Operation Completed Event](API-Events-OperationCompletedEvent)**
- **[More Events …](API-Events-README)**

### Exceptions
- **[Authentication Exception](API-Exceptions-AuthenticationException)**
- **[Client Exception](API-Exceptions-ClientException)**
- **[Configuration Exception](API-Exceptions-ConfigurationException)**
- **[Network Exception](API-Exceptions-NetworkException)**
- **[Serialization Exception](API-Exceptions-SerializationException)**
- **[More Exceptions …](API-Exceptions-README)**

### Integration
- **[Service Provider](API-Integration-ServiceProvider)**

### Models
- **[Authorization Model](API-Models-AuthorizationModel)**
- **[Store](API-Models-Store)**
- **[Tuple](API-Models-Tuple)**
- **[Tuple Key](API-Models-TupleKey)**
- **[User](API-Models-User)**
- **[Assertion](API-Models-Assertion)**
- **[Condition](API-Models-Condition)**
- **[More Models …](API-Models-README)**

### Networking & Concurrency
- **[Request Manager](API-Network-RequestManager)**
- **[Request Context](API-Network-RequestContext)**
- **[Circuit Breaker](API-Network-CircuitBreaker)**
- **[Parallel Task Executor](API-Network-ParallelTaskExecutor)**
- **[Fiber Concurrent Executor](API-Network-FiberConcurrentExecutor)**
- **[Simple Concurrent Executor](API-Network-SimpleConcurrentExecutor)**
- **[Retry Handler](API-Network-RetryHandler)**
- **[Exponential Backoff Retry Strategy](API-Network-ExponetialBackoffRetryStrategy)**
- **[More Networking …](API-Network-README)**

### Observability
- **[Telemetry Factory](API-Observability-TelemetryFactory)**
- **[OpenTelemetry Provider](API-Observability-OpenTelemetryProvider)**

### Requests & Responses
- **[Requests →](API-Requests-README)**
- **[Responses →](API-Responses-README)**

### Schema & Validation
- **[Schema](API-Schema-Schema)**
- **[Schema Validator](API-Schema-SchemaValidator)**
- **[More Schema …](API-Schema)**

---

*[View on GitHub Pages](https://evansims.github.io/openfga-php/) • [Source Code](https://github.com/evansims/openfga-php)*
EOF

# Create custom footer
echo "📋 Creating custom footer..."
cat > "_Footer.md" << 'EOF'
---

**Getting Started:** [Introduction](Introduction) • [Installation](Installation) • [Authentication](Authentication)

**Essentials:** [Stores](Stores) • [Authorization Models](Models) • [Relationship Tuples](Tuples) • [Permissions Queries](Queries)

**Features:** [Helper Functions](Helpers) • [Concurrency](Concurrency) • [Results](Results) • [Exceptions](Exceptions) • [Observability](Observability) • [Integration](Integration)

## Developer Resources

- **[API Reference](API-README)** - Full class and method documentation
- **[Quickstart](Introduction#build-your-first-authorization-system)** - Get up and running in minutes
- **[Helpers](Introduction#helper-functions)** - Convenient shortcuts for common operations
- **[Testing Guide](Integration#testing)** - Unit testing with the SDK
- **[Performance Guide](Concurrency)** - Optimize for high-scale applications

## Support & Community

- **[Report Issues](https://github.com/evansims/openfga-php/issues)** - Bug reports and feature requests
- **[Discussions](https://github.com/evansims/openfga-php/discussions)** - Community support and questions
- **[Contributing](https://github.com/evansims/openfga-php/blob/main/.github/CONTRIBUTING.md)** - Help improve the SDK
- **[Changelog](https://github.com/evansims/openfga-php/blob/main/CHANGELOG.md)** - Latest updates and releases

## OpenFGA Ecosystem

- **[OpenFGA Documentation](https://openfga.dev/docs)** - Official OpenFGA documentation
- **[OpenFGA Playground](https://play.fga.dev)** - Interactive modeling environment
- **[Authorization Concepts](https://openfga.dev/docs/concepts)** - Learn relationship-based access control
- **[Other SDKs](https://openfga.dev/docs/getting-started/install-sdk)** - JavaScript, Go, Python, .NET, and more

---

*OpenFGA PHP SDK • [Apache 2.0 License](https://github.com/evansims/openfga-php/blob/main/LICENSE)*
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

    # Extract the wiki page title from filename and add it back without prefixes
    filename=$(basename "$file" .md)

    # Create clean title by removing API prefix and README suffix
    clean_title="$filename"
    if [[ "$clean_title" == API-* ]]; then
        clean_title="${clean_title#API-}"
    fi
    if [[ "$clean_title" == *-README ]]; then
        clean_title="${clean_title%-README}"
    fi

    # Convert remaining dashes to spaces for readability
    clean_title=$(echo "$clean_title" | sed 's/-/ /g')

    # Handle title removal differently for written guides vs API docs
    if [[ "$file" == "./API-"* ]]; then
        # For API docs: Remove ALL H1 titles and add clean title
        sed -i.bak '/^# /d' "$file"

        # Add the clean title back as the first line
        sed -i.bak "1i\\
# $clean_title
" "$file"
    else
        # For written guides: Remove the first H1 header completely
        # Wiki will auto-generate the page title from filename
        sed -i.bak '1{/^# /d;}' "$file"
    fi

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
echo "🧹 Clearing existing wiki content..."
find . -type f -not -path "./.git/*" -delete 2>/dev/null || true
find . -type d -empty -not -path "./.git/*" -delete 2>/dev/null || true

# Copy new content
echo "📋 Copying new content..."
cp -r ../wiki-content/* .

# Force add timestamp to ensure changes are detected
echo "⏰ Last updated: $(date)" >> .sync-timestamp

# Add and commit changes
git add .

# Debug: Show what files are staged
echo "🔍 Checking staged changes..."
STAGED_FILES=$(git diff --staged --name-only)
if [ -n "$STAGED_FILES" ]; then
    echo "📄 Staged files:"
    echo "$STAGED_FILES" | head -10
    echo "🔍 Sample diff:"
    git diff --staged --stat | head -5
else
    echo "❌ No staged files found"
fi

# Always sync wiki content (force mode is now default)
# The wiki should always reflect the current state of documentation
if git diff --staged --quiet; then
    echo "ℹ️  No file changes detected, but syncing anyway to ensure wiki is up to date"
else
    echo "💾 Committing detected changes..."
fi

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

# Cleanup
cd ..
rm -rf wiki-content

echo "✨ Wiki sync complete!"
