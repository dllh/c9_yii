<?php

/* @var $this yii\web\View */

// Load a helper for outputting html safely.
use yii\helpers\Html;

// Load our Location model, which fetches the data for us.
use app\models\Location;

// Get our query string parameters from the url.
$request = Yii::$app->request;

// The "get" here doesn't mean simply to get the data; it means to get data
// submitted via the GET request method, which will include things added to
// the url after a question mark. So for example if the url contains something
// like /location?id=2, then the value we're fetching here will be an array
// with a key "id" with the value of 2. We'll send this to our model to tell it
// which row to give us back from the database.
$user_input = $request->get();

// Find a record based on the query string parameter named "id".
// TODO: What if we get an invalid id or can't find a record?
$location = Location::findOne( $user_input['id'] );

// Add the city to the page title, and add "Locations" to the breadcrumb nav.
$this->title = 'Locations - ' . $location->city;
$this->params['breadcrumbs'][] = 'Locations';
        
?>
<div class="site-locations">
    <h1><?= Html::encode($location->city ) ?></h1>
    <p><strong>State: </strong><?= Html::encode($location->state ) ?></p>
    <p><strong>Zip: </strong><?= Html::encode($location->zip ) ?></p>
</div>
