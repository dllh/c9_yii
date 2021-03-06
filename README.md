## Set up a database in Cloud 9

In the terminal:

    mysql-ctl start
    mysql -u root
    
Now you're in the mysql app. Do these things (substitute your preferred database
name for "yii_app" and username for "yii" in "yii@localhost" and password after
"identified by." Keep your values for later use in config files in the web app.

    create database yii_app;
    grant all privileges on yii_app.* to yii@localhost identified by 'JDJES88skejj2h!lsjkelbbvflub';
    flush privileges;

--------------------------------------------------------------------------------

## Install Yii

In the terminal (exit out of mysql if needed):

    composer global require "fxp/composer-asset-plugin:^1.3.1"

Now create a basic Yii app:

    composer create-project --prefer-dist yiisoft/yii2-app-basic basic
    
This will add a bunch of files to your file browser in Cloud 9 in a folder
named "basic".

Update config so that your app can see your database by editing
basic/config/db.php and substituting the database name, user, and password
created above in for the default values.

If you go into "basic/web/index.php" and Run that file within C9, it'll start
the web server and output to the console the url to view your site. For me,
it looks like this:

    https://dllh-yii-dllh.c9users.io/basic/web/index.php

Open that in your browser, and you should see a basic web page.

--------------------------------------------------------------------------------

## Make a Controller

A controller maps a url to the code that will cause an action to be performed.
In Yii, controllers are in the controllers directory, and the default is
SiteController.php. Edit this and add something like the following:

    public function actionLocations()
    {
        return $this->render('locations');
    }

This will cause the "locations" route to render a view named "locations."

--------------------------------------------------------------------------------

## Make a View

Views are the code that actually output data to the screen. In Yii, they're in
the "views" folder. Within that folder, you'll see a "layouts" directory 
containing "main.php" -- this is the overall template for the site, and for 
anything that should be pretty consistent across pages, you'll want to change
this file, so that you change things in only one place.

