<?php

declare(strict_types=1);

require dirname(__DIR__,2).'/config.php';

use PHPUnit\Framework\TestCase;
use App\Services\EmailSender;
use App\Services\EmailContent;
use App\Helpers\EmailHelper;
use App\Constants\UsersConstants;
use TestDependencies\Email\MailCatcherHelper;

final class EmailHtmlMessageAndEmailNamesTest extends TestCase {

    private static array $Message;

    public static function setUpBeforeClass(): void {

        $EmailProvider = EmailHelper::buildProvider();
        $EmailContent =  new EmailContent('Subject', 'Simple body', true, '<p><strong>HTML body.</strong></p>');

        $Email = UsersConstants::getDefaultContactEmail();

        $MailCatcherHelper = new MailCatcherHelper();
        $MailCatcherHelper->clearMessages();

        $EmailSender = new EmailSender($EmailProvider, ['Email' => $Email, 'Name' => 'Contact'] , [['Email' => $Email, 'Name' => 'Contact']], $EmailContent);
        $EmailSender->sendOneByOne();

        self::$Message = $MailCatcherHelper->getLastMessage();
        $MailCatcherHelper->clearMessages();

    }

    public function testMessageIsNotEmpty(): void {

        $this->assertFalse(empty(self::$Message));

    }

    public function testVerifyMessageSender(): void {

        $Email = UsersConstants::getDefaultContactEmail();
        $this->assertFalse(empty(self::$Message['sender']));
        $this->assertSame(self::$Message['sender'], $Email);
        $this->assertFalse(empty(self::$Message['alias']['to'][$Email]));
        $this->assertSame(self::$Message['alias']['to'][$Email], 'Contact');

    }

    public function testVerifyMessageRecipients(): void {

        $Email = UsersConstants::getDefaultContactEmail();
        $this->assertFalse(empty(self::$Message['recipients']));
        $this->assertSame(self::$Message['recipients'][0], $Email);
        $this->assertFalse(empty(self::$Message['alias']['from'][$Email]));
        $this->assertSame(self::$Message['alias']['from'][$Email], 'Contact');

    }

    public function testVerifyMessageSubject(): void {

        $this->assertFalse(empty(self::$Message['subject']));
        $this->assertSame(self::$Message['subject'], 'Subject');

    }

    public function testVerifyMessageType(): void {

        $this->assertFalse(empty(self::$Message['type']));
        $this->assertSame(self::$Message['type'], 'multipart/alternative');

    }

    public function testVerifyMessageBody(): void {

        $this->assertFalse(empty(self::$Message['content']['html']));
        $this->assertSame(self::$Message['content']['html'], 'Simple body');
        $this->assertFalse(empty(self::$Message['content']['plain']));
        $this->assertSame(self::$Message['content']['plain'], '<p><strong>HTML body.</strong></p>');

    }

}
