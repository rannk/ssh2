PHP INTERACTIVE SSH
====
provide an interactive ssh connection.

requirements
====
You need PHP version 5.3+ with the SSH2 extension.

Installation
====

The best way to add the library to your project is using [composer](http://getcomposer.org).

    $ composer require rannk/php-interactive-ssh:dev-master

Usage
====
This wraaper tool is very eash to use. But now we just provide using username and password to access the ssh connection.

# How to use it
step 1: instance a connection object
```php
<?php
$conn = new ssh\Connection(hostname, [port]);
$conn->authByPassword(username, password);
```
setp 2: create a session
```php
<?php
$session = $conn->createSession();
```
setp 3: send your command
```php
<?php
$session->expect('$');
$session->send('ls');
$session->expect('$');
```

# method explain
session::expect(expect_word, expire_time, display_message) return String

    expect_word(String):  which word you expected waiting for when you want to run next command
    
    expire_time(int): The default is 0. set the waiting for time. The zero mean always wait.
    
    display_message(boolean): To set true is mean display the output message that from command. 
    
    
session::send(command) return void

    command(String): The shell command
