<?php

declare(strict_types = 1);

namespace Consistence\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File as PhpCsFile;
use PHP_CodeSniffer\Util\Common as PhpCsUtil;

class ValidVariableNameSniff extends \PHP_CodeSniffer\Sniffs\AbstractVariableSniff
{

	const CODE_CAMEL_CAPS = 'NotCamelCaps';

	/** @var string[] */
	private static $phpReservedVars = [
		'_SERVER',
		'_GET',
		'_POST',
		'_REQUEST',
		'_SESSION',
		'_ENV',
		'_COOKIE',
		'_FILES',
		'GLOBALS',
	];

	/**
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processVariable(PhpCsFile $file, $stackPointer)
	{
		$tokens = $file->getTokens();
		$varName = ltrim($tokens[$stackPointer]['content'], '$');

		if (in_array($varName, self::$phpReservedVars, true)) {
			return; // skip PHP reserved vars
		}

		$objOperator = $file->findPrevious([T_WHITESPACE], ($stackPointer - 1), null, true);
		if ($tokens[$objOperator]['code'] === T_DOUBLE_COLON) {
			return; // skip MyClass::$variable, there might be no control over the declaration
		}

		if (!PhpCsUtil::isCamelCaps($varName, false, true, false)) {
			$error = 'Variable "%s" is not in valid camel caps format';
			$data = [$varName];
			$file->addError($error, $stackPointer, self::CODE_CAMEL_CAPS, $data);
		}
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processMemberVar(PhpCsFile $file, $stackPointer)
	{
		// handled by PSR2.Classes.PropertyDeclaration
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $stackPointer position of the double quoted string
	 */
	protected function processVariableInString(PhpCsFile $file, $stackPointer)
	{
		// Consistence standard does not allow variables in strings, handled by Squiz.Strings.DoubleQuoteUsage
	}

}
