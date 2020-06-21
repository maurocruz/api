# Fwc Api

Php api for schema.org modelling

Add in index page

- 
/*
 * FWC API
 */
$fwcApi = FwcApiFactory::create($slimApp);

$fwcApi->connect("driver sql", "host name", "database name", "publicUser", "password");

$fwcApi->run();

> Create mysql schema

> grant insert privilegie for public user in table user

> register user with name, email and password on https://<domain>/api/user with POST HTTP Request

> Update in table user status to 1
