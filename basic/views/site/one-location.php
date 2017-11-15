<?php

/* @var $this yii\web\View */

// Load a helper for outputting html safely.
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Add the city to the page title, and add "Locations" to the breadcrumb nav.
$this->title = 'Locations - ' . $model->city;
$this->params['breadcrumbs'][] = 'Locations';

$form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
]) ?>

<div class="site-location">
    <h1><?= Html::encode($model->city ) ?></h1>
    <?php if (Yii::$app->session->hasFlash('locationFormSubmitted')): ?>
        <div>Saved!</div>
    <?php endif; ?>

        
    <?= Html::activeHiddenInput( $model, 'id' ) ?>
    <?= Html::activeHiddenInput( $model, 'city' ) ?>
        
    <div class=".input-field">
        <?= $form->field($model, 'state') ?>
    </div>
        
    <div class=".input-field">
        <?= $form->field($model, 'zip') ?>
    </div>
        
    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    
</div>

<?php ActiveForm::end() ?>