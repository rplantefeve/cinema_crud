CREATE USER 'cinema'@'localhost' IDENTIFIED BY 'cinema';
GRANT ALL PRIVILEGES ON cinema_crud.* TO 'cinema'@'localhost';
FLUSH PRIVILEGES;
