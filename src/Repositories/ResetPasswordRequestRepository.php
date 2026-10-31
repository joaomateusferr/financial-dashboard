<?php

namespace App\Repositories;

use \Exception;
use App\Services\Log;
use App\Helpers\ResetPasswordRequestHelper;
use App\Helpers\DatabaseHelper;

class ResetPasswordRequestRepository {

    public static function create(int $UserID) : ?string {

        $Time = time();
        $ExpiresAt = $Time + ResetPasswordRequestHelper::getStandardDuration();
        $Token = ResetPasswordRequestHelper::generateToken();

        try{

            $KernelConnection = DatabaseHelper::getConnection('kernel');

            if(empty($KernelConnection))
                return null;

            $Sql = 'INSERT INTO reset_password_requests (Token, UserID, CreatedAt, ExpiresAt) VALUES (:Token, :UserID, :CreatedAt, :ExpiresAt)';
            $Stmt = $KernelConnection->prepare($Sql);
            $Stmt->bindValue(':Token', $Token);
            $Stmt->bindValue(':UserID', $UserID);
            $Stmt->bindValue(':CreatedAt', time());
            $Stmt->bindValue(':ExpiresAt', $ExpiresAt);
            $Stmt->execute();

            return $Token;

        }catch (Exception $Exception){

            Log::add('ALERT','create-reset-password-request', $Exception->getMessage());
            return null;

        }

    }

}