<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "occupation".
 *
 * @property int $id
 * @property string $occupationname
 * @property string $occupationcode
 *
 * @property Employee[] $employees
 */
class Occupation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'occupation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['occupationname'], 'string', 'max' => 100],
            [['occupationcode'], 'string', 'max' => 10],
            [['occupationname'],'required', 'message' => 'Поле обязательно для заполнения'],
            ['occupationname', 'match', 'pattern' => '/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюя АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ-]+$/', 
            'message' => 'Можно использовать только русские буквы'],
            //[['occupationname'], 'match', 'pattern' => '/^[а-яА-Я]+$/', //-плохо работает
            //'message' => 'Можно использовать только русские буквы'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            [['occupationname'],'required'],
            'occupationname' => 'Должность',//'Occupationname',
            'occupationcode' => 'Код Должности',//'Occupationcode',
            'employee_oc'=>'Фамилия Имя Отчество',
            'division_oc'=>'Название Подразделения',
            'occupationemployee'=>'Человек На Должности'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['occupation_id' => 'id']);
    }


    public function getUrl()
    {
        $id=$this->id;
        $url = Url::toRoute(['view','EmployeeSearch[occupation_id]'=>$id,'id'=>$id]);
        return  $url ? $url : '';
    } 
    
    public function getUpname()  //'Изменение данных' 
    {
    //return 'Изменение должности: ' . $this->occupationname;
        return $this->occupationname;
    }
    
    public function getCode()
    {
        if ($this->occupationcode =='') { $code='-'; } 
        else      $code=' ' . $this->occupationcode;
        return  $code;
    }

    public function getEmployee_oc()  // Геттер для полного имени человека 
    {
         //$as = Employee::find()->all();
         $a=$this->id;
         $Employee=Employee::findOne($a);
         $a=$Employee->fullname;
         if ($a=='') { $a=''; }         
         return $a;    
    //return $this->lastname . ' ' . $this->firstname . ' ' . $this->patronymic;
    //firstname-Имя, 'patronymic-Отчество,lastname-Фамилия
    }

    public function getUrlp()
    {
        $id=$this->id;
        $url = Url::toRoute(['view','EmployeeSearch[occupation_id]'=>$id,'EmployeeSearch[isemployed]'=>'1','id'=>$id,'#'=>'p']);
        return  $url ? $url : '';
    }
     
    public function getOccupationemployeer()
    {
        $id=$this->id;
        $name=Employee::find()->where('occupation_id = :id',[':id'=>$id])->andWhere('isemployed = 1')->count();
        return $name.' чел.';
        //SELECT COUNT(*) FROM employee
    }

/*    public function getUrlempl()
    {
        
        $Url = Url::toRoute(['../employee/view','id'=>$this->id//occupationname
     //&EmployeeSearch[occupation_id]=9       
            ]);
        
        return  $Url; //? $Url : '';

    }*/
 //   public function getXr()
   // {
        //$a=$this->id;//occupation_id;
     //   $a=7;
        //$role = 'student'
      //  $users = User::find()->where(['id'=>5, 'role'=>$role])->all();
        
        //$all = Employee::find()->where(['lastname'=>'Гепалов' ])->all();
        //$b=$a->id;
        //$d=$b;
         //$a=$all->id;
       //  $Employee=Employee::findOne('occupation_id'==$a);//where(['lastname'=>'Гепалов', 'role'=>$role]);
       //  $a=$Employee->fullname;
       //  if ($a=='') { $a=''; }  //   */     
       //  return $a;
    //}
}  