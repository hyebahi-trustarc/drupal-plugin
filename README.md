services:
  drupal:
    image: drupal:10
    container_name: drupal
    ports:
      - "8080:80"
    depends_on:
      - db
    volumes:
      - drupal_data:/var/www/html
#      - /Users/haissam/Documents/GitHub/drupal:/var/www/html
    environment:
      - DRUPAL_DATABASE_HOST=db
      - DRUPAL_DATABASE_NAME=drupal
      - DRUPAL_DATABASE_USER=drupal
      - DRUPAL_DATABASE_PASSWORD=drupal
  db:
    image: mariadb
    container_name: drupal_db
    ports:
      - "3306:3306"
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: drupal
      MYSQL_USER: drupal
      MYSQL_PASSWORD: drupal
    volumes:
      - db_data:/var/lib/mysql

volumes:
  drupal_data:
  db_data:
