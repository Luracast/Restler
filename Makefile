VERSION       = 0.1.12
CONFIG_TOOL   = .foundation/repo/bin/project-config.php
GENERATE_TOOL = .foundation/repo/bin/project-generate.php
SHELL         = /bin/sh
PACKAGES_PEAR = pear config-get php_dir

.title:
	@echo "Respect/Foundation - $(VERSION)\n"

.check-foundation: .title
	@test -d .foundation || make -f Makefile foundation-develop
# Help is not the default target cause its mainly used as the main
# build command. We're reserving it.
default: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Menu"
	@echo "                          ====================================================================="
	@echo "                    help: Shows Respect/Foundation Help Menu: type: make help"
	@echo "              foundation: Installs and updates Foundation"
	@echo "                          ====================================================================="
	@echo "                          Other Targets Menus"
	@echo "                          ====================================================================="
	@echo "            project-menu: Project Scripts Menu"
	@echo "            package-menu: Show Packaging Toolbox Menu"
	@echo "                dev-menu: Show Dev Toolbox Menu"
	@echo "             deploy-menu: Show Deploy & Release"
	@echo ""

help: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Help"
	@echo "                          ====================================================================="
	@echo "                          Info: As you probably figured out by now the menu lists the make"
	@echo "                                targets on the left, right aligned like Happy Pandas and the"
	@echo "                                descriptions listed in the menu block like this."
	@echo ""
	@echo "                                To make use of any targets you simply add the target name after"
	@echo "                                make cammand in your shell."
	@echo ""
	@echo "                          Example: make help"
	@echo ""
	@echo "                          Which will bring up this screen."
	@echo "                          ====================================================================="
	@echo "               help-menu: The target for the help-menu, for more info, enter: make help-menu"
	@echo "                          ====================================================================="
	@echo "                          Info: For developers: if you happen to get the following error:"
	@echo ""
	@echo "                                       make: *** [target-name] Error 255"
	@echo ""
	@echo "                                It means the shell command that was executed for this target has"
	@echo "                                failed and the problem lies not with the Makefile."
	@echo "                          ====================================================================="
	@echo "                          Note: Respect/Foundation is currently still under active development"
	@echo "                                and such is the affairs with the help as well, I am affraid."
	@echo "                                More information will be added here in the future, if you want"
	@echo "                                to give us a hand there's no better time than now."
	@echo "                          ====================================================================="
	@echo ""

help-menu: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Help Menu"
	@echo "                          ====================================================================="
	@echo "                          Info: The make targets listed on the left, serves as navigation to"
	@echo "                                the sections where you might find more information."
	@echo ""
	@echo "                                To make use of any targets you simply add the target name after"
	@echo "                                make cammand in your shell."
	@echo ""
	@echo "                          Example: make help-menu"
	@echo ""
	@echo "                                Do the same with any of the targets listed here."
	@echo "                          ====================================================================="
	@echo "            help-skelgen: Information about PHPUnit_SkelGen and how it is facilitated through"
	@echo "                                Respect/Foundation to make your life a little easier."
	@echo "                          ====================================================================="
	@echo "                          Note: Respect/Foundation is currently still under active development"
	@echo "                                and such is the affairs with the help as well, I am affraid."
	@echo "                                More information will be added here in the future, if you want"
	@echo "                                to give us a hand there's no better time than now."
	@echo "                          ====================================================================="
	@echo ""
help-skelgen: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Help - Skelgen"
	@echo "                          ====================================================================="

	@echo "            info-skelgen: Info: Generate boilerplate PHPUnit skeleton tests per class of already"
	@echo "                                implemented source code."
	@echo ""
	@echo "                                We have greatly reduced the complexity involved with using this"
	@echo "                                utility. All you need to provide is the fully qualified classname"
	@echo "                                of the the source you want to generate the unit test for."
	@echo "                                 * We will find the class in question;"
	@echo "                                 * We will bootstrap the gen bot so it doesn't complain;"
	@echo "                                 * Based on the project info we know where the tests should go."
	@echo ""
	@echo "                          Usage:"
	@echo "                                make test-skelgen class:\"My\Awesome\Class"
	@echo ""
	@echo "            test-skelgen: Info: Generate boilerplate PHPUnit skeleton tests per class of already"
	@echo "                                implemented source code."
	@echo ""
	@echo "                                We have greatly reduced the complexity involved with using this"
	@echo "                                utility. All you need to provide is the fully qualified classname"
	@echo "                                of the the source you want to generate the unit test for."
	@echo "                                 * We will find the class in question;"
	@echo "                                 * We will bootstrap the gen bot so it doesn't complain;"
	@echo "                                 * Based on the project info we know where the tests should go."
	@echo ""
	@echo "                          Usage:"
	@echo "                                make test-skelgen class:\"My\Awesome\Class"
	@echo ""
	@echo ""
	@echo "                          Not yet implemented following references skelgen assertion shorthand"
	@echo ""
	@echo "                            @assert (...) == X     \tassertEquals(X, method(...))"
	@echo "                            @assert (...) != X     \tassertNotEquals(X, method(...))"
	@echo "                            @assert (...) === X    \tassertSame(X, method(...))"
	@echo "                            @assert (...) !== X    \tassertNotSame(X, method(...))"
	@echo "                            @assert (...) > X      \tassertGreaterThan(X, method(...))"
	@echo "                            @assert (...) >= X     \tassertGreaterThanOrEqual(X, method(...))"
	@echo "                            @assert (...) < X      \tassertLessThan(X, method(...))"
	@echo "                            @assert (...) <= X     \tassertLessThanOrEqual(X, method(...))"
	@echo "                            @assert (...) throws X \t@expectedException X"
	@echo ""

