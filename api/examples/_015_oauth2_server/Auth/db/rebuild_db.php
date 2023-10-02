<?php

// remove sqlite file if it exists
use Auth\Server;

if (file_exists(Server::$targetFile)) {
    unlink(Server::$targetFile);
}

$dir = dirname(Server::$targetFile);

if (!is_writable($dir)) {
    if (is_dir($dir)) {
        // try to set permissions.
        if (!@chmod($dir, 0777)) {
            throw new Exception("Unable to write to " . Server::$targetFile);
        }
    } else {
        mkdir($dir, 0777, true);
    }
}

// rebuild the DB
$db = new PDO(sprintf('sqlite:%s', Server::$targetFile));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE oauth_clients (client_id TEXT, client_secret TEXT, redirect_uri TEXT)');
$db->exec('CREATE TABLE oauth_access_tokens (access_token TEXT, client_id TEXT, user_id TEXT, expires TIMESTAMP, scope TEXT)');
$db->exec('CREATE TABLE oauth_authorization_codes (authorization_code TEXT, client_id TEXT, user_id TEXT, redirect_uri TEXT, expires TIMESTAMP, scope TEXT, id_token TEXT, code_challenge TEXT, code_challenge_method TEXT)');
$db->exec('CREATE TABLE oauth_refresh_tokens (refresh_token TEXT, client_id TEXT, user_id TEXT, expires TIMESTAMP, scope TEXT)');
$db->exec('CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);');
$db->exec('CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));');
$db->exec('CREATE TABLE oauth_public_keys (client_id VARCHAR(80), public_key VARCHAR(8000), private_key VARCHAR(8000), encryption_algorithm VARCHAR(80) DEFAULT "RS256")');

// add test data
$db->exec('INSERT INTO oauth_clients (client_id, client_secret) VALUES ("demoapp", "demopass")');
$db->exec(sprintf('INSERT INTO oauth_users (username, password) VALUES ("demouser", "%s")', sha1("testpass")));

chmod(Server::$targetFile, 0777);
// $db->exec('INSERT INTO oauth_access_tokens (access_token, client_id) VALUES ("testtoken", "Some Client")');
// $db->exec('INSERT INTO oauth_authorization_codes (authorization_code, client_id) VALUES ("testcode", "Some Client")');
