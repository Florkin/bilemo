# Bilemo API
## Install project

1) Clone repository
2) In project folder, executes ``composer install``
3) In .env (or .env.local), configure your database access
4) Executes these 3 commands 
``$ mkdir -p config/jwt``  
``$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096``  
``$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout``   

5) Now, you have to copy the choosen passphrase in .env, or.env.local like this: ``JWT_PASSPHRASE="YOUR_PASSPHRASE"``
6) Create and fill database with:  
``php bin/console doctrine:database:create``  
``php bin/console doctrine:fixtures:load``

7) To access admin panel, go to: /manager  
``email: "admin@demo.fr"
pass: "demodemo"``  
Don't forget to build the assets:   
``yarn install``, ``yarn encore production``

8) To test API as a client:  
``email: "client@demo.fr"
pass: "demodemo"``

Api documentation is generated on : /api/doc
