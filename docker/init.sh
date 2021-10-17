#!/bin/sh
/usr/local/sbin/php-fpm --nodaemonize | tail -f /tmp/stdout
