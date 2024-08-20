# Install the 501 Blog CMS

1. LAMP stack

You need a LAMP web server (using MariaDB, not MySQL)

For the native LAMP server for this web app, see the [LAMP Desktop](https://github.com/inkVerb/vip/blob/master/Cheat-Sheets/LAMP-Desktop.md) cheat sheet

These instructions use the web directory

```
/srv/www/html
```

and the web user

```
www
```

2. Install Linux packages
  - *(These may be different per distro)*
  - `ffmpeg`
  - `pandoc`
  - `texlive-`
  - `imagemagick`
  - `lame`
  - `xmlstarlet`

| **Arch/Manjaro** :$ (for use in terminal)

```console
sudo pacman -S --noconfirm libxml2 xmlstarlet imagemagick ffmpeg lame pandoc texlive-core texlive-latex texlive-fontsrecommended texlive-latexrecommended
```

| **Debian/Ubuntu** :$ (for use in terminal)

```console
sudo apt install -y libxml2-utils xmlstarlet imagemagick ffmpeg libmp3lame0 pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-recommended
```

- *On Debian, a more up-to-date alternative to `libmp3lame0` is: `libavcodec-extra57`*
  - *If `libavcodec-extra57` is not available, find the right number with: `sudo apt-cache search libavcodec-extra`*

| **OpenSUSE** :$ (for use in terminal)

```console
sudo zypper install -y libxml2 xmlstarlet imagemagick ffmpeg lame pandoc texlive-scheme-full

```

3. Create a database and user

**This guide assumes** : 

```
Database name: blog_db
Database username: blog_db_user
Database password: blogdbpassword
Database host: localhost
```

| **Create database** :> (modify if different, for use in SQL terminal)

```sql
CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON blog_db.* TO blog_db_user@localhost IDENTIFIED BY 'blogdbpassword';
FLUSH PRIVILEGES;
```

4. Create a `cron` task to run important updates

| **Create `cron` file to edit** :$

```console
sudo touch /etc/cron.d/webapp
sudo chmod 644 /etc/cron.d/webapp
sudo vim /etc/cron.d/webapp
```

*We can't add contents to a `cron` file using `sudo` to dump output to the file, so we must do that manually with an editor like `vim`...*

- Copy this with <kyb>Ctrl</kybd> + <kyb>C</kybd>:
  - Make sure `webappfolder` to the location of your web app (if it differs on your distro)

```console
*/15 * * * * root /usr/bin/php /srv/www/html/webappfolder/task.aggregatefetch.php.php
```
- Press:
  - <kbd>i</kbd>
  - <kyb>Ctrl</kybd> + <kyb>V</kybd>
  - <kbd>Esc</kbd>
- Type:
  - `w:` <kbd>Enter</kbd>

5. Create directories

| **Create web directories** :$ (for use in terminal)

```console
sudo mkdir -p /srv/www/html/webappfolder
cd /srv/www/html/webappfolder
sudo mkdir -p media/docs media/audio media/video media/images media/uploads media/original/images media/original/video media/original/audio media/original/docs media/pro
```

*These are the directories we need in the web folder of the app:*

```
media
media/docs
media/audio
media/video
media/images
media/uploads
media/original/images
media/original/video
media/original/audio
media/original/docs
media/pro
```

6. Put the contents of "cms/" into the same webfolder
  - Including rename `htaccess` to `.htaccess`

| **Copy web files** :$ (for use in terminal)

```console
sudo cp cms/* /srv/www/html/webappfolder/
sudo mv /srv/www/html/webappfolder/htaccess /srv/www/html/webappfolder/.htaccess
sudo chown -R www:www /srv/www/html/webappfolder
```

Optional: edit webappfolder/in.conf.php to contain the database name and user info, otherwise configure it on the Install page...

7. Create the proper database
  - This can be done from the command line using `sudo` or as an SQL administrator from the SQL terminal:

| **Create database via BASH CLI** :$

```console
sudo mariadb -e "
CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_db_user'@'localhost' IDENTIFIED BY 'blogdbpassword';
FLUSH PRIVILEGES;"
```

| **Create database via SQL admin prompt** :>

```console
CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON blog_db.* TO 'blog_db_user'@'localhost' IDENTIFIED BY 'blogdbpassword';
FLUSH PRIVILEGES;
```

8. Install

```console
webappfolder/install.php
```

9. **Optional:** Backup/export database if needed
  - If you ever need, this command should back up your database

```console
sudo mariadb-dump blog_db > /etc/501webapp/blog_db.sql
```

10. **Optional: CAUTION!** Delete your database forever if needed
  - If you ever need, this command should delete your database forever
  - This can be done from the command line using `sudo` or as an SQL administrator from the SQL terminal:

| **Forever delete database via BASH CLI** :$

```console
sudo mariadb -e "
DROP USER IF EXISTS 'blog_db_user'@'localhost';
DROP DATABASE IF EXISTS blog_db;
FLUSH PRIVILEGES;"
```

| **Forever delete via SQL admin prompt** :>

```console
DROP USER IF EXISTS 'blog_db_user'@'localhost';
DROP DATABASE IF EXISTS blog_db;
FLUSH PRIVILEGES;
```