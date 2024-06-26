# Traverise Developer Assessment

This is a stripped-down version of Traverise that serve to assess your Laravel knowledge. Before you begin the assessment, please do the following steps:

Setup Traverise:
- Clone / Copy the folder to your webserver folder.
- Make sure bootstrap/cache folder is present and writable
- cd to the root folder where you copied the project
- Rename .env.example to .env
- Run "composer update"
- Run "php artisan key:generate"
- Edit the .env content according to your environment
- Setup the database
- Run "php artisan migrate:fresh --seed"
- Run "php artisan queue:work"
- cd to the root folder again, then run "npm i" to install assets dependencies
- Run "npm run production" to generate the assets

### Creating tenant

- Default tenant named "demo" should be added automatically when you run the migration
- Add entry in your hosts file with the domain you just added as tenant (e.g if you add tenant with a domain called **demo** then add the following hosts entry)

`127.0.0.1 demo.traverise.test`

### Setting up tenant

- Login to your tenant dashboard by going to https://demo.traverise.test/dashboard
- Login with the credentials: admin@traverise.com and password: password
- Finished. You can start with the assessment below.

### Assessment

1. Booking > Add Discount is not working, after submitting the form, it just reloads the page but no discount is added.
2. Implement the blacklist feature in /Controllers/Booking/IndexController.php line 1508 so when guest with blacklisted email submit a booking, the booking will be set to Pending instead of Confirmed.
3. Bookings > Advanced Search. When searching by email address, it doesn't matter what you enter it will always show all results. Check in /Services/Booking/BookingService.php filterBookings function
4. Bookings > Advanced Search. Searching by Stay dates is not working too. Check in /Services/Booking/BookingService.php filterBookings function
5. For this assessment, please create 2 bookings, 1 for Double Room 1 and 1 for Double Room 2. the first booking stay date should be 05.08.2024 to 09.08.2024, the second booking stay date should be 09.08.2024 to 14.08.2024. both bookings should have a private room option checked. Check the calendar and you should have similiar to this
![](https://helloangga.com/assessment1.jpg)
Now add a 3rd booking, and choose the stay date from 05.08.2024 to 14.08.2024 so you should see something like this
![](https://helloangga.com/assessment2.jpg)
The task is to fix the code so the checkbox and the + button on Double Room 1 is greyed out like on the Double Room 2 because the requested dates is fully booked and user should not be able to click either one

### How to submit your answer

You may send your compressed source code (zip/rar/tar.gz) or you may also create a pull request to this repository


Good luck!

Prepared for Traverise applicants only. Do not reproduce document elsewhere