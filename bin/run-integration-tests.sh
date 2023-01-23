#!/usr/bin/env bash

if [[ -f wordpress/wp-content/database/.ht.sqlite ]]; then
    rm wordpress/wp-content/database/.ht.sqlite
fi

vendor/bin/phpunit --bootstrap=tests/integration/bootstrap.php --testsuite=integration
