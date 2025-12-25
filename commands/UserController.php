<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * User management commands
 */
class UserController extends Controller
{
    /**
     * Create a new user
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $role
     */
    public function actionCreate($username, $password, $email = null, $role = User::ROLE_CLIENT)
    {
        $user = new User(['scenario' => 'create']);
        $user->username = $username;
        $user->password = $password;
        $user->email = $email ?? $username . '@example.com';
        $user->role = $role;

        if ($user->save()) {
            echo "User '$username' created successfully.\n";
            return ExitCode::OK;
        } else {
            echo "Failed to create user:\n";
            print_r($user->errors);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Update existing user password
     * @param string $username
     * @param string $password
     */
    public function actionUpdatePassword($username, $password)
    {
        $user = User::findByUsername($username);
        if (!$user) {
            echo "User '$username' not found.\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->scenario = 'update';
        $user->password = $password;
        if ($user->save()) {
            echo "Password for user '$username' updated successfully.\n";
            return ExitCode::OK;
        } else {
            echo "Failed to update password:\n";
            print_r($user->errors);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Update user role
     * @param string $username
     * @param string $role
     */
    public function actionUpdateRole($username, $role)
    {
        $user = User::findByUsername($username);
        if (!$user) {
            echo "User '$username' not found.\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->role = $role;
        if ($user->save(false)) {
            echo "Role for user '$username' updated to '$role'.\n";
            return ExitCode::OK;
        } else {
            echo "Failed to update role:\n";
            print_r($user->errors);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * List all users
     */
    public function actionList()
    {
        $users = User::find()->all();
        
        echo "\nUsers:\n";
        echo str_repeat('-', 80) . "\n";
        printf("%-5s %-20s %-30s %-15s\n", 'ID', 'Username', 'Email', 'Role');
        echo str_repeat('-', 80) . "\n";
        
        foreach ($users as $user) {
            printf("%-5d %-20s %-30s %-15s\n", 
                $user->id, 
                $user->username, 
                $user->email ?? '-',
                $user->role
            );
        }
        
        echo str_repeat('-', 80) . "\n\n";
        
        return ExitCode::OK;
    }
}
