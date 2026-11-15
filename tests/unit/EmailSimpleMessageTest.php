<?php

declare(strict_types=1);

require dirname(__DIR__,2).'/config.php';

use PHPUnit\Framework\TestCase;
use App\Services\EmailSender;
use App\Services\EmailContent;
use App\Helpers\EmailHelper;
use App\Constants\UsersConstants;
use TestDependencies\Email\MailCatcherHelper;

final class EmailSimpleMessageTest extends TestCase {

    private static array $Message;

    public static function setUpBeforeClass(): void {

        $EmailProvider = EmailHelper::buildProvider();
        $EmailContent =  new EmailContent('Subject', 'Simple body');

        $DefaultRootCredentials = UsersConstants::getDefaultRootData();
        $RootEmail = $DefaultRootCredentials['Email'];

        $MailCatcherHelper = new MailCatcherHelper();
        $MailCatcherHelper->clearMessages();

        $EmailSender = new EmailSender($EmailProvider, $RootEmail, [$RootEmail], $EmailContent);
        $EmailSender->sendOneByOne();

        self::$Message = $MailCatcherHelper->getLastMessage();
        $MailCatcherHelper->clearMessages();

    }

    public function testMessageIsNotEmpty(): void {

        $this->assertFalse(empty(self::$Message));

    }

    public function testVerifyMessageSender(): void {

        $this->assertFalse(empty(self::$Message['sender']));
        $this->assertSame(self::$Message['sender'], UsersConstants::getDefaultRootData()['Email']);

    }

    public function testVerifyMessageRecipients(): void {

        $this->assertFalse(empty(self::$Message['recipients']));
        $this->assertSame(self::$Message['recipients'][0], UsersConstants::getDefaultRootData()['Email']);

    }

    public function testVerifyMessageSubject(): void {

        $this->assertFalse(empty(self::$Message['subject']));
        $this->assertSame(self::$Message['subject'], 'Subject');

    }

    public function testVerifyMessageType(): void {

        $this->assertFalse(empty(self::$Message['type']));
        $this->assertSame(self::$Message['type'], 'text/plain');

    }

    public function testVerifyMessageBody(): void {

        $this->assertFalse(empty(self::$Message['content']['plain']));
        $this->assertSame(self::$Message['content']['plain'], 'Simple body');

    }

}
