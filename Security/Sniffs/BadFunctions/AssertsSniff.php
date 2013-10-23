<?php


class Security_Sniffs_BadFunctions_AssertsSniff implements PHP_CodeSniffer_Sniff {

	/**
	* Returns the token types that this sniff is interested in.
	*
	* @return array(int)
	*/
	public function register() {
		return array(T_STRING);
	}

	/**
	* Framework or CMS used. Must be a class under Security_Sniffs.
	*
	* @var String
	*/
	public $CmsFramework = NULL;

	/**
	* Processes the tokens that this sniff is interested in.
	*
	* @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
	* @param int                  $stackPtr  The position in the stack where
	*                                        the token was found.
	*
	* @return void
	*/
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$utils = Security_Sniffs_UtilsFactory::getInstance($this->CmsFramework);

		if ($tokens[$stackPtr]['content'] == 'assert') {
            $opener = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr, null, false, null, true);
			$closer = $tokens[$opener]['parenthesis_closer'];
            $s = $stackPtr + 1;
			$s = $phpcsFile->findNext(array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, PHP_CodeSniffer_Tokens::$bracketTokens, Security_Sniffs_Utils::$staticTokens, array(T_STRING_CONCAT)), $s, $closer, true);
             if ($s) {
				$msg = 'Assert eval function ' . $tokens[$stackPtr]['content'] . '() detected with dynamic parameter';
				if ($utils::is_token_user_input($tokens[$s])) {
					$phpcsFile->addError($msg . ' directly from user input', $stackPtr, 'ErrFunctionHandling');
				} else {
					$phpcsFile->addWarning($msg, $stackPtr, 'WarnFunctionHandling');
				}
			}
		}

	}

}

?>
