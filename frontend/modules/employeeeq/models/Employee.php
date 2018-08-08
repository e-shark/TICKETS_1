<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use frontend\modules\employeeeq\models\District;
use frontend\modules\employeeeq\models\Street;
use frontend\modules\employeeeq\models\Facility;

class Employee extends \yii\db\ActiveRecord
{
    public $district;
    public $streettype; 
    public $streetname; 
    public $house;
    public $emechanic;
    public static function tableName()
    {
        return 'employee';
    }

    public function rules()
    {
        return [
            [['remoteid', 'postcode', 'statusdisability', 'statuschernobyl', 'user_id', 'occupation_id', 'division_id'], 'integer'],
            [['employmentdate', 'dismissaldate'], 'safe'],
            [['salary', 'rate'], 'number'],
            [['firstname', 'patronymic', 'lastname'], 'string', 'max' => 50],
            [['personcode', 'sex', 'empcode', 'skillscategory', 'skillsrank', 'statusmilitary', 'employmenttype', 'oprights'], 'string', 'max' => 10],
            [['passportno', 'personphone', 'personphone1'], 'string', 'max' => 16],
            [['passportdata'], 'string', 'max' => 160],
            [['personaddress', 'currentaddress', 'personemail', 'personurl', 'education', 'lastjob'], 'string', 'max' => 255],
            [['married', 'isemployed'], 'string', 'max' => 1],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['occupation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Occupation::className(), 'targetAttribute' => ['occupation_id' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['division_id' => 'id']],
            ['personemail', 'email',  'message' => 'Некорректно ведён e-mail'],
            [['lastname','occupation_id', 'division_id'],'required','message' => 'Обязательное к заполнению поле'],
            [['patronymic','firstname','lastname'], 'match', 'pattern' => '/^[0-9А-яА-яЄєІЇіїЁё_\s\'\-]+$/u', 'message' => 'Поле может содержать только буквы кириллицы'],
            //[['firstname','lastname','patronymic'], 'match', 'pattern' => '/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюя АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ-]+$/',
            // 'message' => 'Можно использовать только русские буквы'],
            //['employmentdate', 'date','format' =>'php:Y-m-d','message' => 'Введите дату в формате 2001-12-31'],
            //['dismissaldate', 'date','format' =>'php:Y-m-d','message' => 'Введите дату в формате 2001-12-31'],
            [['certprofessional', 'certmedical', 'certnarcology', 'certpsych', 'certcriminal'], 'date','format' =>'php:Y-m-d','message' => 'Введите дату в формате 2001-12-31'],
            //[['lastname', 'unique'], 'message'=>'Запись с таким именем уже существует.']
            //[['firstname', 'patronymic', 'lastname'], 'unique','targetAttribute' => ['firstname', 'patronymic', 'lastname'],//'skipOnEmpty' => true, //'skipOnError' => true, 
            //'message'=>'Запись с таким именем уже существует.']
            [['lastname', 'firstname','patronymic'], 'unique', 'targetAttribute' => ['lastname', 'firstname','patronymic'], 'skipOnEmpty' => true, 'skipOnError' => true, 'message'=>'Запись с таким именем уже существует.'],
            //[['streetdistrict'], 'unique','targetAttribute' => ['streetdistrict', 'streetname', 'streetnameru','streetcode'],'skipOnEmpty' => true, 'skipOnError' => true, 'message'=>'Запись с таким именем уже существует.']
            [['empcode'],  'unique', 'skipOnEmpty' => true, 'skipOnError' => true, 'message'=>'Такой табельный номер уже используется'],
            [['isemployed'], 'default', 'value' => '1'], 
            ['district','safe'],
        ];

    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'remoteid' => 'Remoteid',
            'firstname' => 'Имя',//'Firstname',
            'patronymic' => 'Отчество',//'Patronymic', 
            'lastname' => 'Фамилия',//'Lastname',
            'personcode' => 'Идетификационный Код',//'Personcode',
            'passportno' => 'Номер Паспорта',//'Passportno',
            'passportdata' => 'Паспортные Данные ',//'Passportdata',
            'personaddress' => 'Адрес Регистрации',//'Personaddress',
            'currentaddress' => 'Адрес Проживания',//'Currentaddress',
            'postcode' => 'Почтовый Индекс',//'Postcode',
            'personphone' => 'Телефон',//Номер Телефона',//'Personphone',
            'personphone1' => 'Дополнительный Номер Телефона',//'Personphone1',
            'personemail' => 'Email',//'Personemail',
            'personurl' => 'Сайт',//'Personurl',
            'sex' => 'Пол',//'Sex',
            'birthday' => 'Дата Рождения',//'Birthday',
            'married' => 'Семейное Положение',//'Married',
            'education' => 'Образование',//'Education',
            'employmentdate' => 'Дата Приема На Работу',//'Employmentdate',
            'dismissaldate' => 'Дата Увольнения',//'Dismissaldate',
            'salary' => 'Зарплата',//'Salary',
            'rate' => 'Оплата В Час',//'Rate',
            'skillscategory' => 'Категория',//'Skillscategory',
            'skillsrank' => 'Ранг',//'Skillsrank',
            'certprofessional' => 'Дата Cертификата Квалификации',//'Certprofessional',
            'certmedical' => 'Дата Медицинского Сертификата',//'Certmedical',
            'certnarcology' => 'Дата Наркологического Сертификата',//'Certnarcology',
            'certpsych' => 'Дата Психологического Сертификата',//'Certpsych',
            'certcriminal' => 'Дата Свидетельства Об Отсутствии Судимости',//'Certcriminal',
            'statusmilitary' => 'Военная Обязаность',//'Statusmilitary',
            'statusdisability' => 'Инвалидность',//'Statusdisability',
            'statuschernobyl' => 'Категория чернобыльца',//'Statuschernobyl',
            'lastjob' => 'Последнее место работы','Lastjob',
            'isemployed' => 'Статус работы',//'Безработность',//'Isemployed',
            'employmenttype' => 'Вид Занятости',//'Employmenttype',
            'oprights' => 'Оперативные Права',//'Oprights',
            'user_id' => 'Имя Пользователя Системы',//'ID Пользователя',//'User ID',
            'occupation_id' => 'Должность',
            'division_id' => 'Подразделение',
            'empcode'=>'Табельный номер',//'Empcode',
            'fullName' =>'Фамилия Имя Отчество',// 'Ф.И.О.', /* Название атрибута для вывода на экран */
            'ocname'=>'Должность',
            'division'=>'Подразделение',
            'district'=>'Район',
            //'oprightsall'=>'Оперативные Права',
        ];
    }
    
