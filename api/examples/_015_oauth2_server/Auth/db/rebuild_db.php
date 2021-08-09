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

$db->exec('
    CREATE TABLE oauth_clients (
      client_id             VARCHAR(80)   NOT NULL,
      client_secret         VARCHAR(80),
      redirect_uri          VARCHAR(2000),
      grant_types           VARCHAR(80),
      scope                 VARCHAR(4000),
      user_id               VARCHAR(80),
      PRIMARY KEY (client_id)
  )');

$db->exec('
    CREATE TABLE oauth_access_tokens (
      access_token         VARCHAR(40)    NOT NULL,
      client_id            VARCHAR(80)    NOT NULL,
      user_id              VARCHAR(80),
      expires              TIMESTAMP      NOT NULL,
      scope                VARCHAR(4000),
      PRIMARY KEY (access_token)
    )');

$db->exec('
    CREATE TABLE oauth_authorization_codes (
      authorization_code  VARCHAR(40)     NOT NULL,
      client_id           VARCHAR(80)     NOT NULL,
      user_id             VARCHAR(80),
      redirect_uri        VARCHAR(2000),
      expires             TIMESTAMP       NOT NULL,
      scope               VARCHAR(4000),
      id_token            VARCHAR(1000),
      PRIMARY KEY (authorization_code)
    )');

$db->exec('
    CREATE TABLE oauth_refresh_tokens (
      refresh_token       VARCHAR(40)     NOT NULL,
      client_id           VARCHAR(80)     NOT NULL,
      user_id             VARCHAR(80),
      expires             TIMESTAMP       NOT NULL,
      scope               VARCHAR(4000),
      PRIMARY KEY (refresh_token)
    )');

$db->exec('
    CREATE TABLE oauth_users (
      username            VARCHAR(80),
      password            VARCHAR(80),
      first_name          VARCHAR(80),
      last_name           VARCHAR(80),
      email               VARCHAR(80),
      email_verified      BOOLEAN,
      scope               VARCHAR(4000),
      PRIMARY KEY (username)
    )');

$db->exec('
    CREATE TABLE oauth_scopes (
      scope               VARCHAR(80)     NOT NULL,
      is_default          BOOLEAN,
      PRIMARY KEY (scope)
    )');

$db->exec('
    CREATE TABLE oauth_jwt (
      client_id           VARCHAR(80)     NOT NULL,
      subject             VARCHAR(80),
      public_key          VARCHAR(2000)   NOT NULL
    )');

// add test data
$db->exec('INSERT INTO oauth_clients (client_id, client_secret) VALUES ("demoapp", "demopass")');
$db->exec(sprintf('INSERT INTO oauth_users (username, password) VALUES ("demouser", "%s")', sha1("testpass")));

chmod(Server::$targetFile, 0777);
// $db->exec('INSERT INTO oauth_access_tokens (access_token, client_id) VALUES ("testtoken", "Some Client")');
// $db->exec('INSERT INTO oauth_authorization_codes (authorization_code, client_id) VALUES ("testcode", "Some Client")');
