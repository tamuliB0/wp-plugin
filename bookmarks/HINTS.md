# Hints

If you have been stuck on something for more than an hour, check here
before asking for help. These are common issues, not solutions -- you
still need to figure out the fix yourself.

## Page shows blank or "500 Internal Server Error"

Check that your PHP file starts with `<?php`. Check the ddev logs
with `ddev logs` to see the actual error message.

## Form submits but nothing changes in the database

Check that your INSERT query uses the correct column names from
schema.sql. Check that you are using `$_POST['field_name']` with
the exact name from your form's input fields.

## Connecting to the database from PHP

Inside ddev, the database credentials are: host `db`, username `db`,
password `db`, database name `db`. You can use these with PDO:

    new PDO('mysql:host=db;dbname=db', 'db', 'db')

Run `ddev describe` to see all connection details.

## "Connection refused" or database not working

Run `ddev start` to make sure the environment is running. You can
verify the database works with `ddev mysql` to open a MySQL prompt.

## Stuck for more than an hour on anything

Write down what you tried in your PR description before asking for
help. Describe: what you expected to happen, what actually happened,
and what you already tried. This is how professional developers ask
for help.