    public function getElevators()
    {
        return $this->hasMany(Elevator::className(), ['elperson_id' => 'id']);
    }
    public function getDistrict()//район
     {
        return $district;
     }
    public function getStreettype()//тип улицы
     {
        return $streettype;
     }
    public function getStreetname()//название улицы
     {
        return $streetname; 
     }
     public function getHouse()//дом
     {
        return $house;
     }
     public function streetid($type,$name)//id улицы по-типу и названию
     {
        $searchModel = new EmployeeSearch();
        $street=Street::findone(['streettype'=>$type, 'streetnameru'=>$name]);
        $streetid=$street->id;
        return $streetid;
     }
     public function getEmechanic()//
     {
        return $emechanic;
     }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getUsername()
    {
        $user_id=$this->user_id;
        $User=User::findOne($user_id);
        $username=$User->username;
        if ($username=='') { $username=''; }         
        return $username;//$username;
        //return $nameUser; 
    }

    public function getOccupation()
    {
        return $this->hasOne(Occupation::className(), ['id' => 'occupation_id']);
    }

    public function getDivision()
    {
        return $this->hasOne(Division::className(), ['id' => 'division_id']);
    }

    public function getTicketlogs0()
    {
        return $this->hasMany(Ticketlog::className(), ['tilreceiver_id' => 'id']);
    }

    public function getElevatorscount()//подсчёт всего оборудования
    {
        //$elevators=$this->elevators->elperson_id; 
        $elevatorscount = Elevator::find()->where('elperson_id = :id ', [':id'=>$this->id])
        ->count();
        return $elevatorscount;
    }
    
