Installation | Vuexy

[#](#installation) Installation
===============================

Given below are the steps you need to follow to install the vuexy-laravel-full-version / vuexy-laravel-starter-kit on your system:


#### [#](#system-requirements) System Requirements

*   Node: LTS version
*   PHP: v8.0.2 or Above
*   Composer: v2.0.4 or Above
*   Laravel: v9.0

[#](#guide) Guide
-----------------

**Step 1:** Open the terminal in your root directory(vuexy-laravel-full-version / vuexy-laravel-starter) & to install the composer packages, run the following command:

    composer install




**Step 2:** In the root directory, you will find a file named **`.env.example` rename the given file name to `.env`** and run the following command to generate the key (and you can also edit your data base credentials here)

    php artisan key:generate




**Step 3:** By running the following command, you will be able to get all the dependencies in your node\_modules folder:

    # For Yarn
    yarn
    
    # For npm
    npm install
    
    # try with legacy option if error occurs
    npm install --legacy-peer-deps




**Step 4:** To run the project you need to run following command in the project directory. It will compile the vue files & all the other project files. If you are making any changes in any of the .vue file then you need to run the given command again.

    # For yarn
    yarn dev
    
    # For npm
    npm run dev




**Step 5:** To serve the application you need to run the following command in the project directory. (This will give you an address with port number 8000)

Now navigate to the given address you will see your application is running.

    php artisan serve


To change the port address, run the following command:

    php artisan serve --port=8080    // For port 8080
    sudo php artisan serve --port=80 // If you want to run it on port 80, you probably need to sudo.




**Watching for changes:** Running `npm run dev` every time you make changes to file is inefficient. Hopefully there's command so your changes can be watched and get reflected accordingly.

    # For yarn
    yarn watch
    
    # For npm
    npm run watch




**Building for Production:** If you want to run the project and make the build in the production mode then run the following command in the root directory, otherwise the project will continue to run in the development mode.

    # For yarn
    yarn prod
    
    # For npm
    npm run prod




**Required Permissions**

If you are facing any issues regarding the permissions, then you need to run the following command in your project directory:

    sudo chmod -R o+rw bootstrap/cache
    sudo chmod -R o+rw storage


Last Updated: 11/16/2022, 12:36:59 PM
