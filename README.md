1. В файле .env прописать телефон, логин и пароль (SMS_PHONE, SMS_LOGIN, SMS_PASSWORD) к https://smsc.ru
2. В файле birthday прописать в 6-й строке прописать день рождения
3. В докер скрипт будет выполнен php 5.6
docker build -t birthday .
docker run --rm --env-file .env birthday