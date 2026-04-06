<?php
namespace Plinct\Api\Application\Support;

class SanitizeParams
{
	/**
	 * @param string $type
	 * @param array $params
	 * @return array
	 */
	public static function sanitize(string $type, array $params): array
	{
		$newParams = [];
		foreach ($params as $key => $value) {
			if ($value === null) {
				continue;
			}
			// ELIMINA ESPAÇOS EM BRANCO
			$value = trim($value," \t\n\r\0\x0B");

			// SÓ WEB PAGE ELEMENT NA PROPRIEDADE TEXT ACEITA TAGS
			if ($type !== 'webPageElement' && $key === 'text') {
				$value = htmlspecialchars(strip_tags($value));
			}

			// CONVERTE A PROPRIEDADE 'properties' NUM ARRAY
			if ($key === 'properties') {
				$value = array_map('trim', explode(',',$value));
			}

			// FIELDS MANDATORIES
			if ($key === 'fields') {
				$valueArray = explode(',',$value);
				if (!in_array('idthing', $valueArray)) {
					array_unshift($valueArray, 'idthing');
				}
				if (!in_array('type',$valueArray)) {
					array_unshift($valueArray, 'type');
				}
				$value = implode(',',$valueArray);
			}

			// CONSTRÓI ARRAY
			$newParams[$key] = $value;
		}

		return $newParams;
	}

	/**
	 * @param string $email
	 * @return string|null
	 */
	public static function validateEmail(string $email): ?string
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
	}

	public static function validateUrl(string $url): ?string
	{
		return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
	}

	public static function validateSmtp(string $smtp): ?string
	{
		return filter_var($smtp, FILTER_VALIDATE_DOMAIN) ? $smtp : null;
	}

	public static function validatePassword(string $password): bool|string
	{
		return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)
			? true :
			"'A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula, uma letra minúscula, um número e um caractere especial.'";
	}
}
