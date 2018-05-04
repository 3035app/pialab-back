#!/usr/bin/env bash

# install ln -s ../../bin/git-script/pre-commit.sh .git/hooks/pre-commit

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER=$(which php-cs-fixer)
HAS_PHP_CS_FIXER=false

if [ -x "$PHP_CS_FIXER" ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    git status --porcelain | grep -e '^[AM]\(.*\).php$' | cut -c 3- | while read line; do
        $PHP_CS_FIXER fix "$line";
        git add "$line";
    done
else
    echo ""
    echo "Please install php-cs-fixer"
    echo ""
fi

echo "php-cs-fixer pre commit hook finish"
