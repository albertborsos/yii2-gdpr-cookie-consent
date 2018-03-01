#!/bin/sh

php ./vendor/bin/phpcs --standard=vendor/mito/yii2-coding-standards/Application --ignore=*/views/* --extensions=php /src
SRC=$?
php ./vendor/bin/phpcs --standard=vendor/mito/yii2-coding-standards/ViewsMixed --extensions=php /src/actions/views
VIEWS=$?
php ./vendor/bin/phpcs --standard=vendor/mito/yii2-coding-standards/Application -s --exclude=PSR1.Files.SideEffects,PSR1.Classes.ClassDeclaration --extensions=php  /tests
TESTS=$?

if [ $SRC -ne 0 ] || [ $VIEWS -ne 0 ] || [ $TESTS -ne 0 ]; then
    exit 1
fi
