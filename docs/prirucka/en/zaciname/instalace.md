# Installation

Installation takes a few minutes and has four steps on a single screen.

## 1. Upload the files

1. Download the latest release – the file `phprs-X.Y.Z.zip`.
2. Unpack it and upload its **contents** to the site folder on your hosting. The target folder must directly contain `index.php`, `admin.php`, `install.php` and the folders `system/`, `layout/`, `image/`, `media/`, `storage/` – not another nested folder.
3. Check that the **hidden `.htaccess` files** were uploaded too: one is in the root, others are in the folders `system/`, `storage/` and `media/`. Some FTP programs do not show or transfer hidden files by default. Without them, files that must not be public would be readable from the internet.

On a server running nginx, `.htaccess` files have no effect – set up the rules as described in [Operations → nginx](../provoz/nginx.md).

## 2. Open the installer

In your browser, open the address `https://www.example.com/install.php`. The installer appears in the language of your browser; in the top right corner you can switch it to Czech, Slovak, English or German.
The language you choose is also set as the site language, the administration language of your account and the language of the sample content.

### Step 1 – Server check

The installer checks the PHP version, the required extensions and write access to the root folder and to `storage/`. Items marked with a cross must be fixed on the hosting; then reload the page.

### Step 2 – Database

Fill in the details of the empty database. **Server** is usually `localhost`, but many hosting services use their own address – you will find it in the hosting control panel next to the database.
Change the **Table prefix** (`rs_`) only if several installations will run in the same database.

### Step 3 – Site and administrator

- **Site name** – shown in the header and in search engines; you can change it later in Settings.
- **Username** and **Password** of the first account. The password must be at least 10 characters long; choose a long and unique one, because this account may do everything on the site.
- **Full name** – shown with articles.
- **E-mail** – becomes the newsroom e-mail: system notifications are sent to it.
- **Time zone** – scheduled articles are published and dates are shown according to it.
- **Load sample content** – optional. Instead of a single welcome article the site gets five sections, ten articles and images of a fictional daily in the language of the installation (a Slovak installation gets Czech), so you can see right away what the template looks like with content. Everything is made up and free to use. You can delete the sample later with one click in **Settings → General → Sample content**; it can also be loaded there afterwards.

### Step 4 – Site template

Choose one of the three built-in templates: Classic Newspaper (a daily), Modern Magazine (a bold magazine) or Minimal (a blog or personal magazine). You can change it at any time later in **Appearance → Site identity** without losing any content.

The **Install phpRS 3** button creates the database tables and the `config.php` file with the database access details.

## 3. After installation

1. The installer **deletes itself** when it finishes. If the server permissions do not allow it, it says so – then delete the `install.php` file by hand; as long as it is there, System status keeps reminding you.
2. Sign in at `https://www.example.com/admin.php`.
3. Turn on **Two-factor sign-in**: clicking your avatar in the top right corner opens **My account**, section **Two-factor sign-in**.
4. Continue with the chapter [First steps](prvni-kroky.md).

## If the installation fails

- **“Could not connect to the database”** – most often a wrong server address or password. Copy the details from the hosting control panel; do not retype them by hand.
- **“Tables with this prefix already exist in the database”** – the database is not empty. Choose another prefix, or remove the old tables.
- **“The tables were created, but config.php could not be written”** – the root folder of the site is not writable. Fix the permissions and run the installation again with a different table prefix, or delete the tables first.
- **Blank page or error 500** – the hosting is probably running an older PHP version. Switch it to 8.4 or newer.
