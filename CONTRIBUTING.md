Welcome to the ESO Raidplanner contribution guidelines.

## Contributing
Everyone is able to make pull requests to this repository. However, merging has a couple of requirements:
* Approved review of an administrator
* All Travis CI checks should pass
* Code is properly formatted (Travis CI will also check for this)
* Explain in your pull request what your code adds to the project
* Commit messages are required

## Code styling
To make sure your code is properly formatted simply run this command in the root directory `bin/php-cs-fixer fix -v
`. This command should automatically format your code in order to pass the checks in Travis.
* Keep true to the Docblock in already existing classes. No changes to this text are allowed
* Do not add any authors or names to code or configuration files (such as composer.json)

## PHP7
This project is running on PHP7.2, meaning that we intend to use its functionality to the max. So please make sure to:
* Add typehints wherever possible
* Add return types wherever possible
* Use the Null coalescing operator when dealing with null values wherever possible

## Laravel
This project is based on the Laravel Framework version 5.5. Laravel comes with a lot of handy features out of the box. We encourage contributors to use these features as much as possible.
* Use Eloquent whenever dealing with database queries or Models
* Keep true to the Laravel standard MVC implementation (Model > View > Controller)
