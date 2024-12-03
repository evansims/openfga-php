# Release process

When releasing a new version of the SDK there are some checks and updates that need to be done:

- Clear your local repository with: `git add . && git reset --hard && git checkout 1.x`
- On the GitHub repository, check the contents of [github.com/evansims/openfga-php/compare/{latest_version}...3.x](https://github.com/evansims/openfga-php/compare/{latest_version}...3.x)
- Update the version number in [src/SDK.php](src/SDK.php)
- Run the tests locally using: `composer test`
- Commit the SDK file with the message: `git commit -m "release: vX.X.X"`
- Push the changes to GitHub
- Check that the CI is passing as expected: [github.com/evansims/openfga-php/actions](https://github.com/evanisms/openfga-php/actions)
- Tag and push the tag with `git tag vX.X.X && git push --tags`
- Publish release here: [github.com/evansims/openfga-php/releases/new](https://github.com/evansims/openfga-php/releases/new).