project-menu: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Menu 1"
	@echo "                          ====================================================================="
	@echo "                          Project Scripts"
	@echo "                          ====================================================================="
	@echo "                        :   INFO & SCAFFOLDING"
	@echo "            project-info: Shows project configuration"
	@echo "            project-init: Initilize current folder and create boilerplate project structure"
	@echo "                        :   TESTING"
	@echo "                    test: Run project tests"
	@echo "                coverage: Run project tests and report coverage status"
	@echo "                   clean: Removes code coverage reports"
	@echo "           bootstrap-php: (Re)create all purpose bootstrap.php for phpunit in test folder"
	@echo "       bootstrap-php-opt: Optimized all purpose bootstrap.php with static pear path in test folder"
	@echo "             phpunit-xml: (Re)create phpunit.xml in test folder"
	@echo "              travis-yml: (Re)create .travis.yml in root folder"
	@echo "             travis-lint: Validate your .travis.yml comfiguration"
	@echo "               gitignore: (Re)create .gitignore file"
	@echo "            test-skelgen: Generate boilerplate PHPUnit skeleton tests per class see help-skelgen"
	@echo "        test-skelgen-all: Generate tests for all classes and it's overwrite safe of course"
	@echo "                        :   CLEANUP UTILITIES"
	@echo "        clean-whitespace: All in one does tabs2spaces, unix-line-ends and trailing_spaces"
	@echo "             tabs2spaces: Turns tabs into 4 spaces properly handling mixed tab/spaces"
	@echo "          unix-line-ends: Fixes unix line endings"
	@echo "         trailing_spaces: Removes trailing whitespace"
	@echo "                        :   CODE CONTENT UTILITIES"
	@echo "                cs-fixer: Run PHP Coding Standards Fixer to ensure your cs-style is correct"
	@echo "               codesniff: Run PHP Code Sniffer to generate a report of code analysis"
	@echo "                  phpcpd: Run PHP Copy Paste detector"
	@echo "                  phpdcd: Run PHP Dead Code detector"
	@echo "                  phploc: Run PHP Lines Of Code analyzer for project code statistics"
	@echo "                  phpdoc: Run PhpDocumentor2 to generate the project API documentation"
	@echo "                        :   CONFIGURATION"
	@echo "             package-ini: Creates the basic package.ini file"
	@echo "             package-xml: Propagates changes from package.ini to package.xml"
	@echo "           composer-json: Propagates changes from package.ini to composer.json"
	@echo "                 package: Generates package.ini, package.xml and composer.json files"
	@echo "                    pear: Generates a PEAR package"
	@echo ""



package-menu: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Menu 2"
	@echo "                          ====================================================================="
	@echo "                          Toolbox - Packaging"
	@echo "                          ====================================================================="
	@echo "       composer-validate: Validate composer.json for syntax and other problems"
	@echo "        composer-install: Install this project with composer which will create vendor folder"
	@echo "         composer-update: Update an exiting composer instalation and refresh repositories"
	@echo "                 install: Install this project and its dependencies in the local PEAR"
	@echo "                info-php: Show information about your PHP"
	@echo "              config-php: Locate your PHP configuration file aka. php.ini"
	@echo "             include-php: Show the PHP configured (php.ini) include path"
	@echo "               info-pear: Show information about your PEAR"
	@echo "             locate-pear: Locate the PEAR packages installation folder"
	@echo "            install-pear: PEAR installation instructions"
	@echo "            updated-pear: See if there are any updates for PEAR and the installed packages"
	@echo "         update-all-pear: Update all packages if any updates are available"
	@echo "           packages-pear: Show the list of PEAR installed packages and their version numbers"
	@echo "             verify-pear: Verify that we can include System.php in PHP script"
	@echo "         info-check-pear: PEAR installation verification checklist instructions"
	@echo "            check-pear-1: PEAR Checklist: 1. list PEAR commands"
	@echo "            check-pear-2: PEAR Checklist: 2. PEAR version information aka. make info-pear"
	@echo "            check-pear-3: PEAR Checklist: 3. locate package install folder aka. make locate-pear"
	@echo "            check-pear-4: PEAR Checklist: 4. verify path configured in PHP aka. make include-php"
	@echo "            check-pear-5: PEAR Checklist: 5. include PEAR System.php check aka. make verify-pear"
	@echo "              info-pyrus: Show information about your PEAR2_Pyrus - PEAR2 Installer"
	@echo "           install-pyrus: Downlod and install PEAR2_Pyrus"
	@echo "           info-composer: Show information about your composer"
	@echo "        install-composer: Downlod and install composer"
	@echo ""



