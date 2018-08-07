<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

class UsersList extends Model
{
	public $username;
	public $fullname;
	public $oprights;
	public $oprightsstr;
	public $email;

	public static function FillFilterParams( &$model, $params)
	{
		$filtersql = "";

		if (!empty($params['username'])) {
			$model->username  =  $params['username'];
			$filtersql	.=" and username like '%".$model->username."%'";
		}

		if (!empty($params['email'])) {
			$model->email  =  $params['email'];
			$filtersql	.=" and email like '%".$model->email."%'";
		}

		if (!empty($params['oprightsstr'])) {
			$model->oprightsstr  =  $params['oprightsstr'];
			$litters = str_split($model->oprightsstr,1);
			$ofltr="";
			foreach($litters as $ch){
				$ofltr .= " or (oprights like BINARY '%".$ch."%')";
			}
			if (!empty($ofltr))
				$filtersql .= " and (false ".$ofltr.")";

		//Yii::warning("***********************************oprights*********************************************[\n".$model->oprights."\n]");
		//Yii::warning("************************************************litters***********************[\n".json_encode($litters)."\n]");
		//Yii::warning("***********************************filtersql*********************************************[\n".$filtersql."\n]");

			//$filtersql	.=" and oprights like BINARY '%".$model->oprights."%'";
		}

		if (!empty($params['oprights'])) {
			$model->oprights  =  $params['oprights'];
			$ofltr="";
			foreach($model->oprights as $ch){
				$ofltr .= " or (oprights like BINARY '%".$ch."%')";
			}
			if (!empty($ofltr))
				$filtersql .= " and (false ".$ofltr.")";

		//Yii::warning("***********************************oprights*********************************************[\n".$model->oprights."\n]");
		//Yii::warning("************************************************litters***********************[\n".json_encode($litters)."\n]");
		//Yii::warning("***********************************filtersql*********************************************[\n".$filtersql."\n]");

		}

		return $filtersql;
	}

	public function GetUsersList($filter="")
	{

		//$filter = UsersList::FillFilterParams($this, Yii::$app->request->queryParams);
		$sqltext = "SELECT u.*, e.id AS eid, concat(e.lastname,' ', e.firstname,' ', e.patronymic) as fullname, e.oprights FROM user u left join employee e on e.user_id = u.id";
		$sqltext .=" where u.id>0 ".$filter;
		//Yii::warning("********************************************************************************[\n".$sqltext."\n]");
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'username',
					'fullname',
				],
				//'defaultOrder' => [ 'username' => SORT_ASC ],
			],
		]);
		return $provider;		
	}

	public static function DeleteUser($UserId)
	{
		Yii::$app->db->createCommand()->delete('user','id='.$UserId)->execute();
	}

}