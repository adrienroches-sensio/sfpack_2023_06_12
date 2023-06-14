Requirements
============

1. [symfony-cli](https://symfony.com/download)
2. PHP >= 8.1
3. pdo_sqlite
4. composer
5. yarn

Install
=======

1. clone the repository
2. Create a `.env.local` with the following :
```dotenv
# Get yours here : https://www.omdbapi.com/apikey.aspx
OMDB_API_KEY=xxxx 
```
3. Run the following commands :

```bash
$ symfony composer install
$ yarn install
$ yarn dev
$ symfony console doctrine:migration:migrate -n
$ ./fixtures.sh
$ symfony serve -d 
```
