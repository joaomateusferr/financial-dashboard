<?php

namespace TestDependencies\Email;

require dirname(__DIR__, 3).'/config.php';

class MailCatcherHelper {

    private string $ApiUrl;

    public function __construct() {

        $this->ApiUrl = $_SERVER['MAIL_CATCHER_API_URL'];

    }

    public function getAllMessages() :array {

        $Content = file_get_contents($this->ApiUrl.'/messages');

        if(!json_validate($Content))
            return [];

        $Content = json_decode($Content, true);

        foreach($Content as $Index => $Message){

            $Content[$Index]['size'] = (int) $Content[$Index]['size'];
            $Content[$Index]['sender'] = str_replace(['<', '>'], '', $Content[$Index]['sender']);

            foreach($Content[$Index]['recipients'] as $RecipientsIndex => $Recipient){

                $Content[$Index]['recipients'][$RecipientsIndex] = str_replace(['<', '>'], '', $Content[$Index]['recipients'][$RecipientsIndex]);

            }

            $ExtraInfos = file_get_contents($this->ApiUrl.'/messages/'.$Message['id'].'.json');

            if(!json_validate($ExtraInfos))
                continue;

            $ExtraInfos = json_decode($ExtraInfos, true);

            $Content[$Index]['type'] = $ExtraInfos['type'];
            $Content[$Index]['content'] = [];

            foreach($ExtraInfos['formats'] as $Format){

                $Body = file_get_contents($this->ApiUrl.'/messages/'.$Message['id'].'.'.$Format);

                if(empty($Body))
                    continue;

                $Body = trim($Body);

                $Content[$Index]['content'][$Format] = $Body;

            }

            $Content[$Index]['attachments'] = $ExtraInfos['attachments'];

        }

        return $Content;

    }

    public function getLastMessage() :array {

        $Messages = $this->getAllMessages();

        if(empty($Messages))
            return [];

        return $Messages[array_key_last($Messages)];

    }

    public function clearMessages() :bool {

        $Result = file_get_contents($this->ApiUrl.'/messages', false, stream_context_create(['http' => ['method'  => 'DELETE']]));

        if(empty($Result))
            return false;

        return true;

    }

}