    public function getElevatorselcount()//подсчёт лифтов
    {
        $elevatorcount = Elevator::find()->where('elperson_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 1])->count();
        return $elevatorcount;
    }
    public function getSwitchcount()//подсчёт щитовых
    {
        $switchcount = Elevator::find()->where('elperson_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 10])->count();
        return $switchcount;
    }
    public function getIntercomcount()//подсчёт домофонов
    {
        $intercomcount = Elevator::find()->where('elperson_id = :id ', [':id'=>$this->id])
        ->andwhere(['eldevicetype' => 20])->count();
        return $intercomcount;
    }
    public function getElperson()
    {
        return $this->hasMany(Elevator::className(), ['elperson_id' => 'id']);
    }   

    public function getFullName()  // Геттер для полного имени человека 
    {
    return $this->lastname . ' ' . $this->firstname . ' ' . $this->patronymic;
    //lastname-Фамилия firstname-Имя, patronymic-Отчество
    }

    public function getUpFullName()  //'Изменение данных' 
    {
    return 'Изменение данных ' . $this->fullname;//$this->lastname . ' ' . $this->firstname . ' ' . $this->patronymic;
    //lastname-Фамилия firstname-Имя, patronymic-Отчество 
    }
    
    public function getUrl()
    {
        $parent = Url::toRoute(['employee/view','id'=>$this->id]);
        return  $parent ? $parent : '';
    } 

    public function Occupationname()
    {
        return 'occupationname';
    }

          public function getOccupationname()
    {
         $a=$this->occupation_id;
         $Occupation=Occupation::findOne($a);
         $a=$Occupation->occupationname;
         if ($a=='') { $a=''; }         
         return $a;
    }

    public function getDivisionname()
    {
         $a=$this->division_id;
         $Division=Division::findOne($a);
         $a=$Division->divisionname;
         if ($a=='') { $a=''; }         
         return $a;
    }
    public function getIsemployedname()
    {
        $name=$this->isemployed;
        if ($name==1) { $name='Работает'; }
            elseif ($name==0) { $name='Уволен'; } 
             else { $name=''; }//Не определено
        return $name;
    }
    public function getEmpcode()
    {
     $empcode=$this->empcode;
     if ($empcode=='') $empcode='';
     return $empcode;   
    } 

  public function getEmployment_date() 
  {
        $date_str = $this->employmentdate;
        $date_str = mb_strimwidth($date_str, 0, 10);
        
   return "$date_str";
  }

  public function getDismissal_date() 
  {
        $date = $this->dismissaldate;
        $date = mb_strimwidth($date, 0, 10);
        
   return "$date";
  }

  public function getOprights() //Оперативные Права
  {
     $oprights=$this->oprights;
    if ($oprights=='D') {
          $oprights='Диспетчер';
     }
     elseif ($oprights=='M') {
       $oprights='Мастер';
     }
     elseif ($oprights=='F') {
          $oprights='Електромеханик';
     }
     elseif ($oprights=='d') {
          $oprights='Оператор';
     }
     else {
          $oprights='';
     }
     return $oprights;
  }

  public function getOprights_el() //Оперативные Права+оборудование
  {
    $oprights=$this->Oprights;
    $elevatorscount=$this->elevatorscount;
    if ($elevatorscount!=0)//есть оборудование
    {
      $url=Url::toRoute(['emechanic','ElevatorSearch[elperson_id]'=>
          $this->id,'id'=>$this->id]);
      $elevatorselcount=$this->elevatorselcount;//подсчёт лифтов
      $switchcount=$this->switchcount;//подсчёт щитовых
      $intercomcount=$this->intercomcount;//подсчёт домофонов
      $m=$elevatorselcount+$switchcount+$intercomcount;//подсчёт л+щ+д
      //$eldt=$this->elperson->eldevicetype;//1-Лифт 10-ВДЭС 20-Домофоны
      if ($elevatorselcount!=0)// есть лифты
        { 
          $eldt='<br>'. Html::a ('Лифтов: '.$elevatorselcount.' шт.', $url);
            //$this->elperson->urlelp);
        }
      if ($switchcount!=0)//есть щитовые
        {
          $eldt=$eldt.'<br>'.Html::a ('Электрощитовых: '.$switchcount.' шт.', $url);
        }
      if ($intercomcount!=0)// есть домофоны
        {
           $eldt=$eldt.'<br>'.Html::a ('Домофонов:'.$intercomcount.' шт.', $url);
        }
      if ($elevatorscount>$m)
        { 
          $m=$elevatorscount-$m;//подсчет прочего оборудования
          $eldt=$eldt.'<br>'.Html::a ('Прочего:'.$m.' шт.', $url);
             // $this->elperson->urlelp);//ссылка с модели элеватор
        }
      return $oprights.' <br>Закреплено'.$eldt;
        //Html::a ($eldt.$elevatorscount, $this->elperson->getUrlelp()  ); 
    }
    else { return $oprights; }
  }

  public function getUrldiv()//Список оборудования, закрепленного за подразделением
    {
        $id=$this->division_id;
        $url = Url::toRoute(['elevator/index','ElevatorSearch[eldivision_id]'=>$id,'id'=>$id]);
        return $url ? $url : '';
    }
  public function getUrldelp()//Список сотрудников конкретного подразделения
    {
        $id=$this->division_id;
        $url = Url::toRoute(['division/view','EmployeeSearch[division_id]'=>$id,'EmployeeSearch[isemployed]'=>'1','id'=>$id,'#'=>'p']);
        return $url ? $url : '';
    }
  public function getUrlelp()//Список оборудования, закрепленного за электромехаником 
     {  
        return Url::toRoute(['emechanic','ElevatorSearch[elperson_id]'=>$this->id,'id'=>$this->id]);
     } 
/*   public function getEmploymenttype() 
    {

    $Employmenttype=$this->employmenttype;
       switch ($Employmenttype) 
       {    
         case 
            $Employmenttype=='FT':
              $Employmenttype='Полная Занятость';
              break;
         case 
            $Employmenttype=='PT':
              $Employmenttype='Частичная Занятость';
              break;
         case 
              $Employmenttype=='UFT':
              $Employmenttype='Полная Занятость без регистрации';
              break;
         case 
              $Employmenttype=='UPT':
              $Employmenttype='Частичная Занятость без регистрации';
              break;
          default: $Employmenttype='';
        }
                    
     return $Employmenttype;
    }*/
}