The "views/site" folder contains individual views. Duplicate the "about.php"
file and name it (my example, but use what you'd like) "locations.php".
Now edit it and change some of the text, the title, etc.

The view now exists, and the controller knows how to render it. Let's add it
to our navigation.

--------------------------------------------------------------------------------

## Editing Site Navigation

Open views/layouts/main.php and look for the "Nav::widget" section. This is a 
data structure that Yii has set up to define the navigation, and it's an array
of expressions that can also be arrays that follow an expected format. This is
simply a convention of Yii and not a php-specific thing.

To add a nav item to my locations view, I added this as an element in the array
after the About item that already exists there:

    ['label' => 'Locations', 'url' => [ '/site/locations']],
    
This tells Yii to add an item with the label "Locations" that when clicked will
load the /site/locations route. This route is handled by the controller we 
set up above, which in turn tells Yii to render the locations view we set up.

--------------------------------------------------------------------------------

## Do Something with the Database

What we've done so far is make a static page, but we'll want eventually to pull
some data out of a database.

First, we need to switch to the database we created above:

    USE yii_app;

Next, let's make a sample table. Get back to your terminal and into the mysql
app (type "mysql -u root" if you're not already there). Then paste this:

    CREATE TABLE locations ( 
        id int(10) unsigned zerofill not null primary key auto_increment, 
        city varchar(100), 
        state varchar(25), 
        zip varchar(10) 
    );

This creates a database table with a numeric id and city, state, and zip fields.
Next you can add some sample data:

    INSERT INTO locations (city, state, zip ) VALUES 
        ('Knoxville', 'Tennessee', '37931' ), 
        ( 'Gastonia', 'North Carolina', '28056' ), 
        ( 'Somewhere', 'Somewhere', '12345' );

Now query your table to see the data:

    SELECT * FROM locations;

Now that we have some data in the database, we want to display it in a view.
This requires that we make a model first, though. A model is the code that 
defines methods for interacting with our data (fetching, saving, etc.). Ours 
will be very simple to start.

--------------------------------------------------------------------------------

## Our First Model

Under the models directory, make a file named [Location.php](https://github.com/dllh/c9_yii/blob/master/basic/models/Location.php) and populate it 
like so:

    ```<?php
    
    namespace app\models;
    use yii\db\ActiveRecord;
    
    class Location extends ActiveRecord
    {
     
        public static function tableName()
        {
            return '{{locations}}';
        }
    
    }```

Models can be a lot more complex than this, but for now, we're just telling our
app that we want to define a model for a specific type of data and we're telling
it which database table to fetch the data from. This is all basically a sort of
glue.

ActiveRecord is a standard way of interacting with databases, and it all works
sort of behind the scenes. Because this model declares that it is using
ActiveRecord, we automatically inherit a bunch of neat methods we can use
in our view. For example, below, you'll see methods named find() and orderBy()
that are part of ActiveRecord, which we get as part of the Yii install.
This keeps us from having to rewrite fetch/save type methods
over and over per type of data we want to use.

--------------------------------------------------------------------------------

## Updating our View

We defined a simple Location view earlier, but now we want it to use our model
to output some data from the database. Take a look at my sample view [here](https://github.com/dllh/c9_yii/commit/79def7d2dee32c2ec9132fad0e0df950af5e835b#diff-bec7f5bf51271dc149c861a56a5c819a).

I've left comments in the view that explain what all it does. If you use this
in place of the simpler view we created above and load the page again, you 
should see a list of the location data that we added to the database.

It's unsorted, though. Let's say we want to order by state and then by city.
We could update our find for the data to something like this:

    Location::find()->orderBy( 'state ASC, zip ASC')

--------------------------------------------------------------------------------

# More Advanced Queries

So far, we've done a simple query for 10 locations ordered by state and city.
But say we wanted to find a specific record by id (e.g. if somebody clicks an
item in a list) to drill into more details.

We'll need a controller and a view to display the data. The view will in turn
do a different query to return the subset of data we want.

Step one is to edit controllers/SiteController.php to add a route to render
our view:

    public function actionLocation()
    {
        return $this->render('one-location');
    }
    
Next we'll duplicate views/site/locations.php and name it one-location.php. We
can name these whatever we like, as long as the parameter passed to the render()
function in our controller matches the file name we use when creating the view.
Duplicating the view isn't enough -- we need to edit it now to let us limit
what it displays. [Here's](https://github.com/dllh/c9_yii/commit/79def7d2dee32c2ec9132fad0e0df950af5e835b#diff-110bc0def42ccadde647bd78e780e8e1) my view for showing a single location.

Now we want to make the location listing page link to the new view.

I've done this by editing the "city" line in the locations view to look like
this:

```<td><a href="<?php echo Url::toRoute( [ 'location', 'id' => $location->id ] ); ?>"><?php echo Html::encode( $location->city ); ?></a></td>```

Note that I also added this near the top of the file, to load the Url helper
function that I'm using to define the route that the link uses:

    use yii\helpers\Url;

--------------------------------------------------------------------------------

## Summary So Far

1. We've set up Yii, which is a nice, fairly simple framework for building web
   apps.
2. We've set up a basic database in our mysql server.
3. We've created a model using Yii's built-in ActiveRecord features to let us 
   access data that lives in the mysql server.
4. We've set up controllers that will route requests for a location listing
   and a single location detail page to views to display them.
5. We've set up those two views to use our models to access the data in our 
   database and output the data.
   
In short, we've used the model-view-controller (MVC) pattern to create a very
simple read-only web app. You can go through the steps above for each type of
data you need to display, or you can create controllers and views for any static
pages that don't need data from the database (you won't need models for these).

Next topics to explore include using more complex data structures (e.g. mapping
location data to user ids, and moving authentication data into a database 
rather than having that model be a static data array) and collecting and storing
user-submitted data.

--------------------------------------------------------------------------------

## Saving a Model

You might want not merely to list data, but actually to save changes to it.
This requires a couple of steps. For one, we need to update our controller so 
that it knows how to get and save info about the model. [Here's](https://github.com/dllh/c9_yii/commit/e4e90a7a83d655d5f9206a814df2e98d415af671#diff-d50f7ecbede1ea9d64c6297b2fe716aa) my update.

Note that the model code is a little finicky about how you instantiate its
object -- if you instantiate a `new` Location model, you'll run into difficulty
saving the data. So I look for data in the HTTP POST and if found, I find the
existing model using that id. Else I look for the model using the id in the HTTP
GET data. Once I've got the model, I can save or just populate the $model object,
which I then pass along to the view for rendering.

Second, we need to update our view to add form fields instead of just displaying
the model's data. My change looks like [this](https://github.com/dllh/c9_yii/commit/e4e90a7a83d655d5f9206a814df2e98d415af671#diff-110bc0def42ccadde647bd78e780e8e1).

Of course, this is terrible in practice! With that change, anybody can come 
in and edit any old location, with no authentication and very little validation.
Ideally, we would probably show the form only for admins and show a read-only
view for others. Or we would have the form in an explicit admin area. So this
is a first iteration that we'd want to build on in the future. :)

In the view changes, I introduce Yii's `ActiveForm` API, which saves us some
hassle around building forms. The API also has some security features built in
to help safeguard against what are called CSRF exploits. These are moot given
our own lax security so far, but it's nice to know that there's some built in
security here to back us up once we tighten our own security.

Also of note in this set of changes, I've [added](https://github.com/dllh/c9_yii/commit/e4e90a7a83d655d5f9206a814df2e98d415af671#diff-8aef38d2ae87d638b3cc61f6ecedd664) some very light data validation
to my model. This integrates smoothly with Yii's built-in form validation, 
so if you try submitting blank data, you'll see a nice form error, for example.
You can do more complex validation (right now we're checking only for existence
of form fields).

--------------------------------------------------------------------------------

## Using a Database for Authentication

Let's use an existing library: 
https://github.com/dektrium/yii2-user/blob/master/docs/README.md

To set it up, in the terminal, do these two things (make sure you are in the 
directory that contains all your site files -- if you used the steps above to
set your site up, this'll be named `basic`. Else the files that this process
downloads will be in the wrong place and your database won't be created.

    composer require dektrium/yii2-user
    php yii migrate/up --migrationPath=@vendor/dektrium/yii2-user/migrations

The first downloads the module into your web app and the second sets up the
database tables required to handle authentication.

To enable the new authentication module, edit config/web.php to remove the
'components => user' section and to add this new property:

    'modules' => [
      'user' => [
            'class' => 'dektrium\user\Module',
            'enableConfirmation' => false,
        ],
    ],

Note that we've disabled email confirmation, which is tricky on Cloud9.

Now let's hook our main view into the new login pages that this module adds.
Edit views/layouts/main and update the relevant bits like so:

    ['label' => 'Login', 'url' => ['/user/security/login']]
    ['label' => 'Logout', 'url' => ['/user/security/logout']]

Now you've got full login ability, with a few new user tables added. The one
you'll be most interested in going forward is likely to be `user`, which you'll
join to other tables should you decide to save any user-specific data. So for 
example, if you were to want to save location data for a given user, you would
map the location id to the user id. We'll get to that in more detail later if 
needed.

--------------------------------------------------------------------------------

## Using the Materialize Framework

Start [here](https://github.com/MacGyer/yii2-materializecss). Basic steps
within your site directory (probably named `basic` if you followed the site 
setup above) follow. In the terminal:

    composer global require "fxp/composer-asset-plugin:~1.2"
    
Now open composer.json and add this line as a new array item in the `require` key:

    "macgyer/yii2-materializecss": "*"

Back in the terminal, run this:

    composer update
    
This installs some files needed to integrate Materialize into your Yii site.

Finally, update `assets/AppAsset.php` to add this line to the $depends array:

    'macgyer\yii2materializecss\assets\MaterializeAsset'
    
Now if you load your site, it'll have a more Materializey look, and a number
of Materialize components will be available to you. More docs [here](https://github.com/MacGyer/yii2-materializecss).