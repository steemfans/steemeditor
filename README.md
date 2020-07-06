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

# Deploy through docker way

* download env file

```
wget https://raw.githubusercontent.com/steemfans/steemeditor/master/.env.example
mv .env.example .env
```

* edit .env file to satisfy your environment

* pull the docker image

```
docker pull steemfans/steemeditor
```

* create a database

* migrate db

```
docker run -it --rm \
    --env-file .env \
    steemfans/steemeditor \
    php artisan migrate
```

* run the container

```
docker run -itd --name steemeditor \
    --restart always \
    -p 8080:8080 \
    --env-file .env \
    -v /data/public:/var/www/html/storage/app/public \
    -v /data/logs:/var/www/html/storage/logs \
    steemfans/steemeditor
```

* config voter

```
vim /shell/voter.sh
docker run --rm \
    -e DB_HOST=172.20.0.3 \
    -e DB_PORT=3306 \
    -e DB_USER=steem-editor \
    -e DB_PASS=123456 \
    -e STEEM_POST_KEY=your_posting_key \
    ety001/steemeditor_voter
chmod +x /shell/voter.sh
crontab -e
0 0 * * * /shell/voter.sh
```

# How to contribute

Before the stable version, coontribution is not accepted.
