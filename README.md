# CryptoFolio installation
* Install php 7.4, apache, mySQL, composer, redis, supervisor
* Bind a domain to apache, set up SSL keys
* Create a new user in OS (without root rights)
* Installation
  * Switch to user: `su - NEW_USER`
  * Clone the repo (in the home directory): `git clone https://github.com/Arodnk88/CryptoFolio.git`
  * Modify apache `DocumentRoot` to the folder `public` in the cloned repository
  * Fix access rights: `sudo chown -R NEW_USER:www-data /home/NEW_USER/CryptoFolio/storage`
  * Change directory to the repo and copy config from example:
    ```cd /home/NEW_USER/CryptoFolio
    cp .env.example .env
    ```
  * Install composer: `composer install`
  * And generate keys: `php artisan key:generate`
  * Edit config file `.env` and setup database and Jabber account in this varriables:
    ```DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=

    JABBER_HOST=
    JABBER_PORT=
    JABBER_USERNAME=
    JABBER_PASSWORD=
    JABBER_RESOURCE="CF"
    JABBER_USE_TLS=true
    JABBER_LOG=false
    ```
  * Migrate DB: `php artisan migrate`
  * Create Admin user: `php artisan tinker` and type
    ```$user = new User([
      'login' => 'ADMIN_LOGIN',
      'password' => Hash::make('ADMIN_PASS'),
      'user_id' => Str::uuid(),
      'is_active' => true,
      'is_admin' => true,
      'api_token' => Str::uuid(),
    ])
    $user->save()
    ```
  * If there is no error on the site write `APP_DEBUG=false` to `.env` file
  * Run `php artisan config:clear`
  * Run  `crontab -e` and add an entry: `* * * * * cd /home/NEW_USER/CryptoFolio && php artisan schedule:run >> /dev/null 2>&1`
  * Configure supervisor `sudo nano /etc/supervisor/conf.d/jabber.conf`:
    ```[program:jabber]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/NEW_USER/CryptoFolio/artisan jabber:start
    autostart=true
    autorestart=true
    redirect_stderr=true
    user=www-data
    stdout_logfile=/home/NEW_USER/CryptoFolio/storage/jabber.log
    stdout_logfile_maxbytes=10MB
    logfile_backups=10
    ```
  * Reload supervisor: `supervisorctl reread && supervisorctl update`
