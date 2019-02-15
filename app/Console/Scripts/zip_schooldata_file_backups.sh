#!/usr/bin/env bash

# Validate params
if [ -z "$1" ]
    then
        echo "MISSING ARGUMENTS: the path where the backup files live must be supplied as an argument."
        exit 1
fi

if [ -z "$2" ]
    then
        echo "MISSING ARGUMENTS: the number of days back must be supplied as an argument."
        exit 1
fi

# find all the csv's older than the number of days supplied and zip them up individually
find $1 -type f -name "*.csv" -mtime +$2 -execdir zip -m -T {}.zip {} \;

#
#   EXPLANATION OF PARAMETERS
#
#   find $1                     = find all files and folders in the current directory
#   -type f                     = only return results that are files
#   -name "*.csv"               = only show CSV files
#   -mtime +$2                  = only files where file's data was last modified n*24 hours ago.
#   -execdir                    = execute the following command for each file where {} is a placeholder
#                                   for the filename. Execute from the directory where the file was found.
#                                   This serves to zip ONLY the file, not the parent directories.
#                                   This is contrasted with -exec, which gives the full path.
#   zip -m -T TARGET SOURCE     = -m is move, so zip up the source file into the target dir & delete source.
#                                   In this case, if source is section.csv, the zipped file will be section.csv.zip
#                                   Again, {} is a placeholder for the filename.
#                               = -T performs an integrity check on the newly created archive