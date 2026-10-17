<?php

namespace App\Repositories;

use \Exception;
use App\Services\MariaDB;
use App\Services\Password;
use App\Repositories\SessionRepository;
use App\Constants\ServersConstants;
use App\Constants\UsersConstants;
use App\Helpers\DatabaseHelper;

class UserRepository {

    public static function create(string $Email, string $Password) : bool {

        $User = [
            'Email' => $Email,
            'PasswordHash' => Password::generatePasswordHash($Password),
            'Type' => UsersConstants::getDefaultType(),
            'CustomerServerID' => ServersConstants::getCurrentCustomerServerID(),
        ];

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return false;

            $KernelConnection = new MariaDB('kernel', 'kernel');
            $Sql = 'INSERT INTO users (Email, PasswordHash, Type, CustomerServerID) VALUES (:Email, :PasswordHash, :Type, :CustomerServerID)';
            $Stmt = $KernelConnection->prepare($Sql);
            $Result = $Stmt->execute($User);
            return true;

        }catch (Exception $Exception){

            //add logs hererws

        }

        return false;

    }

    public static function retrieveUserDetailsByEmail(string $Email) : ?array {

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return null;

            $UserDetails = [];

            $Filter = ['Email' => $Email];

            $Sql = 'SELECT ID, Type, Email, PasswordHash FROM users WHERE Email = :Email LIMIT 1';
            $Stmt = $KernelConnection->prepare($Sql);
            $Result = $Stmt->execute($Filter);

            if($Result && $Stmt->rowCount() > 0)
                $UserDetails = $Stmt->fetch();

        } catch (Exception $Exception){

           //add logs here
           return null;

        }

        return $UserDetails;

    }

    public static function changeUserType(int $ID, string $Type) : bool {

        if(!in_array($Type, UsersConstants::getSupportedTypes()))
            return false;

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return false;

            $KernelConnection = new MariaDB('kernel', 'kernel');
            $Sql = 'UPDATE users SET Type = :Type WHERE ID = :ID';
            $Stmt = $KernelConnection->prepare($Sql);
            $Stmt->bindValue(':Type', $Type);
            $Stmt->bindValue(':ID', $ID);
            $Result = $Stmt->execute();

            if($Result)
                return true;

        }catch (Exception $Exception){

            //add logs hererws

        }

        return false;

    }

    public static function changeUserName(int $ID, string $NewName) : bool {

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return false;

            $KernelConnection = new MariaDB('kernel', 'kernel');
            $Sql = 'UPDATE users SET Name = :Name WHERE ID = :ID';
            $Stmt = $KernelConnection->prepare($Sql);
            $Stmt->bindValue(':Name', $NewName);
            $Stmt->bindValue(':ID', $ID);
            $Result = $Stmt->execute();

            if($Result)
                return true;

        }catch (Exception $Exception){

            //add logs hererws

        }

        return false;

    }

    public static function retrieveUserDetailsBySID(string $SID) : bool | array  {

        $Session = SessionRepository::get($SID, ['UserID']);
        $UserDetails = self::retrieveUserDetailsByID($Session['UserID'], ['ID', 'Name', 'Email', 'Type', 'CustomerServerID']);
        return $UserDetails;

    }

    public static function retrieveUserDetailsByID(string $ID, array $Fields = []) : bool | array  {

        $FieldsString = '*';

        if(!empty($Fields))
            $FieldsString = implode(', ', $Fields);

        $UserDetails = false;

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return false;

            $Sql = "SELECT $FieldsString FROM users WHERE ID = :ID";
            $Stmt = $KernelConnection->prepare($Sql);
            $Stmt->bindValue(':ID', $ID);
            $Result = $Stmt->execute();

            if($Result && $Stmt->rowCount() > 0)
                $UserDetails = $Stmt->fetch();

        } catch (Exception $Exception){

           //add logs here

        }

        return $UserDetails;

    }

}