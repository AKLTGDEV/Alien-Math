php artisan cleanup
sudo php artisan migrate:refresh
php artisan db:seed --class=TagsSeeder
php artisan db:seed --class=UserSeeder
php artisan relations
php artisan db:seed --class=PostSeeder
php artisan db:seed --class=WSSeeder
mkdir storage/app/indices
php artisan searchindex
sudo chmod 777 storage/app/indices -R
sudo php artisan answers
sudo php artisan wsanswers
sudo chmod 777 storage/ -R