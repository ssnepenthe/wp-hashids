#!/usr/bin/env bash

if [[ ! -d wordpress/wp-content/uploads ]]; then
    mkdir -p wordpress/wp-content/uploads
fi

if [[ -f wordpress/wp-content/database/.ht.sqlite ]]; then
    rm wordpress/wp-content/database/.ht.sqlite
fi

vendor/bin/phpunit --bootstrap=tests/integration/bootstrap.php --testsuite=integration