dev-menu: .title
	@echo "                          ====================================================================="
	@echo "                          Respect/Foundation Menu 3"
	@echo "                          ====================================================================="
	@echo "                          Toolbox - Development"
	@echo "                          ====================================================================="
	@echo "         info-git-extras: Show information about your installed git extras"
	@echo "      install-git-extras: Install git extras"
	@echo "           info-cs-fixer: Show information about your installed PHP Coding Standards Fixer"
	@echo "        install-cs-fixer: Install PHP Coding Standards Fixer"
	@echo "          info-codesniff: Show information about your installed PHP_CodeSniffer"
	@echo "       install-codesniff: Install PHP_CodeSniffer"
	@echo "       install-psr-sniff: Install Code Sniffer PSR sniffs to allow for PSR 0-3 compliancy checks"
	@echo "            info-phpunit: Show information about your installed PHPUnit"
	@echo "         install-phpunit: Install PHPUnit"
	@echo "             info-phpcpd: Show information about your installed PHP Copy Paste detector"
	@echo "          install-phpcpd: Install PHPcpd"
	@echo "             info-phpdcd: Show information about your installed PHP Dead Code detector"
	@echo "          install-phpdcd: Install PHPdcd"
	@echo "             info-phploc: Show information about your installed PHP LOC analyzer"
	@echo "          install-phploc: Install PHPloc"
	@echo "            info-skelgen: Show information about your installed PHPUnit Skeleton Generator"
	@echo "         install-skelgen: Install PHPUnit Skeleton Generator"
	@echo "       info-test-helpers: Show information about your installed PHPUnit Test Helpers extension"
	@echo "    install-test-helpers: Install PHPUnit Test Helpers extension"
	@echo "             info-phpdoc: Show information about your installed PhpDocumentor2"
	@echo "          install-phpdoc: Install PhpDocumentor2"
	@echo "              info-phpsh: Show information about your installed PHP Shell (phpsh)"
	@echo "           install-phpsh: Install PHP Shell (phpsh) - Requires Python"
	@echo "     install-travis-lint: Install travis-lint configuration checker - Requires ruby gems"
	@echo "    install-uri-template: Install uri_template a php extension. Might require sudo."
	@echo ""



deploy-menu: .title
	@echo "                      ====================================================================="
	@echo "                      Respect/Foundation Menu 4"
	@echo "                      ====================================================================="
	@echo "                      Deploy & Release"
	@echo "                      ====================================================================="
	@echo "               patch: Increases the patch version of the project (X.X.++)"
	@echo "               minor: Increases the minor version of the project (X.++.0)"
	@echo "               major: Increases the major version of the project (++.0.0)"
	@echo "               alpha: Changes the stability of the current version to alpha"
	@echo "                beta: Changes the stability of the current version to beta"
	@echo "              stable: Changes the stability of the current version to stable"
	@echo "                 tag: Makes a git tag of the current project version/stability"
	@echo "               pear-push: Pushes the latest PEAR package. Custom pear_repo='' and pear_package='' available."
	@echo "                 release: Runs tests, coverage reports, tag the build and pushes to package repositories"
	@echo ""



# Foundation puts its files into .foundation inside your project folder.
# You can delete .foundation anytime and then run make foundation again if you need
foundation: .title
	@echo "Updating Makefile"
	curl -LO git.io/Makefile
	@echo "Creating .foundation folder"
	-rm -Rf .foundation
	-mkdir .foundation
	git clone --depth 1 git://github.com/Respect/Foundation.git .foundation/repo
	@make -f Makefile .gitignore-foundation
	@echo "Downloading Onion"
	-curl -L https://github.com/c9s/Onion/raw/master/onion > .foundation/onion;chmod +x .foundation/onion
	@echo "Done."

# Target for Respect/Foundation development and internal use only. This target will not appear on the menus.
foundation-develop:
	@if make .prompt-yesno message="Do you want to update your Makefile?" 2> /dev/null; then \
	  echo "Updating Makefile"; \
	  curl -LO https://raw.github.com/Respect/Foundation/develop/Makefile; \
	fi
	@echo "Creating .foundation folder"
	-rm -Rf .foundation
	-mkdir .foundation
	git clone --depth 1 git://github.com/Respect/Foundation.git .foundation/repo
	cd .foundation/repo/ && git fetch && git checkout develop && cd -
	@make -f Makefile .gitignore-foundation
	@echo "Downloading Onion"
	-curl -L https://github.com/c9s/Onion/raw/master/onion > .foundation/onion;chmod +x .foundation/onion
	@echo "Done."

.gitignore-foundation:
	@test -f .gitignore || make -f Makefile .gen-gitignore
	@grep -q .foundation .gitignore || echo .foundation >> .gitignore

.gen-gitignore:
	@echo "(Re)create .gitignore"
	@$(GENERATE_TOOL) config-template gitignore > gitignore.tmp && mv -f gitignore.tmp .gitignore

gitignore: .title .gen-gitignore

