<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Location;

$this->title = 'Locations';
$this->params['breadcrumbs'][] = $this->title;
        
?>
<div class="site-locations">
    <h1><?= Html::encode($this->title) ?></h1>

<table>
    <tr>
        <th>City</th>
        <th>State</th>
        <th>Zip</th>
    </tr>
    <?php foreach ( Location::find()->orderBy( 'state ASC, zip ASC')->each( 10 ) as $location ) : ?>
        <tr>
            <td><a href="<?php echo Url::toRoute( [ 'location', 'id' => $location->id ] ); ?>"><?php echo Html::encode( $location->city ); ?></a></td>
            <td><?php echo Html::encode( $location->state ); ?></td>
            <td><?php echo Html::encode( $location->zip ); ?></td>
        </tr>
    <?php endforeach; ?>
    
</table>

</div>
