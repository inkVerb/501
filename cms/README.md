# Install the 501 Blog CMS

1. LAMP stack

You need a LAMP web server

These instructions use the web directory

```
/srv/www/html
```

and the web user

```
www
```

These may be different on your system

2. Install Linux packages

- `ffmpeg`
- `pandoc`
- `texlive-core`
- `imagemagick`


| **Arch/Manjaro** :$

```console
sudo pacman -S --noconfirm libxml2 xmlstarlet imagemagick ffmpeg pandoc texlive-core
```

| **Debian/Ubuntu** :$

```console
sudo apt install -y libxml2-utils xmlstarlet imagemagick ffmpeg pandoc
```

3. Create a database and user

| **Create database** : (if using terminal)

```sql
CREATE DATABASE blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON blog_db.* TO blog_db_user@localhost IDENTIFIED BY 'blogdbpassword';
FLUSH PRIVILEGES;
QUIT;
```

4. Create directories

| **Create directories** : (if using terminal)

```console
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

5. Create a `cron` task to run important updates

| **Create `cron` file to edit** :$

```console
sudo touch /etc/cron.d/webapp
sudo chmod 644 /etc/cron.d/webapp
sudo vim /etc/cron.d/webapp
```

*We can't add contents to a `cron` file using `sudo` to dump output to the file, so we must do that manually with an editor like `vim`...*

- Copy this with <kyb>Ctrl</kybd> + <kyb>C</kybd>:
  - Change `webappfolder` to the location of your web app

```console
*/15 * * * * root /usr/bin/php /srv/www/html/webappfolder/task.aggregatefetch.php.php
```
- Press:
  - <kbd>i</kbd>
  - <kyb>Ctrl</kybd> + <kyb>V</kybd>
  - <kbd>Esc</kbd>
- Type:
  - `w:` <kbd>Enter</kbd>


6. Put the contents of "cms/" into the same webfolder

| **Copy web files** : (if using terminal)

```console
sudo cp cms/* /srv/www/html/webappfolder/
sudo chown -R www:www /srv/www/html/webappfolder
```

7. Install

```console
webappfolder/install.php
```
