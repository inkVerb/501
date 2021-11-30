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
sudo mkdir -p media/docs media/audio media/video media/images media/uploads media/original/images media/original/video media/original/audio media/original/docs media/profiles
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
media/profiles
```

5. Put the contents of "cms/" into the same webfolder

| **Copy web files** : (if using terminal)

```console
sudo cp cms/* /srv/www/html/webappfolder/
sudo chown -R www:www /srv/www/html/webappfolder
```

6. Install

```console
webappfolder/install.php
```

