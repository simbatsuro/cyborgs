sudo apt-get install php7.0-sqlite

run composer install 

#### File Handling
The program is designed to work with large files above PHP's default file size limit. To enable users
to upload large files increase the maximum allowed size as well process time in your PHP your server's php.ini file.

Here are the steps you can follow:

1. Locate your PHP configuration file.
2. Edit your PHP configuration file and look for the following lines:

Change the values to the maximum file size you want to allow. In our case it is 150M:
```
upload_max_filesize = 150M
post_max_size = 150M

```
Change the values to the maximum proce size you want to allow. In our case it is 300 seconds:

```
max_execution_time = 300
max_input_time = 300

```
3. Save the changes to the file.
4. Restart your web server to apply the changes.
