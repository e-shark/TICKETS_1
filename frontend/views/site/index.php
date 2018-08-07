<?php
use frontend\models\Tickets;

/* @var $this yii\web\View */

$this->title = 'ОЗК ОДС КСП "Харьковгорлифт"';

?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?=Yii::t('app','System for Field Service Management for Elevators Repair')?></h1>

        <p class="lead">__________</p>

        <?php $uoprights=Tickets::getUserOpRights();?>
        <?php if (Yii::$app->user->isGuest) {?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=site/login"><?=YII::t('app','Login')?></a></p>
        <?php } else { 
            if( 
                (FALSE !== strpos($uoprights['oprights'],'D')) || 
                (FALSE !== strpos($uoprights['oprights'],'d')) ||
                (FALSE !== strpos($uoprights['oprights'],'M')) ||
                (FALSE !== strpos($uoprights['oprights'],'F')) ){?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=tickets/index"><?=YII::t('app','Tickets')?></a></p>
            <?php }  if( FALSE !== strpos($uoprights['oprights'],'D' ) ) {?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=ticket-input/inputform"><?=YII::t('app','Ticket input')?></a></p>
            <?php } if( FALSE === strpos($uoprights['oprights'],'F' ) ) {?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=reports/index"><?=YII::t('app','Reports')?></a></p>
        <p><a class="btn btn-lg btn-success" href="index.php?r=maps/index"><?=YII::t('app','Map')?></a></p>
        <p><a class="btn btn-lg btn-success" href="index.php?r=meter/meter/index"><?=YII::t('app','Meters')?></a></p>
        <p><a class="btn btn-lg btn-success" href="index.php?r=meter/meter/fitter-meters-list"><?=YII::t('app','Meters')." механика"?></a></p>
            <?php }?>
        <p><a class="btn btn-lg btn-success" href="index.php?r=users/index"><?=YII::t('app','Users list')?></a></p>

        <?php }?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>О комплексе</h2>

                <p>Оперативно-заявочный комплекс КСП "Харьковгорлифт" предназначен для автоматизации процессов регистрации, выполнения, мониторинга выполнения и закрытия заявок на выполнение работ по оперативному ремонту и техническому обслуживанию лифтов, а также получение отчетной информации</p>

                <p><a class="btn btn-default" href="#">Помощь &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Пользователи</h2>

                <p>Комплекс предназначен для использования диспетчерами ОДС КСП "Харьковгорлифт", старшими мастерами, мастерами, электротехническим персоналом линейных участков, сотрудниками ЛАС, сотрудниками специализированных участков, сотрудиками отдела ОМТС, руководящим персоналом предприятия</p>
                <?php 
                    if( FALSE != $uoprights ) {
                        $oplog='https://docs.google.com/spreadsheets/d/1F4deeSJm4jbWTxPZ0WfBpgP3QA6_-nSB-y0q4dWlryI/edit?usp=sharing';
                        $expar='target="_blank"';
                    } else $oplog='#';
                    echo '<p><a '.$expar.' class="btn btn-default" href='.$oplog.'>Журнал сопровождения эксплуатации &raquo;</a></p>';
                ?>

                
            </div>
            <div class="col-lg-4">
                <h2>Смежные системы</h2>

                <p>Комплекс предназначен для обслуживания заявок на выполнение работ по оперативному ремонту и обслуживанию лифтов на основании: вызовов, поступивших в ОДС КСП "Харьковгорлифт" с панелей ГГС установленных в лифтах, обращений граждан, поступивших по телефону, а также заявок, поступивших из системы 1562  </p>

                <p><a class="btn btn-default" href="#">Смежные системы &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
