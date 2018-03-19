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
use components\ShadeMenu\ShadeMenu;

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

<?php $uoprights=Tickets::getUserOpRights();?>
<?php if (!Yii::$app->user->isGuest) {?>

    <?php // Навигационная панель ?>
    <input type="checkbox" id="rep-nav-toggle" hidden>
    <div class="rep-nav">
        <label for="rep-nav-toggle" class="rep-nav-toggle" onclick=""></label>


        <h2 class="logo"> МЕНЮ </h2>
        <ul>
            <?php
            if( 
                (FALSE !== strpos($uoprights['oprights'],'D')) || 
                (FALSE !== strpos($uoprights['oprights'],'d')) ||
                (FALSE !== strpos($uoprights['oprights'],'M')) ||
                (FALSE !== strpos($uoprights['oprights'],'F')) ){
                echo "<li>".Html::a(YII::t('app','Tickets'), ["tickets/index"],[])."</li>";
            }

            if( FALSE !== strpos($uoprights['oprights'],'D' ) ) {
                echo "<li>".Html::a(YII::t('app','Ticket input'), ["ticket-input/inputform"], [])."</li>";
            } 

            if( FALSE === strpos($uoprights['oprights'],'F' ) ) {  ?>
                <li>
                    <input type="checkbox" id="group-1" checked hidden>
                    <label for="group-1"><?=YII::t('app','Reports')?><i></i></label>
                    <ul>
                        <li><?= Html::a('Список Заявок', ['reports/ticketslist'], []) ?></li>
                        <li><?= Html::a('Отчет по выполнению заявок', ['reports/titotals'], []) ?></li>
                        <li><?= Html::a('Отчет по неработающим лифтам', ['reports/oosnow'], []) ?></li>
                        <li><?= Html::a('Отчет по повторным заявкам', ['reports/repfailures'], []) ?></li>
                        <li><?= Html::a('Отчет по поступлению заявок по дням', ['reports/tiperday'], []) ?></li>
                        <li><?= Html::a('Отчет по поступлению заявок по месяцам', ['reports/tipermonth'], []) ?></li>
                        <li><?= Html::a('Работа Аварийной Службы', ['reports/tilas'], []) ?></li>
                        <li><?= Html::a('Отчет по выполнению заявок 1562', ['reports/titotals1562'],[]) ?></li>
                        <li><?= Html::a('Список остановленных и запущенных лифтов', ['reports/stopped-list'],[]) ?></li>
                        <li><?= Html::a('Количество остановленных лифтов по районам', ['reports/stopped-sum'],[]) ?></li>
                        <li><?= Html::a('Отчет по количеству остановленных лифтов', ['reports/stopped-count'],[]) ?></li>
                        <?php if(FALSE!==Tickets::getUserOpRights()){// Reports below intended for use by organization staff only?>
                        <li><?= Html::a('Журнал экспорта в систему ИТЕРА', ['reports/iteralog'],[]) ?></li>
                        <?php } ?>
                    </ul>
                </li>
                <li><?= Html::a(YII::t('app','Map'), ["maps/index"],[]) ?></li>
            <?php }   ?>

        </ul>
    </div>
    <?php $this->registerCssFile('css/left-nav-style.css'); ?>

<?php }?>

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

<br>--------------<br>
<?php
echo ShadeMenu::widget([
    'caption'=>"МЕНЮ",
    'items'=>[
        ['caption'=>'Пункт 1',
         'href'=>'#1'
        ],
        ['caption'=>'Пункт 2',
         'items'=>[
                ['caption'=>'Пункт 21',
                 'href'=>'#21'
                ],
                ['caption'=>'Пункт 22',
                 'href'=>'#22'
                ],
            ],
        ],
        ['caption'=>'Пункт 3',
         'href'=>'#3'
        ],
    ],
    'options'=>[],
]);
?>
<br>--------------<br>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::t('app','Intep')?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
