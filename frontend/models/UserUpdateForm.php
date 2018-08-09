<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class UserUpdateForm extends Model
{
    public $firstref;
    public $password;
    public $password_repeat;
    public $username;
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            //['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            //['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            //['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'compare'],

            ['password_repeat', 'string', 'min' => 6],
            //['passconfirm', 'compare','compareAttribute' => 'password'],//, 'message' => 'The password must match.'],

        ];
    }

    public function attributeLabels()
    {
        $labels = [
            'username' => 'Имя пользователя', //'Имя пользователя'
            'password' => 'Пароль',//Yii::t('app','Password'),
            'password_repeat' => 'Подтверждение пароля',//=> Yii::t('app','Password confirm'),
            'rememberMe' => 'запомнить меня',//Yii::t('app','Remember Me'),
        ];

        //$labels = [];
        //$labels['username2'] = Yii::t('app','Username');
        //$labels['rememberMe'] = Yii::t('app','Remember Me');
        //$labels['password'] = Yii::t('app','Password');
        //$labels['password_repeat'] = Yii::t('app','Password confirm');

//Yii::warning("************************************************ labels ***********************[\n".json_encode((array)$labels)."\n]");
        return $labels;
    }

    public function update($UserID)
    {
        $res = null;
        if (!$this->validate()) {
            return null;
        }
        $user = User::findIdentity($UserID);
        if ($user) {
            $user->username = $this->username;
            $user->email = $this->email;
            if (!empty($this->password)){
                $user->setPassword($this->password);
            }
            //$user->generateAuthKey();
            $res = $user->update() ? $user : null;
        } else $res = null;
        return $res;
    }

    public function loaduser($UserID)
    {
        //$user = new User();
        $user = User::findIdentity($UserID);
        $this->username = $user->username;
        $this->email = $user->email;
        return $user;
    }

}
