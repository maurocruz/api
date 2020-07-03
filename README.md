# Fwc Api

Php api for schema.org modelling


> Create mysql schema;

> Create two user on mysql schema: one public user and one admin user;

> Grant insert privilegies for the public user in only the user table and grant all privilegies for the admin user;

> GRANT INSERT ON plinct.user TO 'publicUser'@'localhost' IDENTIFIED BY 'p1r3n0p0l1s';


Add in index page on the root
 
//  FWC API
 
$fwcApi = FwcApiFactory::create($slimApp);

$fwcApi->connect("driver sql", "host name", "database name", "publicUser", "password");

$fwcApi->run();



> Start api using https://<domain>/api/start with request HTTP POST request, sending the database admin username and password by form url encoded using <username> and <password> with name of values;

> Register a user with <name>, <email> and <password> on https://<domain>/api/user sending with request HTTP POST from a form url encoded;

> Update in table user the user for administrator with status = 1


> For adding new entities: