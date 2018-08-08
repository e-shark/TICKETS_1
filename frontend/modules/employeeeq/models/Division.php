<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

//use yii\db\Query;
/**
 * This is the model class for table "division".
 *
 * @property int $id
 * @property string $divisionname
 * @property string $divisionfullname
 * @property string $divisioncode
 * @property string $divisioncodesvc
 * @property string $divisiondate
 * @property int $division_id
 * @property int $divisioncompany_id
 *
 * @property Division $division
 * @property Division[] $divisions
 * @property Company $divisioncompany
 * @property Elevator[] $elevators
 * @property Employee[] $employees
 * @property Ticketlog[] $ticketlogs
 * @property Ticketlog[] $ticketlogs0
 */
class Division extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'division';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['divisiondate'], 'date','format' =>'php:Y-m-d','message' => 'Введите дату в формате 2001-12-31'],
            [['division_id', 'divisioncompany_id'], 'integer'],
            //[['divisioncompany_id'], 'required','message'=>'Поле обязательное для заполнения'],
            [['divisionname'], 'required','message'=>'Поле обязательное для заполнения'],
            [['divisionfullname'],'required','message'=>'Поле обязательное для заполнения'],
            [['divisionname'], 'string', 'max' => 200],
            [['divisionfullname'],'string', 'max' => 255],
            [['divisioncode', 'divisioncodesvc'], 'string', 'max' => 10],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            //[['divisioncompany_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['divisioncompany_id' => 'id']],
            [['divisioncompany_id'], 'default', 'value' => '1'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'divisionname' => 'Наименование Подразделения',//'Divisionname',
            'divisionfullname' => 'Полное Наименование Подразделения',//'Divisionfullname',
            'divisioncode' => 'Код Подразделения',//'Divisioncode',
            'divisioncodesvc' => 'Закреплённое Оборудование',//'Сервисный код',//'Divisioncodesvc',
            //-elevators,E-electricity,S-speakerphones
            //код обслуживания, L-лифты, E-электричество, S-громкоговорители
            'divisiondate' => 'Дата',//'Divisiondate',
            'division_id' => 'ID-Подразделения',//'Division ID',
            'divisioncompany_id' => 'Код компании',//'Divisioncompany ID',
            'divisionsvc' => 'Оборудование',//Название атрибута для вывода на экран 
            'divisionnameup'=>'Название Подразделения',
            'divisionemployee' =>'Количество сотрудников',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivisions()
    {
        return $this->hasMany(Division::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivisioncompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'divisioncompany_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElevators()
    {
        return $this->hasMany(Elevator::className(), ['eldivision_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['division_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketlogs()
    {
        return $this->hasMany(Ticketlog::className(), ['tilsenderdesk_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketlogs0()
    {
        return $this->hasMany(Ticketlog::className(), ['tilreceiverdesk_id' => 'id']);
    }
    public function getElevators_division_count()//подсчёт всего оборудования
    {
        $elevatorscount = Elevator::find()->where('eldivision_id = :id ', [':id'=>$this->id])
        ->count();
        return $elevatorscount;
    }
    public function getDivisionelcount()//подсчёт лифтов
    {
        $elevatorscount = Elevator::find()->where('eldivision_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 1])->count();
        return $elevatorscount;
    }
    public function getDivisionswitchcount()//подсчёт щитовых
    {
        $switchcount = Elevator::find()->where('eldivision_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 10])->count();
        return $switchcount;
    }
    public function getDivisionintercomcount()//подсчёт домофонов
    {
        $intercomcount = Elevator::find()->where('eldivision_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 20])->count();
        return $intercomcount;
    }
    public function getDivisionsvc()
    {
        $L=Лифты; $E=Электрощитовые; $S=Домофоны;//также в views/index и views/_form
        $divisioncodename=$this->divisioncodesvc;
        if ($divisioncodename ==L) { $divisioncodename=$L;}//'Лифты'
        elseif ($divisioncodename ==E) { $divisioncodename=$E;}//'Электричество'
        elseif ($divisioncodename ==S) {$divisioncodename=$S;}//'Домофоны'
        elseif ($divisioncodename =='') {$divisioncodename ='-';}
        else      $divisioncodename='' . $this->divisioncodesvc; 
        return $divisioncodename;
    }

    public function getDivisionsvc_el() //Закреплённое оборудование+ кол-во оборудования
    {
        $dsvc=$this->divisionsvc;
        $elevatorscount=$this->elevators_division_count;//всё оборудование
        if ($elevatorscount!=0)
        {//всё оборудование
            $dsvc='';
            $elcount=$this->divisionelcount;//подсчёт лифтов
            $switchcount=$this->divisionswitchcount;//подсчёт щитовых
            $intercomcount=$this->divisionintercomcount;//подсчёт домофонов
            $m=$elcount+$switchcount+$intercomcount;//подсчёт л+щ+д
            //$eldt=$this->elperson->eldevicetype;//1-Лифт 10-ВДЭС 20-Домофоны
            if ($elcount!=0)// есть лифты
              {
                $dsvc=$dsvc.Html::a 
                ('Лифты ('. $elcount.' шт.) ', $this->geturleldivision());
              }
            if ($switchcount!=0)//есть щитовые
              { if ($dsvc!='') {$dsvc=$dsvc.'<br>'; }// новая строка
                $dsvc=$dsvc.Html::a 
                ('Электрощитовые ('.$switchcount.' шт.) ', $this->geturleldivision());
              }
            if ($intercomcount!=0)// есть домофоны
              { if ($dsvc!='') {$dsvc=$dsvc.'<br>'; }// новая строка
                $dsvc=$dsvc.Html::a 
                ('Домофонов:'.$intercomcount.' шт.) ', $this->geturleldivision());
              }
            if ($elevatorscount>$m)// есть прочее оборудование
              { if ($dsvc!='') {$dsvc=$dsvc.'<br>'; }// новая строка
                $m=$elevatorscount-$m;//подсчет прочего оборудования
                $dsvc=$dsvc.Html::a 
                ('Прочего:'.$m.' шт.)', $this->geturleldivision());
              }
        }
        return $dsvc; 
    }
    
    public function getUrleldivision()
    {
        $url = Url::toRoute(['elevator/index','ElevatorSearch[eldivision_id]'=>$this->id,'id'=>$this->id]);
        return $url ? $url : '';
    } 

     public function getUrl()
    {
        $id=$this->id;
        $url = Url::toRoute(['view','EmployeeSearch[division_id]'=>$id,'id'=>$id]);
        return $url ? $url : '';
    } 

     public function getUrlp()
    {
        $id=$this->id;
        $url = Url::toRoute(['view','EmployeeSearch[division_id]'=>$id,'EmployeeSearch[isemployed]'=>'1','id'=>$id,'#'=>'p']);
        return $url ? $url : '';
    } 


    public function getDivisionnameup()
    {
        $name=$this->divisionname;
        $fname=$this->divisionfullname;
        if ($name==$fname) { return $this->divisionname; }
        else { return $this->divisionname . ' / '. $this->divisionfullname; }
    }

    public function getUpdivisionname()  //'Изменение данных' 
    {
    return //'Изменение Подразделения: ' . 
    $this->divisionname;
    }

    public function getDivisiondate()
    {
        if ($this->divisiondate =='') { $date='-'; } 
        else      $date=' ' . $this->divisiondate;
        return  $date;
    }

    public function getDivisionemployeer()
    {
        $name=$this->id;
        $name2 = Employee::find()->where('division_id = :name ', [':name'=>$name])
        ->andWhere('isemployed = 1')->count();
        return $name2.' чел.';
    }
}