# MTurk-Batch-Posting-Tool

This tool posts batches of tasks to Amazon's Mechanical Turk service. It authenticates users via GitHub, and uses templates placed in a Github repo to create the tasks via the MTurk API. Some of the edges are still kind of rough: a future version will place the configurable settings into a separate file.

## Requirements
Install [KNPLab's PHP Github API Client](https://github.com/KnpLabs/php-github-api)

## Attribution
This tool uses a modified version of [jackbot's PHP-Mechanical-Turk class](https://github.com/jackbot/PHP-Mechanical-Turk).
