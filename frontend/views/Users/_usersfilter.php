<?php  
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

  echo "<div id='usersfilter'>";
      echo '<p>';

      echo Html::beginForm(['/'.$this->context->getRoute()],'get',['class'=>'form','id'=>'UserFiltr']);
      echo '<div class="row">';

      //--- имя пользователя
      if( array_key_exists('username',$model->attributes ) ) {
        echo '<div class="form-group col-xs-3" id="mfAddr"> '.Yii::t('app','Username').":".
        Html::textinput('username', $model->username,['class'=>'form-control']).'</div>';
      }

      //--- email
      if( array_key_exists('email',$model->attributes ) ) {
        echo '<div class="form-group col-xs-3" id="mfAddr"> '.Yii::t('app','email').":".
        Html::textinput('email', $model->email,['class'=>'form-control']).'</div>';
      }

      /*
      //--- права строкой
      if( array_key_exists('oprightsstr',$model->attributes ) ) {
        echo '<div class="form-group col-xs-1" id="mfAddr"> '.Yii::t('app','Oprights').":".
        Html::textinput('oprightsstr', $model->oprightsstr,['class'=>'form-control']).'</div>';
      }
      */
      
      //--- права мультиселектом
      if( array_key_exists('oprights',$model->attributes ) ) {
        echo '<div class="form-group col-xs-3" id="mfAddr"> '.Yii::t('app','Oprights').":";
          echo   Select2Widget::widget([
            'id' => 'oprights',
            'name' => 'oprights',
            'settings' => [ 'width' => '100%', 'val' => "611" ],                 
            'items' => [
                "a" => "(a) Администратор",
                "m" => "(m) Мастер", 
                "M" => "(M) Старший мастер", 
                "d" => "(d) Оператор", 
                "D" => "(D) Диспетчер",
                "F" => "(F) Электромеханик",
                "T" => "(T) Технолог ПТО",
            ],
            'value' => $model->oprights,
    		'multiple' => true,            

          ]);           
          echo '</div>';
      }


      echo '<div class="form-group col-xs-1"> <br>'; 
      echo Html::submitButton(Yii::t('app','Choose'),['class'=>'submit btn btn-primary','id'=>'submitMeterFiltr']).'</div>';

      echo '</div>'; /* End of row*/
      echo Html::endForm();
      echo '</p>';
  echo '</div>';
?>