project-info: .check-foundation
	@echo "\nProject Information\n"
	@echo "             php-version:" `$(CONFIG_TOOL) php-version `
	@echo "      project-repository:" `$(CONFIG_TOOL) project-repository `
	@echo "          library-folder:" `$(CONFIG_TOOL) library-folder `
	@echo "             test-folder:" `$(CONFIG_TOOL) test-folder `
	@echo "           config-folder:" `$(CONFIG_TOOL) config-folder `
	@echo "           public-folder:" `$(CONFIG_TOOL) public-folder `
	@echo "           vendor-folder:" `$(CONFIG_TOOL) vendor-folder `
	@echo "          sandbox-folder:" `$(CONFIG_TOOL) sandbox-folder `
	@echo "    documentation-folder:" `$(CONFIG_TOOL) documentation-folder `
	@echo "      executables-folder:" `$(CONFIG_TOOL) executables-folder `
	@echo "             vendor-name:" `$(CONFIG_TOOL) vendor-name `
	@echo "            package-name:" `$(CONFIG_TOOL) package-name `
	@echo "            project-name:" `$(CONFIG_TOOL) project-name `
	@echo "        one-line-summary:" `$(CONFIG_TOOL) one-line-summary `
	@echo "     package-description:" `$(CONFIG_TOOL) package-description `
	@echo "         package-version:" `$(CONFIG_TOOL) package-version `
	@echo "       package-stability:" `$(CONFIG_TOOL) package-stability `
	@echo "\r         project-authors: "`$(CONFIG_TOOL) package-authors ` \
		| tr ',' '\n' \
		| awk -F' <' '{ printf "                         %-10-s \t<%15-s \n",$$1,$$2 }'
	@echo "\r    project-contributors: "`$(CONFIG_TOOL) package-contributors ` \
		| tr ',' '\n' \
		| awk -F' <' '{ printf "                         %-10-s \t<%15-s \n",$$1,$$2 }'

	@echo "       package-date-time:" `$(CONFIG_TOOL) package-date-time `
	@echo "               pear-path:" `$(CONFIG_TOOL) pear-path `
	@echo "            pear-channel:" `$(CONFIG_TOOL) pear-channel `
	@echo "         pear-repository:" `$(CONFIG_TOOL) pear-repository `
	@echo "         phar-repository:" `$(CONFIG_TOOL) phar-repository `
	@echo "       pear-dependencies:" `$(CONFIG_TOOL) pear-dependencies `
	@echo "  extension-dependencies:" `$(CONFIG_TOOL) extension-dependencies `
	@echo "             readme-file:" `$(CONFIG_TOOL) readme-file `
	@echo "         project-license:" `$(CONFIG_TOOL) project-license `
	@echo "        project-homepage:" `$(CONFIG_TOOL) project-homepage `
	@echo "               user-name:" `$(CONFIG_TOOL) user-name `
	@echo "              user-email:" `$(CONFIG_TOOL) user-email `
	@echo "               user-home:" `$(CONFIG_TOOL) user-home `
	@echo ""



test-skelgen:	.check-foundation
	@test -f $(shell $(CONFIG_TOOL) test-folder)/bootstrap.php || make bootstrap-php > /dev/null
	@$(eval source-folder=$(shell $(CONFIG_TOOL) library-folder))
	-@if test "$(class)"; then \
		cd $(shell $(CONFIG_TOOL) test-folder) && ../.foundation/repo/bin/phpunit-skelgen-classname "${class}" $(source-folder); \
	else \
		echo "Usage:"; \
		echo "     make test-skelgen class=\"My\\Awesome\\Class\""; \
		echo; \
	fi; \

test-skelgen-all:
	@$(eval source-folder=$(shell $(CONFIG_TOOL) library-folder))
	@find $(source-folder) -type f -name "*.php" \
	  | sed -E 's%$(source-folder)/(.*).php%class=\\"\1\\"%' \
	  | sed 's%/%\\\\\\\\%g' \
	  | xargs -L 1 make test-skelgen;

# Re-usable target for yes no prompt. Usage: make .prompt-yesno message="Is it yes or no?"
# Will exit with error if not yes
.prompt-yesno:
	@printf "$(message) (Y/N) :"
	@read yn; \
	if ! echo $$yn | grep -qi y; then \
	  exit 1; \
	fi;

project-init: .check-foundation
	@if test -d .git; then \
	  echo; \
	  echo "It appears you already have a git repository configured."; \
	  echo "This target, will run git init and auto add + commit."; \
	  if ! make .prompt-yesno message="Do you want to continue?" 2> /dev/null; then \
	    echo "Aborting on request."; \
	    exit; \
	  fi; \
	fi; \
	make -f Makefile .project-init

.project-init: git-init project-folders phpunit-xml bootstrap-php package git-add-all
	sleep 1
	git add -A
	git commit -a -m"Project initialized."

project-folders: .check-foundation
	@$(GENERATE_TOOL) project-folders createFolders

info-git-extras:
	@echo "This is what I know about your git extras:"
	git extras --version

