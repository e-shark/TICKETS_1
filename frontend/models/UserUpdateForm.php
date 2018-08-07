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
    public $username;
    public $email;
    public $password;
    public $firstref;


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

        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>Yii::t('app','Username'),
            'password'=>Yii::t('app','Password'),
            'rememberMe'=>Yii::t('app','Remember Me')
        ];
    }

    public function update($UserID)
    {
        if (!$this->validate()) {
            return null;
        }
        
        //$user = new User();
        $user = User::findIdentity($UserID);
        if ($user) {
            $user->username = $this->username;
            $user->email = $this->email;
            if (!empty($this->password))
                $user->setPassword($this->password);
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
