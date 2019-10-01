Use composer to install firebase/php-jwt in this folder [(see here)](https://github.com/php-jwt)

There is also a settings.php file in this folder.  It should look something like this:

```php
    <?php
        $settings = array(
            //database information
            "hostname" => "a_hostname_that_point_to_a_database",
            "username" => "a_sql_username",
            "password" => "a_sql_password",
            "dbname"   => "a_name_to_a_database",
            //jwt secret key
            "secret_key" => "a_secret_key",
        );
    ?>

```