install-git-extras: .check-foundation
	@make -f Makefile info-git-extras > /dev/null || (cd .foundation && curl https://raw.github.com/visionmedia/git-extras/master/bin/git-extras | INSTALL=y sh)

git-init: .check-foundation git-init-only git-add-all
	@git commit -a -m"Initial commit."

git-init-only: .check-foundation
	@git init --shared=all

git-add-all: .check-foundation
	@git add -A

codesniff: .check-foundation
	@echo "Running PHP Codesniffer to assess PSR compliancy"
	phpcs -p --report-full=`$(CONFIG_TOOL) documentation-folder `/full2.out `$(CONFIG_TOOL) library-folder `

phpunit-codesniff: .check-foundation
	@echo "Running PHP Codesniffer to assess PHPUnit compliancy"
	phpcs -p --extensions=PHPUnit --report-full=`$(CONFIG_TOOL) documentation-folder `/full2.out `$(CONFIG_TOOL) library-folder `

phpcpd: .check-foundation
	@echo Running PHP Copy paste detection on library folder
	phpcpd --verbose `$(CONFIG_TOOL) library-folder `

phpdcd: .check-foundation
	@echo Running PHP Dead Code detection on library folder
	phpdcd --verbose `$(CONFIG_TOOL) library-folder `

phploc: .check-foundation
	@echo Running PHP Lines of code statistics on library folder
	phploc --verbose `$(CONFIG_TOOL) library-folder `

phpdoc: .check-foundation
	@echo generating documentation with PhpDocumentor2.
	phpdoc -d `$(CONFIG_TOOL) library-folder ` -t `$(CONFIG_TOOL) documentation-folder ` -p

phpunit-xml: .check-foundation
	@$(GENERATE_TOOL) config-template phpunit.xml > phpunit.xml.tmp && mkdir -p $(shell $(CONFIG_TOOL) test-folder) && mv -f phpunit.xml.tmp $(shell $(CONFIG_TOOL) test-folder)/phpunit.xml

bootstrap-php: .check-foundation
	@$(GENERATE_TOOL) config-template bootstrap.php > bootstrap.php.tmp && mkdir -p $(shell $(CONFIG_TOOL) test-folder) && mv -f bootstrap.php.tmp $(shell $(CONFIG_TOOL) test-folder)/bootstrap.php

bootstrap-php-opt: .check-foundation
	@$(GENERATE_TOOL) config-template bootstrap.php.opt > bootstrap.php.tmp && mkdir -p $(shell $(CONFIG_TOOL) test-folder) && mv -f bootstrap.php.tmp $(shell $(CONFIG_TOOL) test-folder)/bootstrap.php

package-ini: .check-foundation
	@$(GENERATE_TOOL) package-ini > package.ini.tmp && mv -f package.ini.tmp package.ini

travis-yml: .check-foundation
	@$(GENERATE_TOOL) config-template travis.yml > travis.yml.tmp && mv -f travis.yml.tmp .travis.yml

# Generates a package.xml from the package.ini
package-xml: .check-foundation
	@.foundation/onion build; echo
	@if test -f package.xml; then \
	  echo Respect/Foundation:; \
	  echo; echo "    $$ make pear"; echo; \
	fi;


composer-json: .check-foundation
	@$(GENERATE_TOOL) composer-json > composer.json.tmp && mv -f composer.json.tmp composer.json

# Generates all package files
package: .check-foundation package-ini package-xml composer-json

# Phony target so the test folder don't conflict
.PHONY: test
test: .check-foundation
	@cd `$(CONFIG_TOOL) test-folder`;phpunit --testdox .

coverage: .check-foundation
	@cd `$(CONFIG_TOOL) test-folder`;phpunit  --coverage-html=reports/coverage --coverage-text .
	@echo "Done. Reports also available on `$(CONFIG_TOOL) test-folder`/reports/coverage/index.html"

cs-fixer: .check-foundation
	@cd `$(CONFIG_TOOL) library-folder`;../.foundation/php-cs-fixer -v fix --level=all --fixers=indentation,linefeed,trailing_spaces,unused_use,return,php_closing_tag,short_tag,visibility,braces,extra_empty_lines,phpdoc_params,eof_ending,include,controls_spaces,elseif .
	@echo "Library folder done. `$(CONFIG_TOOL) library-folder`"
	@cd `$(CONFIG_TOOL) test-folder`;../.foundation/php-cs-fixer -v fix --level=all --fixers=indentation,linefeed,trailing_spaces,unused_use,return,php_closing_tag,short_tag,visibility,braces,extra_empty_lines,phpdoc_params,eof_ending,include,controls_spaces,elseif .
	@echo "Test folder done. `$(CONFIG_TOOL) test-folder` "
	@echo "Done. You may verify the changes and commit if you are happy."

# Any cleaning mechanism should be here
clean: .check-foundation
	@rm -Rf `$(CONFIG_TOOL) test-folder`/reports

# Targets below use the same rationale. They change the package.ini file, so you'll need a
# package-sync after them
patch: .check-foundation
	@$(GENERATE_TOOL) package-ini patch > package.ini.tmp && mv -f package.ini.tmp package.ini

minor: .check-foundation
	@$(GENERATE_TOOL) package-ini minor > package.ini.tmp && mv -f package.ini.tmp package.ini

major: .check-foundation
	@$(GENERATE_TOOL) package-ini major > package.ini.tmp && mv -f package.ini.tmp package.ini

alpha: .check-foundation
	@$(GENERATE_TOOL) package-ini alpha > package.ini.tmp && mv -f package.ini.tmp package.ini

beta: .check-foundation
	@$(GENERATE_TOOL) package-ini beta > package.ini.tmp && mv -f package.ini.tmp package.ini

stable: .check-foundation
	@$(GENERATE_TOOL) package-ini stable > package.ini.tmp && mv -f package.ini.tmp package.ini

tag: .check-foundation
	-git tag `$(CONFIG_TOOL) package-version ` -m 'Tagging.'

# Runs on the current package.xml file
pear: .check-foundation
	@$(eval count=$(shell grep -c dir package.xml)) \
	if test $(count) -gt 1; then \
	  pear package; \
	else \
	  echo "There are no <contents> defined in package.xml"; \
	  echo "Nothing to build"; \
	fi;

info:
	@pear info $(shell $(CONFIG_TOOL) package-name)|egrep 'Version|Name|Summary|Description|-'

# On root PEAR installarions, this need to run as sudo
install: .check-foundation
	@if ! test -f package.xml; then \
	  echo "No package.xml found."; \
	  echo "Nothing to install"; \
	elif ! make info 2> /dev/null; then \
	  echo "You may need to run this as sudo."; \
	  echo "Discovering channel"; \
	  pear channel-info $(shell $(CONFIG_TOOL) pear-channel) || pear channel-discover $(shell $(CONFIG_TOOL) pear-channel); \
	  pear install package.xml; \
	fi;

info-php: .check-foundation
	@echo "This is what I know about your PHP."
	php --version

config-php: .check-foundation
	@echo "The location of your PHP configuration file."
	php --ini

include-php: .check-foundation
	@echo "The PHP configured include path where external packages can be found, like PEAR packages for example."
	php  -r 'echo get_include_path()."\n";'

info-pear: .check-foundation
	@echo "This is what I know about your PEAR."
	pear -V

updated-pear: .check-foundation
	@echo "Fetching possible upgrade information from all channels."
	pear list-upgrades

update-all-pear: .check-foundation
	@echo "Updating all PEAR packages if any updates are available."
	pear upgrade-all

packages-pear: .check-foundation
	@echo "The following PEAR packages are currently installed."
	pear list

locate-pear: .check-foundation
	@echo "The PEAR installed package can be found at:"
	pear config-get php_dir

verify-pear: .check-foundation
	@echo "If the following PHP script:"
	@echo "<?php"
	@echo "  require_once 'System.php';"
	@echo "  echo 'Can we include PEAR System.php? : ';"
	@echo "  var_export(class_exists('System', false));"
	@echo "?>"
	@echo ""
	@echo "Executes without any error and answers true fully to our question then may safely assume that the PEAR installation is sound."
	echo "<?php require_once 'System.php'; echo 'Can we include PEAR System.php? : ', var_export(class_exists('System', false), true), PHP_EOL;"

install-pear: .check-foundation
	@echo "Because we rely so extensively on a proper PEAR installation it is pertinent that PEAR is installed propeprly."
	@echo "Unfortunately I am not cenfident that I am capable, at this point, to successfully install PEAR on every system,"
	@echo "yours in particular, without baking a complete mess of things."
	@echo ""
	@echo "Don't worry this is not difficult and I am sure you will succeed by following the detailed instructions at the"
	@echo "following URL: http://pear.php.net/manual/en/installation.getting.php"
	@echo ""
	@echo "Once you're done you are welcome to return so I may assist you with the installation verification process."
	@echo "Good luck!"

info-check-pear: .check-foundation
	@echo "At the address: http://pear.php.net/manual/en/guide.users.commandline.packageinfo.php PEAR lists a comprehensive"
	@echo "list of insructions to execute and verify that the installation is sound."
	@echo ""
	@echo "For your convenience you may verify these routiens by executing the targets make check-pear-1 through 5 but before"
	@echo "you eagerly run all the scripts one by one, they are non changing and only informative in nature, I know this must"
	@echo "be very exciting I can still remember when I did my first installation back in 1977 when I was created by Dr. Stuart"
	@echo "Feldman at Bell Labs who later received the ACM Software System Award in 2003, on my behalf. It seems like yesterday."
	@echo ""
	@echo "There is a trick to the checklist which might save you some time, I really don't mind if you follow them one by one"
	@echo "and the Respect/Foundation developers were so maticulous to add them all but let me share a little secret with you."
	@echo ""
	@echo "If making the final target : make check-pear-5 succeeds then we have verified that the installation was a success."
	@echo ""
	@echo "Should you however hav"

check-pear-1: .check-foundation
	@echo "PEAR Checklist step 1"
	@echo ""
	@echo "By executing the pear command we should see a list of commands."
	@echo "If the did no succeed verify that the pear command is on your system path."
	@echo "The command is piped through more, press spacebar to page or q to abort."
	pear | more

check-pear-2: .check-foundation
	@echo "PEAR Checklist step 2"
	@echo ""
	@echo "Display PEAR version information"
	make info-pear

check-pear-3: .check-foundation
	@echo "PEAR Checklist step 3"
	@echo ""
	@echo "Display the PEAR install location where packages are installed"
	make locate-pear

check-pear-4: .check-foundation
	@echo "PEAR Checklist step 4"
	@echo ""
	@echo "Verify the presence of the PEAR install directory in the PHP include path."
	make include-php

check-pear-5: .check-foundation
	@echo "PEAR Checklist step 5"
	@echo ""
	@echo "Test to see if we can include packages from PEAR."
	@echo "For troubleshooting please refer to the target : make info-check-pear."
	make verify-pear

info-cs-fixer: .check-foundation
	@echo "This is what I know about your PHP Coding Standards Fixer."
	.foundation/php-cs-fixer -V

install-cs-fixer: .check-foundation
	@echo "Attempting to download PHP Coding Standards Fixer."
	curl http://cs.sensiolabs.org/get/php-cs-fixer.phar -o .foundation/php-cs-fixer && chmod a+x .foundation/php-cs-fixer

install-travis-lint: .check-foundation
	@echo "Attempting to install travis-lint. Requires ruby gem..."
	@gem install travis-lint

travis-lint: .check-foundation
	@echo "Checking your .travis.yml"
	@travis-lint ./.travis.yml

info-composer: .check-foundation
	@echo "This is what I know about your composer."
	@/usr/bin/env PATH=$$PATH:./.foundation composer about 2> /dev/null || (echo "No composer installed." && false)

install-composer: .check-foundation
	@echo "Attempting to download and install composer packager."
	@curl -s http://getcomposer.org/installer | php
	@mv composer.phar .foundation/composer && chmod a+x .foundation/composer && exit 0

.check-composer:
	@make -f Makefile info-composer > /dev/null || make -f Makefile install-composer > /dev/null || (echo "Unable to install composer. Aborting..." && false)

composer-validate: .check-foundation .check-composer
	@echo "Running composer validate, be brave."
	@/usr/bin/env PATH=$$PATH:./.foundation composer validate -v

composer-install: .check-foundation .check-composer
	@echo "Running composer install, this will create a vendor folder and configure autoloader."
	@/usr/bin/env PATH=$$PATH:./.foundation composer install -v

composer-update: .check-foundation .check-composer
	@echo "Running composer update, which updates your existing installation."
	@/usr/bin/env PATH=$$PATH:./.foundation composer update -v

info-pyrus: .check-foundation
	@echo "This is what I know about your PEAR2_Pyrus."
	.foundation/pyrus --version

install-pyrus: .check-foundation
	@echo "Attempting to download and install PEAR2_Pyrus."
	curl http://pear2.php.net/pyrus.phar -o .foundation/pyrus && chmod a+x .foundation/pyrus
	.foundation/pyrus mypear `$(CONFIG_TOOL) vendor-folder`
	.foundation/pyrus install PEAR2_Pyrus_Developer-alpha
	.foundation/pyrus install PEAR2_Autoload-alpha
	.foundation/pyrus install PEAR2_Templates_Savant-alpha

info-codesniff: .check-foundation
	@echo "This is what I know about your PHP_CodeSniffer."
	phpcs --version
	@echo "The following PHP_CodeSniffer coding standard sniffs are installed."
	phpcs -i

install-codesniff: .check-foundation
	@echo "Attempting to download and install PHP_CodeSniffer. This will likely require sudo."
	pear install --alldeps PHP_CodeSniffer
	https://github.com/elblinkin/PHPUnit-CodeSniffer.git

install-psr-sniff: .check-foundation
	@echo "Attempting to download and install PHP_CodeSniffer sniffs for PSR's. This will likely require sudo."
	@cd `$(PACKAGES_PEAR)`/PHP/CodeSniffer/Standards && git clone https://github.com/klaussilveira/phpcs-psr PSR
	@phpcs --config-set default_standard PSR

install-phpunit-sniff: .check-foundation
	@echo "Attempting to download and install PHPUnit_CodeSniffer sniffs for PHPUnit standards. This will likely require sudo."
	@cd `$(PACKAGES_PEAR)`/PHPUnit/ && git clone https://github.com/elblinkin/PHPUnit-CodeSniffer.git && cp -R PHPUnit-CodeSniffer/PHPUnitStandard ../PHP/CodeSniffer/Standards/PHPUnit

info-phpunit: .check-foundation
	@echo "This is what I know about your PHPUnit."
	@phpunit --version

install-phpunit: .check-foundation
	@echo "Attempting to download and install PHPUnit. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear channel-info pear.symfony-project.com > /dev/null || pear channel-discover pear.symfony-project.com
	@pear install --alldeps pear.phpunit.de/PHPUnit

info-phpcpd: .check-foundation
	@echo "This is what I know about your PHPcpd."
	@phpcpd --version

install-phpcpd: .check-foundation
	@echo "Attempting to download and install PHPcpd. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear install --alldeps pear.phpunit.de/phpcpd

info-phpdcd: .check-foundation
	@echo "This is what I know about your PHPdcd."
	@phpdcd --version

install-phpdcd: .check-foundation
	@echo "Attempting to download and install PHPdcd. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear install --alldeps pear.phpunit.de/phpdcd-beta

info-phploc: .check-foundation
	@echo "This is what I know about your PHPloc."
	@phploc --version

install-phploc: .check-foundation
	@echo "Attempting to download and install PHPloc. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear install --alldeps pear.phpunit.de/phploc

install-phpcov: .check-foundation
	@echo "Attempting to download and install PHPcov. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear install --alldeps pear.phpunit.de/phpcov

info-skelgen:
	@echo "This is what I know about your PHPUnit_SkeletonGenerator.\n"
	@phpunit-skelgen --version

install-skelgen: .check-foundation
	@echo "Attempting to download and install PHPUnit Skeleton Generator. This will likely require sudo."
	@pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de
	@pear install --alldeps pear.phpunit.de/PHPUnit_SkeletonGenerator

info-test-helpers: .check-foundation
	@pecl info phpunit/test_helpers|egrep 'Version|Name|Summary|Description|-'

install-test-helpers:
	@if make info-test-helpers 2> /dev/null; then \
	  exit; \
	fi; \
	echo "Attempting to download and install PHPUnit Test Helpers. This will likely require sudo." \
	pear channel-info pear.phpunit.de > /dev/null || pear channel-discover pear.phpunit.de; \
	pecl install  --alldeps phpunit/test_helpers

info-phpdoc: .check-foundation
	@echo "This is what I know about your PhpDocumentor."
	@echo "The command is piped through more, press spacebar to page or q to abort."
	@pear info phpdoc/phpDocumentor-alpha

install-phpdoc: .check-foundation
	@echo "Attempting to download and install PhpDocumentor2. This will likely require sudo."
	@pear channel-info pear.phpdoc.org > /dev/null || pear channel-discover pear.phpdoc.org
	@pear install --alldeps phpdoc/phpDocumentor-alpha

info-phpsh: .check-foundation
	@echo "This is what I know about your phpsh."
	@phpsh --version

install-phpsh: .check-foundation
	@echo "Attempting to download and install phpsh."
	git clone --progress -v https://github.com/facebook/phpsh.git .foundation/phpshsrc
	sudo easy_install readline
	cd .foundation/phpshsrc && python setup.py build && sudo python setup.py install

install-uri-template: .check-foundation
	@git clone --progress -v git://github.com/ioseb/uri-template.git .foundation/uri-template
	@cd .foundation/uri-template && phpize && ./configure && make && make test && make install
	@echo
	@echo If all went well and you saw no errors or FAILs then congratulations!
	@echo all that is left is to ensure that extension=uri_template.so is in your php.ini
	@echo

# Clean up utils

tabs2spaces: .check-foundation
	@if test "$(file)"; then \
	  expand -t 4 "$(file)" > "$(file).tmp" && mv -f "$(file).tmp" "$(file)"; \
	else \
		find . -type f -name "*.php" -exec make tabs2spaces file="{}" \;; \
	fi;

trailing-spaces: .check-foundation
	@if test "$(file)"; then \
	  awk '{sub(/[ \t]+$$/, "")};1' "$(file)" > "$(file).tmp" && mv -f "$(file).tmp" "$(file)"; \
	else \
		find . -type f -name "*.php" -exec make trailing-spaces file="{}" \;; \
	fi;

unix-line-ends: .check-foundation
	@if test "$(file)"; then \
	  awk '{sub(/\r$$/,"")};1' "$(file)" > "$(file).tmp" && mv -f "$(file).tmp" "$(file)"; \
	else \
		find . -type f -name "*.php" -exec make unix-line-ends file="{}" \;; \
	fi;

clean-whitespace: .check-foundation
	@if test "$(file)"; then \
		make tabs2spaces file="$(file)" > /dev/null; \
		make unix-line-ends file="$(file)" > /dev/null; \
		make trailing-spaces file="$(file)" > /dev/null; \
	else \
		make tabs2spaces > /dev/null; \
		make unix-line-ends > /dev/null; \
		make trailing-spaces > /dev/null; \
	fi;


# Install pirum, clones the PEAR Repository, make changes there and push them.
pear-push: .check-foundation
	@echo "Installing Pirum"
	@sudo pear install --soft --force pear.pirum-project.org/Pirum
	@echo "Cloning channel from git" `$(CONFIG_TOOL) pear-repository`
	-rm -Rf .foundation/pirum
	git clone --depth 1 `$(CONFIG_TOOL) pear-repository`.git .foundation/pirum
	pirum add .foundation/pirum `$(CONFIG_TOOL) package-name`-`$(CONFIG_TOOL) package-version`.tgz;pirum build .foundation/pirum;
	cd .foundation/pirum;git add .;git commit -m "Added " `$(CONFIG_TOOL) package-version`;git push

packagecommit:
	@git add package.ini package.xml composer.json
	@git commit -m "Updated package files"

# Uses other targets to complete the build
release: test package packagecommit pear pear-push tag
	@echo "Release done. Pushing to GitHub"
	@git push
	@git push --tags
	@echo "Done. " `$(CONFIG_TOOL) package-name`-`$(CONFIG_TOOL) package-version`
