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
use eshark\ShadeMenu\ShadeMenu;

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

        <?php
            if (!Yii::$app->user->isGuest) {
                $reportsitms=[
                    [ 'caption' => 'Список Заявок',                             'href' => 'reports/ticketslist', ],
                    [ 'caption' => 'Отчет по выполнению заявок',                'href' => 'reports/titotals', ],
                    [ 'caption' => 'Отчет по неработающим лифтам',              'href' => 'reports/oosnow', ],
                    [ 'caption' => 'Отчет по повторным заявкам',                'href' => 'reports/repfailures', ],
                    [ 'caption' => 'Отчет по поступлению заявок по дням',       'href' => 'reports/tiperday', ],
                    [ 'caption' => 'Отчет по поступлению заявок по месяцам',    'href' => 'reports/tipermonth', ],
                    [ 'caption' => 'Работа Аварийной Службы',                   'href' => 'reports/tilas', ],
                    [ 'caption' => 'Отчет по выполнению заявок 1562',           'href' => 'reports/titotals1562', ],
                    [ 'caption' => 'Список остановленных и запущенных лифтов',  'href' => 'reports/stopped-list', ],
                    [ 'caption' => 'Количество остановленных лифтов по районам','href' => 'reports/stopped-sum', ],
                    [ 'caption' => 'Отчет по количеству остановленных лифтов',  'href' => 'reports/stopped-count', ],
                ];
                if(FALSE!==Tickets::getUserOpRights())  
                    $reportsitms[] =  [ 'caption'=>'Журнал экспорта в систему ИТЕРА',   'href'=>'reports/iteralog', ];

                $menuitms = [
                    [ 'caption' => "Главная", 'href' => "site/index", ]
                ];

                if( 
                    (FALSE !== strpos($uoprights['oprights'],'D')) || 
                    (FALSE !== strpos($uoprights['oprights'],'d')) ||
                    (FALSE !== strpos($uoprights['oprights'],'M')) ||
                    (FALSE !== strpos($uoprights['oprights'],'F')) ){
                    $menuitms[] = [ 'caption' => YII::t('app','Tickets'), 'href' => "tickets/index", ];
                }

                if( FALSE !== strpos($uoprights['oprights'],'D' ) ) {
                    $menuitms[] = [ 'caption' => YII::t('app','Ticket input'), 'href' => "ticket-input/inputform", ];
                } 

                if( FALSE === strpos($uoprights['oprights'],'F' ) ) {
                    $menuitms[] = [ 'caption' => YII::t('app','Reports'), 'items' => $reportsitms, ];
                    $menuitms[] = [ 'caption' => YII::t('app','Map'),     'href' => "maps/index", ];
                }

                echo ShadeMenu::widget([
                    'caption'=>"МЕНЮ",
                    'items'=>$menuitms,
                    'options'=>[],
                ]);
            }
        ?>
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
