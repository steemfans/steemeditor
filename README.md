# Steem Editor
Steem Editor is a smart editor for steem. Its purpose is to enhance the writing experience of writers in the steem community.

# How to Build

1. Clone repo

```
git clone https://github.com/ety001/steem-editor.git
```

2. Install composer

```
cd steem-editor
composer install
```

3. Deploy DB

Before deploying , please edit your `.env` file to make sure the connection informaiton right.
And then run this command.

```
php artisan migrate
```

4. Create symbolic link to upload resouce

```
php artisan storage:link
```

5. Run server

```
php artisan serve
```

# How to develop frontend

* install dependences

```
npm install
```

* develop mode

```
npm run watch
```

* build production

```
npm run production
```

# How to contribute

Before the stable version, coontribution is not accepted.
