#!/usr/bin/env bash

echo "#To check if the nginx server is responding to static requests:";
echo "curl $WEB_FQDN:$WEB_PORT/status/index.html"
echo "echo"

echo "#To check if the nginx server is responding to php requests:";
echo "curl $WEB_FQDN:$WEB_PORT/status/index.php"
echo "echo"

echo "#To check basic deployment info:";
echo "curl -H 'Host: clean-db._PROJECT_.com' $WEB_FQDN:$WEB_PORT/status/dna-health-checks.php"
echo "echo"

echo "#To check if the nginx server is responding to php requests using sessions:";
echo "curl -H 'Host: clean-db._PROJECT_.com' $WEB_FQDN:$WEB_PORT/status/dna-health-checks.php?s=1"
echo "echo"

exit