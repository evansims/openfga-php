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

# Function to extract code region from snippet content
extract_region() {
    local file="$1"
    local region="$2"
    local range="$3"
    
    if [ -z "$region" ]; then
        # No region specified, use extract_lines for entire file
        extract_lines "$file" "$range"
        return
    fi
    
    # Handle special #intro meta-region
    if [ "$region" = "intro" ]; then
        local intro_content=""
        local first_line=true
        local found_first_region=false
        
        while IFS= read -r line; do
            # Check if we've hit the first example region
            if [[ "$line" =~ ^[[:space:]]*(//|#|/\*)[[:space:]]*example:[[:space:]]*[^[:space:]]+[[:space:]]*$ ]]; then
                found_first_region=true
                break
            fi
            
            # Collect all lines before the first region
            if [ "$first_line" = true ]; then
                intro_content="$line"
                first_line=false
            else
                intro_content+=$'\n'"$line"
            fi
        done < "$file"
        
        # Output the intro content
        if [ -n "$intro_content" ]; then
            if [ -n "$range" ]; then
                echo "$intro_content" | extract_lines_from_string "$range"
            else
                echo "$intro_content"
            fi
        fi
        return
    fi
    
    # Extract content within the specified region
    local in_region=false
    local region_content=""
    local line_num=0
    local first_line=true
    
    while IFS= read -r line; do
        # Check for region start marker
        if [[ "$line" =~ ^[[:space:]]*(//|#|/\*)[[:space:]]*example:[[:space:]]*${region}[[:space:]]*$ ]]; then
            in_region=true
            continue
        fi
        
        # Check for region end marker
        if [[ "$line" =~ ^[[:space:]]*(//|#|/\*)[[:space:]]*end-example:[[:space:]]*${region}[[:space:]]*$ ]]; then
            break
        fi
        
        # Collect lines within the region
        if [ "$in_region" = true ]; then
            ((line_num++))
            if [ "$first_line" = true ]; then
                region_content="$line"
                first_line=false
            else
                region_content+=$'\n'"$line"
            fi
        fi
    done < "$file"
    
    # If no region found, return empty
    if [ -z "$region_content" ]; then
        return
    fi
    
    # Apply line range to the region content if specified
    if [ -n "$range" ]; then
        echo "$region_content" | extract_lines_from_string "$range"
    else
        # Output the region content as-is
        echo "$region_content"
    fi
}

# Function to extract line range from string content
extract_lines_from_string() {
    local range="$1"
    
    if [ -z "$range" ]; then
        # No range specified, output entire content
        cat
    elif [[ "$range" =~ ^([0-9]+)-([0-9]+)$ ]]; then
        # Range: line1-line2
        local start="${BASH_REMATCH[1]}"
        local end="${BASH_REMATCH[2]}"
        sed -n "${start},${end}p"
    elif [[ "$range" =~ ^([0-9]+)-$ ]]; then
        # Range: line- (from line to end)
        local start="${BASH_REMATCH[1]}"
        tail -n +$start
    elif [[ "$range" =~ ^-([0-9]+)$ ]]; then
        # Range: -line (from start to line)
        local end="${BASH_REMATCH[1]}"
        head -n $end
    elif [[ "$range" =~ ^([0-9]+)$ ]]; then
        # Single line
        local line="${BASH_REMATCH[1]}"
        sed -n "${line}p"
    else
        # Invalid range, output entire content
        cat
    fi
}

# Function to extract line range from snippet content
extract_lines() {
    local file="$1"
    local range="$2"
    
    if [ -z "$range" ]; then
        # No range specified, output entire file
        cat "$file"
    elif [[ "$range" =~ ^([0-9]+)-([0-9]+)$ ]]; then
        # Range: line1-line2
        local start="${BASH_REMATCH[1]}"
        local end="${BASH_REMATCH[2]}"
        sed -n "${start},${end}p" "$file"
    elif [[ "$range" =~ ^([0-9]+)-$ ]]; then
        # Range: line- (from line to end)
        local start="${BASH_REMATCH[1]}"
        tail -n +$start "$file"
    elif [[ "$range" =~ ^-([0-9]+)$ ]]; then
        # Range: -line (from start to line)
        local end="${BASH_REMATCH[1]}"
        head -n $end "$file"
    elif [[ "$range" =~ ^([0-9]+)$ ]]; then
        # Single line
        local line="${BASH_REMATCH[1]}"
        sed -n "${line}p" "$file"
    else
        # Invalid range, output entire file
        cat "$file"
    fi
}

# Function to process snippet includes and spacers
process_snippets() {
    local file="$1"
    local temp_file="${file}.tmp"
    
    # Process the file line by line
    while IFS= read -r line; do
        # Check for Markdown link snippet pattern: [Snippet](../../examples/snippets/file.php) or with regions/ranges
        if [[ "$line" =~ ^\[Snippet\]\((\.\./)*examples/snippets/([^\):#]+)(#([^\):]+))?(:([0-9]+(-[0-9]*)?|-[0-9]+))?\)$ ]]; then
            snippet_file="${BASH_REMATCH[2]}"
            region="${BASH_REMATCH[4]}"
            line_range="${BASH_REMATCH[6]}"
        # Check if line contains HTML comment snippet pattern with region and/or line range
        # First try pattern with region support: file.php#region, file.php#region:1-10
        elif [[ "$line" =~ ^[[:space:]]*\<!--[[:space:]]+snippet:[[:space:]]+([^[:space:]:#]+)(#([^[:space:]:]+))(:([0-9]+(-[0-9]*)?|-[0-9]+))?[[:space:]]+--\>[[:space:]]*$ ]]; then
            snippet_file="${BASH_REMATCH[1]}"
            region="${BASH_REMATCH[3]}"
            line_range="${BASH_REMATCH[5]}"
        # Otherwise try pattern without region: file.php, file.php:1-10
        elif [[ "$line" =~ ^[[:space:]]*\<!--[[:space:]]+snippet:[[:space:]]+([^[:space:]:]+)(:([0-9]+(-[0-9]*)?|-[0-9]+))?[[:space:]]+--\>[[:space:]]*$ ]]; then
            snippet_file="${BASH_REMATCH[1]}"
            region=""
            line_range="${BASH_REMATCH[3]}"
        else
            # Not a snippet pattern, check other patterns
            snippet_file=""
        fi
        
        # Process snippet if found
        if [ -n "$snippet_file" ]; then
            snippet_path="examples/snippets/$snippet_file"
            
            if [ -f "$snippet_path" ]; then
                # Determine language from file extension
                extension="${snippet_file##*.}"
                
                # Output the code block with appropriate syntax highlighting
                echo '```'"$extension"
                if [ -n "$region" ]; then
                    # Extract region with optional line range
                    extract_region "$snippet_path" "$region" "$line_range"
                else
                    # Extract full file with optional line range
                    extract_lines "$snippet_path" "$line_range"
                fi
                echo '```'
            else
                echo "<!-- ERROR: Snippet file not found: $snippet_path -->"
                echo '```'"$extension"
                echo "// Snippet file not found: $snippet_file"
                echo '```'
            fi
        # Check if line contains spacer pattern
        elif [[ "$line" =~ ^[[:space:]]*\<!--[[:space:]]+spacer[[:space:]]+--\>[[:space:]]*$ ]]; then
            # Convert spacer comment to HTML break
            echo '<p><br /></p>'
        else
            # Output the line as-is
            echo "$line"
        fi
    done < "$file" > "$temp_file"
    
    # Replace original file with processed content
    mv "$temp_file" "$file"
}

# Flatten written guides into wiki-content root
find docs -name "*.md" -not -path "docs/README.md" -not -path "docs/API/*" | while read file; do
    filename=$(basename "$file")
    cp "$file" "wiki-content/$filename"
    
    # Process snippet includes for written guides only
    echo "Processing snippets in: $filename"
    process_snippets "wiki-content/$filename"
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
    # First, handle relative paths like ../Essentials/Stores.md -> Stores
    sed -i.bak 's|\](../[^/]*/\([^)]*\)\.md)|\](\1)|g' "$file"
    
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

# Function to add spacing before headers
add_header_spacing() {
    local file="$1"
    local temp_file="${file}.spacing.tmp"
    local last_content_was_header=false
    local line_count=0
    local has_content=false
    
    # First pass: count non-empty lines to detect if H2 is first content
    while IFS= read -r line; do
        if [[ -n "$line" ]] && [[ ! "$line" =~ ^[[:space:]]*$ ]]; then
            ((line_count++))
            if [[ $line_count -gt 1 ]] || [[ ! "$line" =~ ^##[[:space:]] ]]; then
                has_content=true
                break
            fi
        fi
    done < "$file"
    
    # Reset for second pass
    last_content_was_header=false
    
    # Second pass: add spacing
    while IFS= read -r line; do
        # Check if current line is any type of header
        if [[ "$line" =~ ^##+[[:space:]] ]]; then
            # Current line is a header
            if [[ "$last_content_was_header" == true ]]; then
                # Header follows another header (possibly with blank lines), skip spacing
                echo "$line"
            elif [[ "$line" =~ ^##[[:space:]] ]] && [[ ! "$line" =~ ^### ]]; then
                # H2 header
                if [[ "$has_content" == true ]] || [[ $line_count -gt 1 ]]; then
                    # Add spacing before H2 unless it's the first content
                    echo ""
                    echo "<p><br /></p>"
                    echo ""
                fi
                echo "$line"
            elif [[ "$line" =~ ^###+ ]]; then
                # H3, H4, H5 headers
                echo ""
                echo "<br />"
                echo ""
                echo "$line"
            else
                echo "$line"
            fi
            last_content_was_header=true
        elif [[ -n "$line" ]] && [[ ! "$line" =~ ^[[:space:]]*$ ]]; then
            # Non-empty, non-header line
            echo "$line"
            last_content_was_header=false
        else
            # Empty line - just output it without changing header tracking
            echo "$line"
        fi
    done < "$file" > "$temp_file"
    
    # Replace original file
    mv "$temp_file" "$file"
}

# Apply header spacing to all markdown files
echo "📐 Adding header spacing..."
find . -name "*.md" -type f -not -name "_Sidebar.md" -not -name "_Footer.md" | while read file; do
    add_header_spacing "$file"
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
