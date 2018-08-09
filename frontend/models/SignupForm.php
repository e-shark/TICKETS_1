<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password', 'required'],
            ['password', 'compare'],

            ['password_repeat', 'string', 'min' => 6],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя', //'Имя пользователя'
            'password' => 'Пароль',//Yii::t('app','Password'),
            'password_repeat' => 'Подтверждение пароля',//=> Yii::t('app','Password confirm'),
            'rememberMe' => 'запомнить меня',//Yii::t('app','Remember Me'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }

    public function loaduser($UserID)
    {
        $user = new User();
        $user=User::findIdentity($UserID);
        $this->username = $user->username;
        $this->email = $user->email;
    }

}
