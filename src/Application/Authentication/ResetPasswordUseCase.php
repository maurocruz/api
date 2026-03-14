<?php
namespace Plinct\Api\Application\Authentication;

use DateInterval;
use DateTime;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use Random\RandomException;

readonly class ResetPasswordUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{ }

	/**
	 * @throws RandomException
	 */
	public function resetPassword(array $parseBody): array
	{
		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('reset_password', $parseBody);
		$email = SanitizeParams::validateEmail($parseBody['email'] ?? null);
		$mailHost = SanitizeParams::validateSmtp($parseBody['mailHost'] ?? $_ENV['MAIL_HOST'] ?? null);
		$mailUsername = SanitizeParams::validateEmail($parseBody['mailUsername'] ?? $_ENV['MAIL_USERNAME'] ?? null);
		$mailPassword = $parseBody['mailPassword'] ?? $_ENV['MAIL_PASSWORD'] ?? null;
		$urlToResetPassword = SanitizeParams::validateUrl($parseBody['urlToResetPassword'] ?? $_ENV['URL_TO_RESET_PASSWORD'] ?? null);

		// VERIFICA SE EXISTEM TODOS OS DADOS
		if (!$email || !$mailHost || !$mailUsername || !$mailPassword || !$urlToResetPassword) {
			return SupportApplication::returnDTO(false, 'Missing required fields', $parseBody);
		}

		// VERIFICA SE EXISTE USUÁRIO DO EMAIL
		$userDataBase = $this->databaseActions->read('user', ['email' => $email])->render();
		if ($userDataBase['status'] === false) {
			return $userDataBase;
		}
		$userData = $userDataBase['data'][0] ?? null;
		if (!$userData) {
			return ['status'=>false, 'message'=>'User not found', 'data'=>$parseBody];
		}
		$iduser = $userData['iduser'];

		// VERIFICA SE EXISTE UM TOKEN DE RESET (SE HOUVER, APAGA)
		$databaseUserReset = $this->databaseActions->read('user_passwordReset', ['iduser' => $iduser])->render();
		if ($databaseUserReset['status'] === false) {
			return $databaseUserReset;
		}
		$passwordReset = $databaseUserReset['data'][0] ?? null;
		if ($passwordReset) {
			$deleteData = $this->databaseActions->delete('user_passwordReset', ['iduser' => $iduser]);
			if ($deleteData['status'] === false) {
				return $deleteData;
			}
		}

		// GERA UM NOVO TOKEN DE RESET
		$now = new DateTime('NOW');

		$selector = bin2hex(random_bytes(8));
		$validatorBytes = random_bytes(32);
		$validator = bin2hex($validatorBytes);
		$tokenHash = hash('sha256', $validatorBytes);

		$now->add(new DateInterval('PT01H')); // 1 hour
		$expires = $now->format('Y-m-d\TH:i:s');

		// SALVA O NOVO TOKEN DE RESET
		$databaseCreateToken = $this->databaseActions->create('user_passwordReset', ['iduser'=>$iduser, 'selector'=>$selector, 'token' => $tokenHash, 'expires'=>$expires]);
		if ($databaseCreateToken['status'] === false) {
			return $databaseCreateToken;
		}

		// ENVIA UMA EMAIL DE CONFIRMAÇÃO PARA O USUÁRIO
		return self::sendEmailForResetPassword($parseBody, $selector, $validator);
	}

	/**
	 * @param array $parseBody
	 * @param string $selector
	 * @param string $validator
	 * @return array
	 */
	private static function sendEmailForResetPassword(array $parseBody, string $selector, string $validator): array
	{
		$resetPasswordUri = $parseBody['urlToResetPassword']."?selector=$selector&validator=$validator";
		$subject = _("Reset password");
		$body = "<p>"._("A new password was requested for this email. If it wasn't you, please disregard this message.")."</p>"
			. "<p>" . sprintf(_("Otherwise, go to this link <a href='%s'>%s</a> and change your password. This token expires in half an hour."), $resetPasswordUri, $resetPasswordUri)."</p>"
			. "<p>"._("regards!")."</p>";

		$phpMail = new PHPMailer(true);

		try {
			$phpMail->isSMTP();
			$phpMail->Host = $parseBody['mailHost'];
			$phpMail->SMTPAuth   = true;
			$phpMail->Username = $parseBody['mailUsername'];
			$phpMail->Password = $parseBody['mailPassword'];
			$phpMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$phpMail->Port = 465;

			$phpMail->setFrom($parseBody['mailUsername'], $subject);

			$phpMail->addAddress($parseBody['email']);

			$phpMail->isHTML();
			$phpMail->Subject = $subject;
			$phpMail->Body = $body;

			$phpMail->send();

			$message = sprintf(_("An email has been sent to %s from %s to confirm your identity and change your password."), $parseBody['email'], $parseBody['mailUsername']);

			return SupportApplication::returnDTO(true, $message);

		} catch (Exception) {
			return SupportApplication::returnDTO(false, $phpMail->ErrorInfo);
		}
	}
}
