<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = Yii::t('app','Users list');
$this->params['breadcrumbs'][] = $this->title;
?>

<div>

	<h1><?= Html::encode($this->title) ?></h1>
	<div>
	    <?php echo $this->render('_usersfilter.php', [ 'model'=>$model]); ?>
	</div>

	<?php 

		$Columns = [
			['class' => 'yii\grid\SerialColumn'],
			[
				'label' => Yii::t('app','Username'),
				'attribute' => 'username',
				'content' => function($data){
					return Html::a($data['username'], Url::to(['edit-user', 'UserID'=>$data['id']]), []);
				}
			],
			[
				'label' => Yii::t('app','email'),
				'attribute' => 'email',
			],
			[
				'label' => Yii::t('app','Full name'),
				'attribute' => 'fullname',
			],
			[
				'label' => Yii::t('app','Oprights'),
				'content' => function($data){ return $data['oprights']; },
			],
			[
				'label' => Yii::t('app','Created'),
				'content' => function($data){
					return date("Y-m-d H:i:s", $data['created_at']);
				}
			],
			[
				'label' => Yii::t('app','Updated'),
				'content' => function($data){
					return date("Y-m-d H:i:s", $data['updated_at']);
				}
			],
		];

		// Тут надо еще проверку на разрешение удалять юзеров
		if (true){
			array_push( $Columns, 
				[
					'content' => function($data) {
						return Html::a(
							'<span class="glyphicon glyphicon-remove" style="color: red;"></span>',
							Url::to(['delete-user', 'UserID'=>$data['id']]),
							['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')]
						);
					}		
				]
			);
		}

		echo GridView::widget([
			'dataProvider' => $provider,
			'columns' => $Columns, 
		]);
	?>
</div>

<?php echo Html::a(Yii::t('app','Add user'), Url::toRoute(['users/add-new']), ['class' =>'submit btn btn-primary']); ?>



