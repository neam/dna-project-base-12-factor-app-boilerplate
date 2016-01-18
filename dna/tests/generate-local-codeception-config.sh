#!/bin/bash

script_path=`dirname $0`

erb $script_path/codeception.yml.erb > $script_path/codeception.yml

exit 0