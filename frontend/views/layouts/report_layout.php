<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use frontend\models\Tickets;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<input type="checkbox" id="rep-nav-toggle" hidden>
<div class="rep-nav">
    <label for="rep-nav-toggle" class="rep-nav-toggle" onclick=""></label>
    <h2 class="logo"> Отчеты </h2>
    <ul>
        <li><?= Html::a('Список Заявок', ['ticketslist'], []) ?></li>
        <li><?= Html::a('Отчет по выполнению заявок', ['titotals'], []) ?></li>
        <li><?= Html::a('Отчет по неработающим лифтам', ['oosnow'], []) ?></li>
        <li><?= Html::a('Отчет по повторным заявкам', ['repfailures'], []) ?></li>
        <li><?= Html::a('Отчет по поступлению заявок по дням', ['tiperday'], []) ?></li>
        <li><?= Html::a('Отчет по поступлению заявок по месяцам', ['tipermonth'], []) ?></li>
        <li><?= Html::a('Работа Аварийной Службы', ['tilas'], []) ?></li>
        <li><?= Html::a('Отчет по выполнению заявок 1562', ['titotals1562'],[]) ?></li>
        <li><?= Html::a('Список остановленных и запущенных лифтов', ['stopped-list'],[]) ?></li>
        <li><?= Html::a('Количество остановленных лифтов по районам', ['stopped-sum'],[]) ?></li>
        <li><?= Html::a('Отчет по количеству остановленных лифтов', ['stopped-count'],[]) ?></li>
        <li><?= Html::a('Отчет по ремонтам лифтов', ['repairs-list'],[]) ?></li>
        <li><?= Html::a('Перечень лифтов', ['elevators-list'],[]) ?></li>
        <li><?= Html::a('Свод заявок 1562', ['summary1562'],[]) ?></li>
        <br>
        <?php if(FALSE!==Tickets::getUserOpRights()){// Reports below intended for use by organization staff only?>
        <li><?= Html::a('Журнал экспорта в систему ИТЕРА', ['iteralog'],[]) ?></li>
        <?php } ?>

    </ul>
</div>


<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '<div ><img src="/img/logo_small.png" style="display: inline-block;">&nbsp;'.Yii::t('app','SE Kharkivgorlift').'</div>',
        'brandUrl' => Yii::$app->homeUrl,
        'brandOptions'=>[
            'style'=>"padding: 7px 1px;",
        ],
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => Yii::t('app','Home'), 'url' => ['/site/index']],
        ['label' => Yii::t('app','About'), 'url' => ['/site/about']],
        ['label' => Yii::t('app','Contact'), 'url' => ['/site/contact']],
    ];

    if (Yii::$app->user->isGuest) {
        //$menuItems[] = ['label' => Yii::t('app','Signup'), 'url' => ['/site/signup']];
        $menuItems[] = ['label' => Yii::t('app','Login'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                Yii::t('app','Logout').' (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::t('app','Intep')?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php
$this->registerCssFile('css/left-nav-style.css');
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

