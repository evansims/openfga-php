# Documentation Generator

Automatically generates comprehensive API documentation from the OpenFGA PHP SDK source code.

## Requirements

- PHP 8.3 or higher
- Composer

## Installation

1. Navigate to the tools directory:

   ```bash
   cd tools/docs
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

## Usage

Generate fresh API documentation:

```bash
php generate.php [--src=path/to/src] [--out=path/to/output] [--clean]
```

**Options:**
- `--src`: Override the default source directory (defaults to `../../src/`)  
- `--out`: Override the default output directory (defaults to `../../docs/API/`)  
- `--clean`: Remove existing output directory before generating new files

**Example with clean regeneration:**
```bash
php generate.php --clean
```

This parses all PHP files in the `src/` directory and generates comprehensive Markdown documentation in the `docs/API/` directory.

## Customizing the Output

You can customize the output by editing the `documentation.twig` template file. The template uses the Twig templating engine.

## How It Works

The documentation generator:

1. Scans all PHP files in the `src/` directory
2. Uses PHP's Reflection API to extract class and method information
3. Generates Markdown files in a directory structure that matches the namespace structure
4. Creates links between related classes in the documentation

## Output

The generated documentation will be placed in the `docs/API/` directory, with the following structure:

```text
docs/
  API/
    Client.md
    Models/
      Assertion.md
      AuthorizationModel.md
      # ... and so on
```

Each Markdown file contains:

- Class description (from docblock)
- Namespace information
- Implemented interfaces
- Public methods with:
  - Method signature
  - Description (from docblock)
  - Parameters with types and descriptions
  - Return type and description

## License

This tool is part of the OpenFGA PHP SDK and is distributed under the same license.
