IF NOT EXIST c:\xampp\mysql\data\lcheadend (
    c:\xampp\mysql\bin\mysql.exe -uroot < lcheadend_v1.0.0.sql
    c:\xampp\mysql\bin\mysql.exe -uroot -e "CREATE USER 'lcheadend_user'@'localhost' IDENTIFIED BY 'a1a9998499c99f17caf84603c2977804';"
    c:\xampp\mysql\bin\mysql.exe -uroot -e "GRANT ALL PRIVILEGES ON lcheadend.* TO 'lcheadend_user'@'localhost';"
)

c:\xampp\php\php.exe c:\xampp\htdocs\lcheadend\protected\yiic migrate --interactive=0